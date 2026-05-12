@extends($layout ?? 'layouts.user')

@section('title', 'Edit Profil')

@section('css')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .profile-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #000;
    }

    .edit-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        gap: 30px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border-radius: 12px;
        padding: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 14px;
        color: #111827;
        outline: none;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #991b1b;
        box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
    }

    .form-control[readonly] {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }

    /* Gallery Styles */
    .gallery-container {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 15px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .gallery-item {
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-add-gallery {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        color: #9ca3af;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add-gallery:hover {
        border-color: #991b1b;
        color: #991b1b;
    }

    .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    /* Social Links */
    .social-input-group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .social-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: #f9fafb;
        color: #4b5563;
        font-size: 14px;
        border: 1px solid #e5e7eb;
    }

    /* Profile Photo */
    .profile-photo-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
    }

    .profile-photo-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }

    .profile-photo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-upload-photo {
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    /* Specialization Tags */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        min-height: 45px;
    }

    .tag {
        background: #e5e7eb;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tag-close {
        cursor: pointer;
        color: #9ca3af;
    }

    /* Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
    }

    .btn-save {
        background: #991b1b;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        flex: 1;
    }

    .btn-cancel {
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        flex: 1;
    }

    @media (max-width: 1024px) {
        .edit-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <h1 class="profile-title">Edit Profil</h1>

    @if($errors->any())
        <div style="background: #fee2e2; color: #dc2626; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="edit-grid">
            <!-- Left Column: User/Business Basic Info -->
            <div class="form-card">
                <div class="form-group">
                    <label>Nama Usaha</label>
                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" placeholder="Nama Usaha">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div id="password-display-section">
                        <input type="text" class="form-control" value="**********" readonly>
                        <small><a href="javascript:void(0)" onclick="togglePasswordFields()" style="color: #dc2626; text-decoration: none;">ubah password</a></small>
                    </div>
                    <div id="password-edit-section" style="display: none;">
                        <input type="password" name="password" class="form-control mb-2" placeholder="Password Baru (Kosongkan jika tidak diubah)" autocomplete="new-password">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password Baru" autocomplete="new-password">
                        <small><a href="javascript:void(0)" onclick="togglePasswordFields()" style="color: #6b7280; text-decoration: none;">batal ubah</a></small>
                    </div>
                </div>

                @if($user->role == 'umkm')
                <div class="form-group">
                    <label>Spesialisasi Usaha</label>
                    <input type="text" name="spesialisasi_usaha" class="form-control" value="{{ $user->usaha->spesialisasi_usaha ?? '' }}" placeholder="Contoh: Tatah, Emas, Perak">
                </div>
                @else
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}">
                </div>
                @endif
            </div>

            <!-- Middle Column: Gallery & Socials -->
            <div class="form-card">
                @if($user->role == 'umkm')
                <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 8px;">Foto Tempat</label>
                <div class="gallery-container">
                    @php $gallery = $user->usaha->foto_tempat ?? []; @endphp
                    @for($i = 0; $i < 3; $i++)
                        <div class="gallery-item" id="gallery-preview-{{ $i }}">
                            @if(isset($gallery[$i]))
                                <img src="{{ asset('storage/' . $gallery[$i]) }}">
                                <button type="button" class="remove-btn" onclick="clearSlot({{ $i }})"><i class="fas fa-times"></i></button>
                                <input type="hidden" name="existing_foto_tempat[{{ $i }}]" value="{{ $gallery[$i] }}" id="existing-input-{{ $i }}">
                            @else
                                <label class="btn-add-gallery" for="gallery-input-{{ $i }}">
                                    <i class="fas fa-plus"></i>
                                </label>
                            @endif
                        </div>
                        <input type="file" name="foto_tempat[{{ $i }}]" id="gallery-input-{{ $i }}" style="display: none;" accept="image/*" onchange="previewGallery(this, {{ $i }})">
                    @endfor
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="4" placeholder="Alamat Lengkap">{{ $user->alamat }}</textarea>
                </div>

                <div class="social-links">
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fas fa-link"></i></div>
                        <input type="text" name="link_website_usaha" class="form-control" value="{{ $user->usaha->link_website_usaha ?? '' }}" placeholder="Website">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <input type="text" name="link_gmap_usaha" class="form-control" value="{{ $user->usaha->link_gmap_usaha ?? '' }}" placeholder="Google Maps">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fab fa-whatsapp"></i></div>
                        <input type="text" name="link_wa_usaha" class="form-control" value="{{ $user->usaha->link_wa_usaha ?? '' }}" placeholder="WhatsApp">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fab fa-instagram"></i></div>
                        <input type="text" name="link_instagram_usaha" class="form-control" value="{{ $user->usaha->link_instagram_usaha ?? '' }}" placeholder="Instagram">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fab fa-facebook"></i></div>
                        <input type="text" name="link_facebook_usaha" class="form-control" value="{{ $user->usaha->link_facebook_usaha ?? '' }}" placeholder="Facebook">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fab fa-tiktok"></i></div>
                        <input type="text" name="link_tiktok_usaha" class="form-control" value="{{ $user->usaha->link_tiktok_usaha ?? '' }}" placeholder="TikTok">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fas fa-shopping-bag"></i></div>
                        <input type="text" name="link_shopee_usaha" class="form-control" value="{{ $user->usaha->link_shopee_usaha ?? '' }}" placeholder="Shopee">
                    </div>
                    <div class="social-input-group">
                        <div class="social-icon"><i class="fas fa-store"></i></div>
                        <input type="text" name="link_tokopedia_usaha" class="form-control" value="{{ $user->usaha->link_tokopedia_usaha ?? '' }}" placeholder="Tokopedia">
                    </div>
                </div>

                {{-- Lokasi Usaha (Leaflet picker + manual lat/lng — sinkron dua arah) --}}
                @include('admin.usaha._location-picker', [
                    'lat' => $user->usaha->latitude  ?? null,
                    'lng' => $user->usaha->longitude ?? null,
                ])
                @else
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="4" placeholder="Alamat Lengkap">{{ $user->alamat }}</textarea>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="gender" class="form-control">
                        <option value="Pria" {{ $user->gender == 'Pria' ? 'selected' : '' }}>Pria</option>
                        <option value="Wanita" {{ $user->gender == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Usia</label>
                    <input type="number" name="usia" class="form-control" value="{{ $user->usia }}">
                </div>
                @endif
            </div>

            <!-- Right Column: Photo & Description -->
            <div class="form-card">
                <div class="profile-photo-section">
                    <div class="profile-photo-circle" id="profile-preview">
                        <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}" alt="Profile">
                    </div>
                    <input type="file" name="foto" id="foto-input" style="display: none;" accept="image/*">
                    <button type="button" class="btn-upload-photo" onclick="document.getElementById('foto-input').click()">Ubah Foto</button>
                </div>

                @if($user->role == 'umkm')
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi_usaha" class="form-control" rows="8" placeholder="Deskripsi Usaha">{{ $user->usaha->deskripsi_usaha ?? '' }}</textarea>
                </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                    <a href="{{ route('profile') }}" class="btn-cancel">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    // Profile photo preview
    document.getElementById('foto-input').onchange = function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#profile-preview img').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    };

    // Gallery preview logic
    function previewGallery(input, slotIndex) {
        const preview = document.getElementById(`gallery-preview-${slotIndex}`);
        const [file] = input.files;
        if (file) {
            const existingInput = document.getElementById(`existing-input-${slotIndex}`);
            if (existingInput) existingInput.remove();

            preview.innerHTML = `<img src="${URL.createObjectURL(file)}">
                                 <button type="button" class="remove-btn" onclick="clearSlot(${slotIndex})"><i class="fas fa-times"></i></button>`;
        }
    }

    function clearSlot(slotIndex) {
        const input = document.getElementById(`gallery-input-${slotIndex}`);
        const preview = document.getElementById(`gallery-preview-${slotIndex}`);
        input.value = "";
        preview.innerHTML = `<label class="btn-add-gallery" for="gallery-input-${slotIndex}"><i class="fas fa-plus"></i></label>`;
        
        const existingInput = document.getElementById(`existing-input-${slotIndex}`);
        if (existingInput) existingInput.remove();
    }

    function togglePasswordFields() {
        const display = document.getElementById('password-display-section');
        const edit = document.getElementById('password-edit-section');
        if (edit.style.display === 'none') {
            edit.style.display = 'block';
            display.style.display = 'none';
        } else {
            edit.style.display = 'none';
            display.style.display = 'block';
        }
    }
</script>
@endsection
