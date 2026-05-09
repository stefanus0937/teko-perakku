@extends('layouts.admin_premium')

@section('title', 'Edit Usaha')

@section('css')
<style>
    .form-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 40px;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #4b5563;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
        background: #fcfcfc;
    }

    .form-input:focus {
        border-color: #991b1b;
        background: #fff;
    }

    .form-input[readonly] {
        background: #f3f4f6;
        cursor: not-allowed;
    }

    .social-input-group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .social-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 16px;
        flex-shrink: 0;
    }

    .photo-preview-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f9fafb;
        position: relative;
    }

    .photo-preview-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-upload {
        display: block;
        width: fit-content;
        margin: 0 auto;
        padding: 8px 16px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-upload:hover {
        background: #f9fafb;
    }

    .gallery-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 12px;
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
        border: 2px dashed #e5e7eb;
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
        top: 5px;
        right: 5px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .remove-btn:hover { background: #dc2626; }

    .action-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-submit {
        background: #991b1b;
        color: #fff;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel {
        background: #fff;
        color: #4b5563;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-delete {
        background: #fff;
        color: #dc2626;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: 1px solid #fee2e2;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover { opacity: 0.9; }
    .btn-cancel:hover { background: #f9fafb; }
    .btn-delete:hover { background: #fee2e2; }
</style>
@stop

@section('content')
<h2 style="font-size: 18px; font-weight: 700; margin-bottom: 30px;">Edit Usaha</h2>

@if($errors->any())
    <div style="background: #fee2e2; color: #dc2626; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.usaha-update', $usaha->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-container">
        <!-- Left Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Kode Usaha</label>
                <input type="text" name="kode_usaha" class="form-input" value="{{ $usaha->kode_usaha }}" readonly>
            </div>
            <div class="form-group">
                <label>Nama Usaha</label>
                <input type="text" name="nama_usaha" class="form-input" value="{{ $usaha->nama_usaha }}" required>
            </div>
            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="telp_usaha" class="form-input" value="{{ $usaha->telp_usaha }}" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-input" value="{{ $usaha->user->username ?? '' }}" readonly>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input" value="{{ $usaha->user->email ?? '' }}" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input" placeholder="******** (Kosongkan jika tidak diubah)">
            </div>
            <div class="form-group">
                <label>Pemilik (Pengrajin)</label>
                <select name="pengerajin_id[]" class="form-input" multiple style="height: 100px;">
                    @foreach($pengerajins as $p)
                        <option value="{{ $p->id }}" {{ $usaha->pengerajins->contains($p->id) ? 'selected' : '' }}>{{ $p->nama_pengerajin }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Tahan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu pengrajin.</small>
            </div>
            <div class="form-group">
                <label>Wilayah</label>
                <select name="wilayah_id" class="form-input" required>
                    <option value="">Pilih Wilayah</option>
                    @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}" {{ $usaha->wilayah_id == $w->id ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Middle Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-input" rows="4">{{ $usaha->user->alamat ?? '' }}</textarea>
            </div>
            
            <div class="social-input-group">
                <div class="social-icon"><i class="fas fa-link"></i></div>
                <input type="text" name="link_website_usaha" class="form-input" value="{{ $usaha->link_website_usaha }}" placeholder="https://toko-yanto.com">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fas fa-map-marker-alt"></i></div>
                <input type="text" name="link_gmap_usaha" class="form-input" value="{{ $usaha->link_gmap_usaha }}" placeholder="Link Google Maps">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fab fa-whatsapp"></i></div>
                <input type="text" name="link_wa_usaha" class="form-input" value="{{ $usaha->link_wa_usaha }}" placeholder="wa.me/nomor-wa">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fab fa-instagram"></i></div>
                <input type="text" name="link_instagram_usaha" class="form-input" value="{{ $usaha->link_instagram_usaha }}" placeholder="instagram.com/username">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fab fa-facebook"></i></div>
                <input type="text" name="link_facebook_usaha" class="form-input" value="{{ $usaha->link_facebook_usaha }}" placeholder="facebook.com/username">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fab fa-tiktok"></i></div>
                <input type="text" name="link_tiktok_usaha" class="form-input" value="{{ $usaha->link_tiktok_usaha }}" placeholder="tiktok.com/@username">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fas fa-shopping-bag"></i></div>
                <input type="text" name="link_shopee_usaha" class="form-input" value="{{ $usaha->link_shopee_usaha }}" placeholder="shopee.co.id/username">
            </div>
            <div class="social-input-group">
                <div class="social-icon"><i class="fas fa-store"></i></div>
                <input type="text" name="link_tokopedia_usaha" class="form-input" value="{{ $usaha->link_tokopedia_usaha }}" placeholder="tokopedia.com/username">
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label>Spesialisasi Usaha</label>
                <textarea name="spesialisasi_usaha" class="form-input" rows="3" placeholder="Kerajinan Perak, Kuningan, dsb">{{ $usaha->spesialisasi_usaha }}</textarea>
            </div>
        </div>

        <!-- Right Column -->
        <div class="form-column">
            <div class="photo-preview-circle" id="profile-preview">
                @if($usaha->foto_usaha)
                    <img src="{{ asset('storage/' . $usaha->foto_usaha) }}" alt="Profile">
                @else
                    <i class="fas fa-user" style="font-size: 60px; color: #e5e7eb;"></i>
                @endif
            </div>
            <input type="file" name="foto_usaha" id="foto_usaha" style="display: none;" accept="image/*">
            <button type="button" class="btn-upload" onclick="document.getElementById('foto_usaha').click()">Ubah Foto</button>

            <div class="form-group" style="margin-top: 40px;">
                <label>Deskripsi</label>
                <textarea name="deskripsi_usaha" class="form-input" rows="6">{{ $usaha->deskripsi_usaha }}</textarea>
            </div>

            <div class="form-group">
                <label>Foto Tempat (Max 3)</label>
                <div class="gallery-container">
                    @php
                        $gallery = $usaha->foto_tempat ?? [];
                    @endphp
                    @for($i = 0; $i < 3; $i++)
                        <div class="gallery-item" id="gallery-preview-{{ $i }}">
                            @if(isset($gallery[$i]))
                                <img src="{{ asset('storage/' . $gallery[$i]) }}" style="width:100%; height:100%; object-fit:cover;">
                                <button type="button" class="remove-btn" onclick="removeExistingImg({{ $i }}, '{{ $gallery[$i] }}')"><i class="fas fa-times"></i></button>
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
            </div>
        </div>
    </div>

    <div class="action-footer">
        @if(auth()->user()->role !== 'umkm')
            <button type="button" class="btn-delete" onclick="if(confirm('Yakin ingin menghapus usaha ini?')) document.getElementById('delete-form').submit()">Hapus Usaha</button>
        @else
            <div></div>
        @endif
        <div style="display: flex; gap: 12px;">
            <a href="{{ auth()->user()->role === 'umkm' ? route('umkm.profile') : route('admin.usaha-index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </div>
</form>

<form id="delete-form" action="{{ route('admin.usaha-destroy', $usaha->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@stop

@section('js')
<script>
    // Profile photo preview
    document.getElementById('foto_usaha').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('profile-preview');
            preview.innerHTML = `<img src="${URL.createObjectURL(file)}" alt="Preview">`;
        }
    };

    // Multiple gallery preview logic for 3 slots
    function previewGallery(input, slotIndex) {
        const preview = document.getElementById(`gallery-preview-${slotIndex}`);
        const [file] = input.files;
        if (file) {
            // Remove existing hidden input if any (we are replacing it with a new upload)
            const existingInput = document.getElementById(`existing-input-${slotIndex}`);
            if (existingInput) existingInput.remove();

            preview.innerHTML = `<img src="${URL.createObjectURL(file)}" style="width:100%; height:100%; object-fit:cover;">
                                 <button type="button" class="remove-btn" onclick="clearSlot(${slotIndex})"><i class="fas fa-times"></i></button>`;
        }
    }

    function clearSlot(slotIndex) {
        const input = document.getElementById(`gallery-input-${slotIndex}`);
        const preview = document.getElementById(`gallery-preview-${slotIndex}`);
        input.value = "";
        preview.innerHTML = `<label class="btn-add-gallery" for="gallery-input-${slotIndex}"><i class="fas fa-plus"></i></label>`;
    }

    function removeExistingImg(slotIndex, path) {
        if (confirm('Hapus foto ini?')) {
            clearSlot(slotIndex);
            // We don't need to do anything else, the hidden input was removed by clearSlot or replaced
        }
    }
</script>
@stop
