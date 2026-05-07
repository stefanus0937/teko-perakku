<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $chatUsers = $this->getChatUsers($user);

        if (request()->ajax()) {
            return response()->json($chatUsers);
        }

        $layout = 'layouts.admin_premium';
        if ($user->role == 'umkm') $layout = 'layouts.umkm';
        if ($user->role == 'user') $layout = 'layouts.user';

        return view('chats.index_new', compact('chatUsers', 'layout'));
    }

    public function show(User $user)
    {
        $usahaId = request('usaha_id');
        $productId = request('product_id');
        $prefillMessage = '';
        $product = null;

        if ($productId) {
            $product = \App\Models\Produk::with('fotoProduk')->find($productId);
            if ($product) {
                $url = route('guest-singleProduct', $product->slug);
                $prefillMessage = "Halo, saya tertarik dengan produk " . $product->nama_produk . ".\n\n" . $url . "\n\nApakah produk ini masih tersedia?";
            }
        }

        $user->load('usaha');
        $currentUser = Auth::user();

        // Mark messages as read immediately when opening the room
        $readQuery = Chat::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false);
        
        if ($usahaId) {
            $readQuery->where('usaha_id', $usahaId);
        } else {
            $readQuery->whereNull('usaha_id');
        }
        $readQuery->update(['is_read' => true, 'is_delivered' => true]);
        
        // Pass usahaId to getChatUsers to ensure the sidebar reflects the current shop
        $chatUsers = $this->getChatUsers($currentUser, $user->id, $usahaId);
        
        $messages = Chat::where(function($q) use ($currentUser, $user, $usahaId) {
            $q->where('sender_id', $currentUser->id)
              ->where('receiver_id', $user->id)
              ->where('usaha_id', $usahaId);
        })->orWhere(function($q) use ($currentUser, $user, $usahaId) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', $currentUser->id)
              ->where('usaha_id', $usahaId);
        })->orderBy('created_at', 'asc')->with('product.fotoProduk')->get();

        // Note: Read status is now handled via AJAX in the frontend to avoid pre-fetching issues.

        if (request()->ajax()) {
            return response()->json([
                'user' => $user,
                'messages' => $messages
            ]);
        }

        $layout = 'layouts.admin_premium';
        if ($currentUser->role == 'umkm') $layout = 'layouts.umkm';
        if ($currentUser->role == 'user') $layout = 'layouts.user';

        // Custom display name if usaha_id is provided
        $usaha = $usahaId ? \App\Models\Usaha::find($usahaId) : null;
        if ($usaha) {
            if (Auth::id() == $usaha->user_id) {
                // Saya (Auth) adalah pemilik usaha, maka lawan bicara adalah pembeli
                $user->display_name = $user->nama ?: $user->username;
            } else {
                // Saya (Auth) adalah pembeli, maka lawan bicara tampil sebagai Nama Usaha
                $user->display_name = $usaha->nama_usaha;
            }
            $user->specific_usaha = $usaha;
        } else {
            // Jika chat partner adalah UMKM tapi tanpa context usaha tertentu (fallback)
            if ($user->role === 'umkm' && $user->usaha) {
                $user->display_name = $user->usaha->nama_usaha;
            } else {
                $user->display_name = $user->nama ?: $user->username;
            }
        }
        


        return view('chats.show_new', compact('user', 'messages', 'chatUsers', 'layout', 'usahaId', 'prefillMessage', 'product'));
    }

    public function store(Request $request)
    {
        \Log::info('Chat Store Request:', $request->all());

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'usaha_id' => 'nullable|exists:usaha,id',
            'product_id' => 'nullable|exists:produk,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,gif,pdf,docx,doc,xls,xlsx,ppt,pptx,txt,zip',
            'reply_to_id' => 'nullable|exists:chats,id',
        ]);

        $type = 'text';
        $attachmentPath = null;
        $messageContent = $request->input('message');

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->getClientOriginalExtension());
            $type = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'file';
            $attachmentPath = $file->store('chat_attachments', 'public');
        }

        // Security Check: If sender is UMKM, ensure usaha_id belongs to them.
        // If sender is User, ensure usaha_id belongs to the receiver.
        $currentUser = Auth::user();
        $receiver = User::find($request->receiver_id);
        $finalUsahaId = $request->usaha_id;

        if ($currentUser->role === 'umkm') {
            // UMKM is sending. usaha_id must be their own usaha.
            if ($currentUser->usaha) {
                $finalUsahaId = $currentUser->usaha->id;
            }
        } else {
            // User is sending. usaha_id must be the receiver's usaha.
            if ($receiver->role === 'umkm' && $receiver->usaha) {
                $finalUsahaId = $receiver->usaha->id;
            }
        }

        $chat = Chat::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $request->receiver_id,
            'usaha_id' => $finalUsahaId,
            'product_id' => $request->product_id,
            'message' => $messageContent,
            'type' => $type,
            'attachment' => $attachmentPath,
            'reply_to_id' => $request->reply_to_id,
        ]);

        \Log::info('Chat Created:', $chat->toArray());

        $chat->load(['replyTo', 'usaha', 'product.fotoProduk']);

        broadcast(new \App\Events\MessageSent($chat))->toOthers();

        if ($request->ajax()) {
            return response()->json($chat);
        }

        return back()->with('success', 'Pesan terkirim.');
    }

    private function getChatUsers($user, $activeChatUserId = null, $activeUsahaId = null)
    {
        // 1. Ambil semua kombinasi unik (partner_id, usaha_id) yang diikuti user ini
        $sent = Chat::where('sender_id', $user->id)
            ->select('receiver_id as partner_id', 'usaha_id')
            ->distinct();
            
        $received = Chat::where('receiver_id', $user->id)
            ->select('sender_id as partner_id', 'usaha_id')
            ->distinct();
            
        $combinations = $sent->union($received)->get();
        
        // 2. Jika ada chat aktif yang belum dimulai, tambahkan ke list
        if ($activeChatUserId) {
             $exists = $combinations->where('partner_id', $activeChatUserId)->where('usaha_id', $activeUsahaId)->first();
             if (!$exists) {
                 $combinations->push((object)['partner_id' => $activeChatUserId, 'usaha_id' => $activeUsahaId]);
             }
        }

        // 3. Petakan ke objek User dengan informasi tambahan
        return $combinations->map(function($combo) use ($user) {
            $partner = User::with('usaha')->find($combo->partner_id);
            if (!$partner) return null;
            
            // Clone user object to avoid sharing state
            $contact = clone $partner;
            $contact->active_usaha_id = $combo->usaha_id;
            
            // Ambil pesan terakhir untuk pasangan (partner, usaha) spesifik ini
            $lastChat = Chat::where('usaha_id', $combo->usaha_id)
                ->where(function($q) use ($user, $combo) {
                    $q->where(function($q1) use ($user, $combo) {
                        $q1->where('sender_id', $user->id)->where('receiver_id', $combo->partner_id);
                    })->orWhere(function($q1) use ($user, $combo) {
                        $q1->where('sender_id', $combo->partner_id)->where('receiver_id', $user->id);
                    });
                })->latest()->first();

            // Set nama tampilan secara dinamis
            $usaha = $combo->usaha_id ? \App\Models\Usaha::find($combo->usaha_id) : null;
            if ($usaha) {
                if ($user->id == $usaha->user_id) {
                    // Saya adalah penjual, maka lawan bicara adalah pembeli
                    $contact->display_name = $partner->nama ?: $partner->username;
                } else {
                    // Saya adalah pembeli, maka lawan bicara adalah Toko
                    $contact->display_name = $usaha->nama_usaha;
                }
            } else {
                // Chat tanpa usaha_id (mungkin antar admin atau user biasa)
                if ($partner->role === 'umkm' && $partner->usaha) {
                    $contact->display_name = $partner->usaha->nama_usaha;
                } else {
                    $contact->display_name = $partner->nama ?: $partner->username;
                }
            }

            $contact->last_message = $lastChat ? $lastChat->message : '';
            $contact->last_message_sender_id = $lastChat ? $lastChat->sender_id : null;
            $contact->last_message_is_read = $lastChat ? $lastChat->is_read : false;
            $contact->last_chat_time_raw = $lastChat ? $lastChat->created_at : null;
            $contact->last_chat_time = $lastChat ? $lastChat->created_at->format('H:i') : '';
            
            $contact->unread_count = Chat::where('sender_id', $partner->id)
                ->where('receiver_id', $user->id)
                ->where('usaha_id', $combo->usaha_id)
                ->where('is_read', false)
                ->count();
                
            return $contact;
        })->filter()->sortByDesc(function($u) {
            return $u->last_chat_time_raw;
        })->values();
    }

    public function update(Request $request, Chat $chat)
    {
        if ($chat->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            $chat->update([
                'message' => $request->message,
                'is_edited' => true,
            ]);

            $chat->load(['sender', 'receiver']);

            broadcast(new \App\Events\MessageUpdated($chat))->toOthers();

            return response()->json($chat);
        } catch (\Exception $e) {
            \Log::error("Chat Update Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function deleteForMe(Chat $chat)
    {
        $user = Auth::user();
        if ($chat->sender_id == $user->id) {
            $chat->update(['deleted_by_sender' => true]);
        } else if ($chat->receiver_id == $user->id) {
            $chat->update(['deleted_by_receiver' => true]);
        }

        broadcast(new \App\Events\MessageDeleted($chat->id, $chat->sender_id, $chat->receiver_id, 'me'));

        return response()->json(['success' => true]);
    }

    public function deleteForEveryone(Chat $chat)
    {
        $this->authorize('delete', $chat);

        $senderId = $chat->sender_id;
        $receiverId = $chat->receiver_id;
        $chatId = $chat->id;

        $chat->delete();

        broadcast(new \App\Events\MessageDeleted($chatId, $senderId, $receiverId, 'everyone'));

        return response()->json(['success' => true]);
    }

    public function markAsRead(User $user)
    {
        $currentUser = Auth::user();
        $usahaId = request('usaha_id');
        
        \Log::info("Chat: User {$currentUser->id} marking messages from {$user->id} for Usaha {$usahaId} as READ");
        
        $query = Chat::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false);

        if ($usahaId) {
            $query->where('usaha_id', $usahaId);
        } else {
            $query->whereNull('usaha_id');
        }

        $unreadMessages = $query->get();

        foreach ($unreadMessages as $msg) {
            $msg->update(['is_read' => true, 'is_delivered' => true]);
            broadcast(new \App\Events\MessageRead($msg))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }
    public function markAsDelivered(User $user)
    {
        $currentUser = Auth::user();
        \Log::info("Chat: User {$currentUser->id} marking messages from {$user->id} as DELIVERED");
        $undeliveredMessages = Chat::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_delivered', false)
            ->get();

        foreach ($undeliveredMessages as $msg) {
            $msg->update(['is_delivered' => true]);
            broadcast(new \App\Events\MessageDelivered($msg))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }

    public function markAllAsDelivered()
    {
        $currentUser = Auth::user();
        $undeliveredMessages = Chat::where('receiver_id', $currentUser->id)
            ->where('is_delivered', false)
            ->get();

        foreach ($undeliveredMessages as $msg) {
            $msg->update(['is_delivered' => true]);
            broadcast(new \App\Events\MessageDelivered($msg))->toOthers();
        }

        return response()->json(['status' => 'success']);
    }
}
