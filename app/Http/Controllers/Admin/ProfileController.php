<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role == 'user' || $user->role == 'umkm') {
            if ($user->role == 'umkm') {
                $user->load('usaha');
            }
            return view('user.profile', compact('user'))->with('layout', $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.user');
        }

        return view('admin.profile.profile', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        
        if ($user->role == 'umkm' || $user->role == 'user') {
            return view('user.edit-profile', compact('user'))->with('layout', $user->role == 'umkm' ? 'layouts.umkm' : 'layouts.user');
        }

        return view('admin.profile.edit-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $rules = [
            'nama' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Pria,Wanita',
            'usia' => 'nullable|integer',
            'alamat' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        if ($user->role == 'umkm') {
            $rules = array_merge($rules, [
                'deskripsi_usaha' => 'nullable|string',
                'spesialisasi_usaha' => 'nullable|string',
                'foto_tempat' => 'nullable|array|max:3',
                'foto_tempat.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'link_website_usaha' => 'nullable|string',
                'link_gmap_usaha' => 'nullable|string',
                'link_wa_usaha' => 'nullable|string',
                'link_instagram_usaha' => 'nullable|string',
                'link_facebook_usaha' => 'nullable|string',
                'link_tiktok_usaha' => 'nullable|string',
                'link_shopee_usaha' => 'nullable|string',
                'link_tokopedia_usaha' => 'nullable|string',
                'latitude'  => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
        }

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $userData = $request->only(['nama', 'no_hp', 'gender', 'usia', 'alamat', 'email']);
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                \Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $userData['foto'] = $path;
        }

        $user->update($userData);

        if ($user->role == 'umkm') {
            $usaha = $user->usaha;
            if ($usaha) {
                $usahaData = $request->only([
                    'deskripsi_usaha', 'spesialisasi_usaha',
                    'link_website_usaha', 'link_gmap_usaha', 'link_wa_usaha',
                    'link_instagram_usaha', 'link_facebook_usaha', 'link_tiktok_usaha',
                    'link_shopee_usaha', 'link_tokopedia_usaha'
                ]);

                // Sync name
                $usahaData['nama_usaha'] = $request->nama;

                // Lokasi dari Leaflet picker. Lat+lng datang bersamaan atau
                // dua-duanya kosong (kalau user pencet "Hapus Lokasi").
                $lat = $request->input('latitude');
                $lng = $request->input('longitude');
                $usahaData['latitude']  = is_numeric($lat) ? (float) $lat : null;
                $usahaData['longitude'] = is_numeric($lng) ? (float) $lng : null;

                // Handle Gallery
                $existingGallery = $request->input('existing_foto_tempat', []);
                $newGallery = [];
                for ($i = 0; $i < 3; $i++) {
                    if ($request->hasFile("foto_tempat.$i")) {
                        $path = $request->file("foto_tempat.$i")->store('foto_tempat', 'public');
                        $newGallery[] = $path;
                    } elseif (isset($existingGallery[$i])) {
                        $newGallery[] = $existingGallery[$i];
                    }
                }

                // Cleanup deleted gallery files
                $oldGallery = $usaha->foto_tempat ?? [];
                foreach ($oldGallery as $oldPath) {
                    if (!in_array($oldPath, $newGallery)) {
                        \Storage::disk('public')->delete($oldPath);
                    }
                }
                $usahaData['foto_tempat'] = $newGallery;

                $usaha->update($usahaData);
            }
        }

        $route = 'admin.profile';
        if ($user->role == 'umkm') $route = 'umkm.profile';
        if ($user->role == 'user') $route = 'user.profile';

        return redirect()->route($route)
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
