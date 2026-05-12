<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usaha;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\Pengerajin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsahaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Usaha::with(['wilayah', 'user']);

        if ($user->role === 'admin_wilayah') {
            $query->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->role === 'umkm') {
            $query->where('user_id', $user->id);
        }

        $dataUsaha = $query->get();

        return view('admin.usaha.index-usaha', [
            'usahas' => $dataUsaha
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'admin_wilayah') {
            $wilayahs = Wilayah::where('id', $user->wilayah_id)->get();
        } else {
            $wilayahs = Wilayah::all();
        }
        $pengerajins = Pengerajin::all();
        
        $lastUsaha = Usaha::orderBy('id', 'desc')->first();
        $nextId = $lastUsaha ? $lastUsaha->id + 1 : 1;
        $autoKode = 'PR' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.usaha.create-usaha', compact('wilayahs', 'pengerajins', 'autoKode'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $usaha = Usaha::with('user')->findOrFail($id);
        
        if ($user->role === 'umkm') {
            if ($usaha->user_id != $user->id) {
                abort(403, 'Unauthorized action.');
            }
            $wilayahs = Wilayah::where('id', $usaha->wilayah_id)->get();
        } elseif ($user->role === 'admin_wilayah') {
            if ($usaha->wilayah_id != $user->wilayah_id) {
                abort(403, 'Unauthorized action.');
            }
            $wilayahs = Wilayah::where('id', $user->wilayah_id)->get();
        } else {
            $wilayahs = Wilayah::all();
        }
        
        $pengerajins = Pengerajin::all();
        return view('admin.usaha.edit-usaha', compact('usaha', 'wilayahs', 'pengerajins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_usaha' => 'required|string|max:255|unique:usaha',
            'nama_usaha' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'telp_usaha' => 'nullable|string|max:15',
            'deskripsi_usaha' => 'nullable|string',
            'spesialisasi_usaha' => 'nullable|string',
            'foto_usaha' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'foto_tempat' => 'nullable|array|max:3',
            'foto_tempat.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_gmap_usaha' => 'nullable|string|max:2048',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // 1. Authorization check for admin_wilayah
        $authUser = Auth::user();
        if ($authUser->role === 'admin_wilayah' && $request->wilayah_id != $authUser->wilayah_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menambahkan usaha di wilayah Anda sendiri.');
        }

        // 2. Create User
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'umkm',
            'wilayah_id' => $request->wilayah_id,
            'nama' => $request->nama_usaha,
            'alamat' => $request->alamat,
        ]);

        $data = $request->except(['password', 'username', 'email', 'pengerajin_id', 'foto_tempat']);
        $data['user_id'] = $user->id;
        $data['email_usaha'] = $request->email;
        $data['status_usaha'] = 'aktif';

        // Lokasi: koordinat di-pick lewat Leaflet picker di form (lat+lng selalu
        // datang bersamaan, atau dua-duanya kosong kalau user belum pilih).
        [$data['latitude'], $data['longitude']] = $this->resolveCoordinates(
            $request->input('latitude'),
            $request->input('longitude')
        );

        // 2. Handle Profile Photo
        if ($request->hasFile('foto_usaha')) {
            $path = $request->file('foto_usaha')->store('foto_usaha', 'public');
            $data['foto_usaha'] = $path;
        }

        // 3. Handle Gallery Photos (foto_tempat)
        if ($request->hasFile('foto_tempat')) {
            $gallery = [];
            foreach ($request->file('foto_tempat') as $file) {
                if ($file) { // Check if slot has a file
                    $path = $file->store('foto_tempat', 'public');
                    $gallery[] = $path;
                }
            }
            $data['foto_tempat'] = $gallery;
        }

        $usaha = Usaha::create($data);

        // 4. Link Pengerajin if selected
        if ($request->filled('pengerajin_id')) {
            $usaha->pengerajins()->attach($request->pengerajin_id);
        }

        return redirect()->route('admin.usaha-index')
            ->with('success', 'Usaha berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $usaha = Usaha::findOrFail($id);
        $user = $usaha->user;

        $request->validate([
            'kode_usaha' => 'required|string|max:255|unique:usaha,kode_usaha,' . $id,
            'nama_usaha' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . ($user->id ?? 0),
            'email' => 'required|email|max:255|unique:users,email,' . ($user->id ?? 0),
            'password' => 'nullable|string|min:8',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'telp_usaha' => 'nullable|string|max:15',
            'deskripsi_usaha' => 'nullable|string',
            'spesialisasi_usaha' => 'nullable|string',
            'foto_usaha' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'foto_tempat' => 'nullable|array|max:3',
            'foto_tempat.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_gmap_usaha' => 'nullable|string|max:2048',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // 1. Update User
        if ($user) {
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'wilayah_id' => $request->wilayah_id,
                'nama' => $request->nama_usaha,
                'alamat' => $request->alamat,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);
        }

        $data = $request->except(['password', 'username', 'email', 'pengerajin_id', 'foto_tempat']);
        $data['email_usaha'] = $request->email;

        // Lokasi: ambil dari Leaflet picker (lat+lng atau dua-duanya kosong).
        [$data['latitude'], $data['longitude']] = $this->resolveCoordinates(
            $request->input('latitude'),
            $request->input('longitude')
        );

        // 2. Handle Profile Photo
        if ($request->hasFile('foto_usaha')) {
            if ($usaha->foto_usaha) {
                Storage::disk('public')->delete($usaha->foto_usaha);
            }
            $path = $request->file('foto_usaha')->store('foto_usaha', 'public');
            $data['foto_usaha'] = $path;
        }

        // 3. Handle Gallery Photos (Max 3 slots)
        $existingGallery = $request->input('existing_foto_tempat', []);
        $newGallery = [];
        
        // Fill slots
        for ($i = 0; $i < 3; $i++) {
            if ($request->hasFile("foto_tempat.$i")) {
                // If a new file is uploaded for this slot, store it
                $path = $request->file("foto_tempat.$i")->store('foto_tempat', 'public');
                $newGallery[] = $path;
            } elseif (isset($existingGallery[$i])) {
                // If no new file, but an existing photo is kept for this slot
                $newGallery[] = $existingGallery[$i];
            }
        }

        // Cleanup: find photos that were in the old gallery but are NOT in the new gallery
        $oldGallery = $usaha->foto_tempat ?? [];
        foreach ($oldGallery as $oldPath) {
            if (!in_array($oldPath, $newGallery)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        $data['foto_tempat'] = $newGallery;

        $usaha->update($data);

        // 4. Update Pengerajin relationship
        if ($request->has('pengerajin_id')) {
            $usaha->pengerajins()->sync($request->pengerajin_id);
        }

        if (Auth::user()->role === 'umkm') {
            return redirect()->route('umkm.profile')
                ->with('success', 'Informasi Usaha berhasil diperbarui.');
        }

        return redirect()->route('admin.usaha-index')
            ->with('success', 'Usaha berhasil diperbarui.');
    }

    /**
     * Lat + lng dari Leaflet picker. Datang bersamaan atau dua-duanya kosong;
     * kalau salah satu invalid, anggap belum dipilih (null/null) → halaman
     * detail-usaha akan fallback ke placeholder.
     */
    private function resolveCoordinates($lat, $lng): array
    {
        if (is_numeric($lat) && is_numeric($lng)) {
            return [(float) $lat, (float) $lng];
        }
        return [null, null];
    }

    public function destroy($id)
    {
        $usaha = Usaha::findOrFail($id);
        if ($usaha->foto_usaha) {
            Storage::disk('public')->delete($usaha->foto_usaha);
        }
        $gallery = $usaha->foto_tempat ?? [];
        foreach ($gallery as $path) {
            Storage::disk('public')->delete($path);
        }
        
        $user = $usaha->user;
        $usaha->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.usaha-index')
            ->with('success', 'Usaha berhasil dihapus.');
    }
}
