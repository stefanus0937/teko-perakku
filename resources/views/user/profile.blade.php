@extends($layout ?? 'layouts.user')

@section('title', 'Profil')

@section('css')
<style>
    .profile-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #18181b;
    }

    .welcome-banner {
        background: linear-gradient(90deg, #f4f4f5 0%, #e4e4e7 100%);
        border-radius: 20px;
        padding: 40px;
        display: flex;
        align-items: center;
        gap: 40px;
        margin-bottom: 40px;
        position: relative;
    }

    .profile-img-container {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 4px solid #fff;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .profile-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .welcome-text {
        flex: 1;
    }

    .welcome-text h2 {
        font-size: 32px;
        font-weight: 700;
        color: #18181b;
        margin-bottom: 5px;
    }

    .welcome-text .username {
        font-size: 16px;
        color: #71717a;
        margin-bottom: 20px;
        display: block;
    }

    .welcome-text .description {
        font-size: 14px;
        color: #4b5563;
        line-height: 1.6;
        max-width: 800px;
    }

    .banner-actions {
        position: absolute;
        top: 30px;
        right: 30px;
        display: flex;
        gap: 12px;
    }

    .ubah-btn {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .ubah-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px 40px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #71717a;
    }

    .form-control {
        width: 100%;
        padding: 14px 20px;
        border-radius: 10px;
        border: 1px solid #f1f1f4;
        background: #fff;
        font-size: 14px;
        font-weight: 500;
        color: #18181b;
        outline: none;
        transition: border-color 0.2s;
    }

    @media (max-width: 1024px) {
        .welcome-banner { flex-direction: column; text-align: center; padding: 40px 20px; }
        .banner-actions { position: static; justify-content: center; margin-top: 20px; }
        .welcome-text .description { margin: 0 auto; }
    }
</style>
@endsection

@section('content')
<h1 class="profile-title">Profil</h1>

<div class="welcome-banner">
    <div class="profile-img-container">
        <img src="{{ $user->foto ? asset('storage/'.$user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->username).'&background=f4f4f5&color=71717a&size=200' }}" alt="">
    </div>
    <div class="welcome-text">
        <h2>{{ $user->nama ?? $user->username }}</h2>
        <span class="username">@<span>{{ $user->username }}</span></span>
        
        @if($user->role == 'umkm' && $user->usaha)
            <p class="description">{{ $user->usaha->deskripsi_usaha ?? 'Belum ada deskripsi usaha.' }}</p>
        @endif
    </div>

    <div class="banner-actions">
        @if($user->role == 'umkm' && $user->usaha)
            <a href="{{ route('guest-detail-usaha', $user->usaha->id) }}" class="ubah-btn">
                Lihat Toko
            </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="ubah-btn">
            Ubah
        </a>
    </div>
</div>

<div class="info-grid">
    <div class="form-group">
        <label>Nama Lengkap / Usaha</label>
        <input type="text" class="form-control" value="{{ $user->nama ?? '-' }}" readonly>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
    </div>
    
    @if($user->role !== 'umkm')
    <div class="form-group">
        <label>Nomor HP</label>
        <input type="text" class="form-control" value="{{ $user->no_hp ?? '-' }}" readonly>
    </div>
    <div class="form-group">
        <label>Jenis Kelamin</label>
        <input type="text" class="form-control" value="{{ $user->gender ?? '-' }}" readonly>
    </div>
    <div class="form-group">
        <label>Usia</label>
        <input type="text" class="form-control" value="{{ $user->usia ? $user->usia . ' Tahun' : '-' }}" readonly>
    </div>
    @else
    <div class="form-group">
        <label>Spesialisasi</label>
        <input type="text" class="form-control" value="{{ $user->usaha->spesialisasi_usaha ?? '-' }}" readonly>
    </div>
    <div class="form-group">
        <label>Wilayah</label>
        <input type="text" class="form-control" value="{{ $user->wilayah->nama_wilayah ?? '-' }}" readonly>
    </div>
    @endif

    <div class="form-group">
        <label>Username</label>
        <input type="text" class="form-control" value="{{ $user->username }}" readonly>
    </div>
</div>

<div class="form-group mt-4">
    <label>Alamat</label>
    <textarea class="form-control" rows="3" readonly style="resize: none;">{{ $user->alamat ?? '-' }}</textarea>
</div>
@endsection
