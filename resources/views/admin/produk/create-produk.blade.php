@extends($layout ?? 'layouts.admin_premium')

@section('title', 'Tambah Produk')

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
    .video-preview-container {
        margin-top: 12px;
        border-radius: 8px;
        overflow: hidden;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        position: relative;
        display: none;
    }
    .video-preview-container video {
        width: 100%;
        display: block;
    }

    .action-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
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

    .btn-submit:hover { opacity: 0.9; }
    .btn-cancel:hover { background: #f9fafb; }

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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
<h2 style="font-size: 18px; font-weight: 700; margin-bottom: 30px;">Tambah Produk</h2>

<form action="{{ route('admin.produk-store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-container">
        <!-- Left Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Kode Produk</label>
                <input type="text" name="kode_produk" class="form-input" value="{{ $autoKode }}" readonly>
            </div>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-input" placeholder="Masukkan nama produk" required>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" class="form-input" placeholder="Rp. 0" required>
            </div>
            <div class="form-group">
                <label>Kategori Produk</label>
                <select name="kategori_produk_id[]" class="form-input select2" multiple required>
                    @foreach($kategoriProduks as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori_produk }}</option>
                    @endforeach
                </select>
            </div>
            @if(count($usahas) > 1)
            <div class="form-group">
                <label>Pemilik (Usaha)</label>
                <select name="usaha_id" class="form-input" required>
                    <option value="">Pilih Usaha</option>
                    @foreach($usahas as $u)
                        <option value="{{ $u->id }}">{{ $u->nama_usaha }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="usaha_id" value="{{ $usahas->first()->id }}">
            @endif
            <!-- Hidden stok field as it's not in UI but required in controller -->
            <input type="hidden" name="stok" value="0">
        </div>

        <!-- Right Column -->
        <div class="form-column">
            <div class="form-group">
                <label>Foto Produk</label>
                <div class="gallery-container" id="gallery-preview">
                    <div class="gallery-item" id="add-photo-btn">
                        <label class="btn-add-gallery">
                            <i class="fas fa-plus"></i>
                            <input type="file" name="foto_produk[]" id="foto_input" multiple style="display: none;" onchange="handleFileSelect(this)">
                        </label>
                    </div>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">* Klik ikon + untuk menambah foto. Anda bisa memilih banyak foto sekaligus.</p>
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label>Video Produk (Opsional)</label>
                <input type="file" name="video_produk" class="form-input" accept="video/*" onchange="previewVideo(this)">
                <div class="video-preview-container" id="video-preview-box">
                    <video id="video-preview" controls></video>
                    <div class="remove-image" onclick="clearVideo()" title="Hapus Video">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 40px;">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-input" rows="10" placeholder="Masukkan deskripsi produk" required></textarea>
            </div>
        </div>
    </div>

    <div class="action-footer">
        <a href="{{ route('admin.produk-index') }}" class="btn-cancel">Batal</a>
        <button type="submit" class="btn-submit">Tambah Produk</button>
    </div>
</form>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih Kategori",
            allowClear: true
        });
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
        
        // Remove old previews
        const oldPreviews = container.querySelectorAll('.gallery-item:not(#add-photo-btn)');
        oldPreviews.forEach(el => el.remove());
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'gallery-item';
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
        } else {
            box.style.display = 'none';
        }
    }
    function clearVideo() {
        const input = document.querySelector('input[name="video_produk"]');
        const box = document.getElementById('video-preview-box');
        const video = document.getElementById('video-preview');
        
        input.value = '';
        video.src = '';
        box.style.display = 'none';
    }
</script>
@stop
