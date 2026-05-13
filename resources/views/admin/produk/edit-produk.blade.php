@extends($layout ?? 'layouts.admin_premium')

@section('title', 'Edit Produk')

@section('css')
<style>
    .form-container {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
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

    .gallery-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 12px;
    }

    .gallery-item {
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        background: #f9fafb;
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
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        color: #9ca3af;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0;
    }

    .btn-add-gallery i {
        font-size: 24px;
        margin: 0;
        padding: 0;
    }

    .btn-add-gallery:hover {
        border-color: #991b1b;
        color: #991b1b;
    }

    .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        border: 2px solid white;
        z-index: 10;
        transition: all 0.2s;
    }

    .remove-image:hover {
        transform: scale(1.1);
        background: #dc2626;
    }

    .video-upload-box {
        width: 100%;
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.25s ease;
        display: block;
        overflow: hidden;
    }

    .video-upload-box:hover {
        border-color: #991b1b;
        background: #fff7f7;
    }

    .video-upload-content {
        padding: 10px 10px;
        text-align: center;
    }

    .video-upload-content i {
        font-size: 24px;
        color: #991b1b;
        margin-bottom: 14px;
    }

    .video-upload-content h5 {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
    }

    .video-upload-content p {
        margin: 8px 0 4px;
        font-size: 10px;
        color: #6b7280;
    }

    .video-upload-content span {
        font-size: 12px;
        color: #9ca3af;
    }

    .video-preview-container {
        margin-top: 16px;
        border-radius: 14px;
        overflow: hidden;
        background: #000;
        border: 1px solid #e5e7eb;
        position: relative;
        display: none;
    }

    .video-preview-container video {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        display: block;
    }

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

    /* Tag/Category Style */
    .category-tag {
        display: inline-flex;
        align-items: center;
        background: #f3f4f6;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        color: #374151;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    /* Select2 Overrides */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 4px 8px;
        background: #fcfcfc;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #991b1b;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #991b1b;
        border: none;
        color: #fff;
        border-radius: 4px;
        padding: 2px 8px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@stop

@section('content')
<h2 style="font-size: 18px; font-weight: 700; margin-bottom: 30px;">Edit Produk</h2>

<form action="{{ route('admin.produk-update', $produk->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-container">
        <!-- Left Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Kode Produk</label>
                <input type="text" name="kode_produk" class="form-input" value="{{ $produk->kode_produk }}" readonly>
            </div>
            <div class="form-group">
                <label class="label-required">Nama Produk</label>
                <input type="text" name="nama_produk" class="form-input" value="{{ $produk->nama_produk }}" required>
            </div>
            <div class="form-group">
                <label class="label-required">Harga</label>
                <input type="number" name="harga" class="form-input" value="{{ $produk->harga }}" required>
            </div>
            <div class="form-group">
                <label class="form-label label-required">Kategori Produk</label>
                @php
                    $selectedKategories = $produk->kategoriProduk->pluck('id')->toArray();
                @endphp
                <select name="kategori_produk_id[]" id="kategoriSelect" multiple required>
                    @foreach($kategoriProduks as $kat)
                        <option value="{{ $kat->id }}" {{ in_array($kat->id, $selectedKategories) ? 'selected' : '' }}>{{ $kat->nama_kategori_produk }}</option>
                    @endforeach
                </select>
            </div>
            @if(count($usahas) > 1)
            <div class="form-group">
                <label class="label-required">Pemilik (Usaha)</label>
                <select name="usaha_id" class="form-input" required>
                    <option value="">Pilih Usaha</option>
                    @foreach($usahas as $u)
                        <option value="{{ $u->id }}" {{ $produk->usaha->first() && $produk->usaha->first()->id == $u->id ? 'selected' : '' }}>{{ $u->nama_usaha }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="usaha_id" value="{{ $usahas->first()->id }}">
            @endif
            <!-- Hidden stok field as it's not in UI but required in controller -->
            <input type="hidden" name="stok" value="{{ $produk->stok }}">
        </div>

        <!-- Right Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Foto Produk</label>
                <div class="gallery-container" id="gallery-preview">
                    @foreach($produk->fotoProduk as $foto)
                        <div class="gallery-item" id="photo-{{ $foto->id }}">
                            <img src="{{ asset('storage/' . $foto->file_foto_produk) }}">
                            <div class="remove-image" onclick="deleteExistingPhoto({{ $foto->id }})" title="Hapus Foto">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    @endforeach
                    <div class="gallery-item" id="add-photo-btn">
                        <label class="btn-add-gallery">
                            <i class="fas fa-plus"></i>
                            <input type="file" name="foto_produk[]" id="foto_input" multiple style="display: none;" onchange="handleFileSelect(this)">
                        </label>
                    </div>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">* Klik ikon + untuk menambah foto baru. Anda bisa memilih banyak foto sekaligus.</p>
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label>Video Produk (Opsional)</label>

                <label class="video-upload-box">
                    <input 
                        type="file"
                        name="video_produk"
                        id="video_input"
                        accept="video/*"
                        hidden
                        onchange="previewVideo(this)"
                    >

                    <div class="video-upload-content" id="video-upload-content">
                        <i class="fas fa-video"></i>
                        <h5>Upload Video Produk</h5>
                        <p>Klik untuk memilih video</p>
                        <span>Maksimal 1 video</span>
                    </div>
                </label>

                <div 
                    class="video-preview-container" 
                    id="video-preview-box"
                    style="{{ $produk->video_produk ? 'display:block;' : 'display:none;' }}"
                >
                    <video id="video-preview" controls>
                        @if($produk->video_produk)
                            <source src="{{ asset('storage/' . $produk->video_produk) }}" type="video/mp4">
                        @endif
                    </video>

                    <div class="remove-image" onclick="clearVideo()" title="Hapus Video">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 40px;">
                <label class="label-required">Deskripsi</label>
                <textarea name="deskripsi" class="form-input" rows="10" required>{{ $produk->deskripsi }}</textarea>
            </div>
        </div>
    </div>

    <div class="action-footer">
        <button type="button" class="btn-delete" onclick="if(confirm('Yakin ingin menghapus produk ini?')) document.getElementById('delete-form').submit()">Hapus Produk</button>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.produk-index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </div>
    </div>
</form>

<form id="delete-form" action="{{ route('admin.produk-destroy', $produk->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#kategoriSelect", {
        plugins: ['remove_button'],
        placeholder: 'Pilih kategori produk...',
        create: false,
        maxOptions: 100,
    });

    let selectedFiles = [];

    function handleFileSelect(input) {
        if (!input.files) return;
        
        const files = Array.from(input.files);
        selectedFiles = [...selectedFiles, ...files];
        
        updatePreviews();
        updateInputFiles();
    }

    function updatePreviews() {
        const container = document.getElementById('gallery-preview');
        const addButton = document.getElementById('add-photo-btn');
        
        // Remove only new previews (ones that don't have an ID starting with photo-)
        const oldNewPreviews = container.querySelectorAll('.gallery-item.new-preview');
        oldNewPreviews.forEach(el => el.remove());

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'gallery-item new-preview';
                div.innerHTML = `
                    <img src="${e.target.result}">
                    <div class="remove-image" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </div>
                `;
                container.insertBefore(div, addButton);
            }
            reader.readAsDataURL(file);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updatePreviews();
        updateInputFiles();
    }

    function updateInputFiles() {
        const input = document.getElementById('foto_input');
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    function deleteExistingPhoto(id) {
        if (confirm('Hapus foto ini secara permanen?')) {
            $.ajax({
                url: `{{ url('admin/foto-produk/destroy') }}/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(result) {
                    $(`#photo-${id}`).remove();
                },
                error: function(xhr) {
                    alert('Gagal menghapus foto.');
                }
            });
        }
    }

    function previewVideo(input) {
        const box = document.getElementById('video-preview-box');
        const video = document.getElementById('video-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                video.src = e.target.result;
                box.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearVideo() {
        const input = document.querySelector('input[name="video_produk"]');
        const box = document.getElementById('video-preview-box');
        const video = document.getElementById('video-preview');
        
        input.value = '';
        video.src = '';
        box.style.display = 'none';
        
        // If there was an existing video, we might want to tell the server to delete it.
        // For simplicity, we'll let the controller handle it if a new file is uploaded, 
        // but if we just want to DELETE without uploading new one, we need a hidden input.
        if (!document.getElementById('delete_video_flag')) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'delete_video';
            hidden.value = '1';
            hidden.id = 'delete_video_flag';
            input.parentNode.appendChild(hidden);
        }
    }
</script>
@stop
