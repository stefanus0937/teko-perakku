@extends('guest.layouts.main')
@section('title', 'Daftar')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/login-style.css') }}">
@endpush

@section('content')
<div class="login-page-wrapper">
    <div class="login-card">
        <h2 class="mb-4">Daftar Akun</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="username" class="form-label label-required">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="masukkan Username" value="{{ old('username') }}" required />
                @error('username')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="email" class="form-label label-required">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="masukkan Email" value="{{ old('email') }}" required />
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="password" class="form-label label-required">Kata Sandi</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="masukkan kata Sandi" required />
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="fa fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-4">
                <label for="password_confirmation" class="form-label label-required">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ulangi kata Sandi" required />
            </div>

            @if ($errors->any() && !$errors->has('username') && !$errors->has('email') && !$errors->has('password'))
                <div class="alert alert-danger mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="btn-login w-100">Daftar</button>

            <div class="text-center mt-3">
                <span class="text-muted">Sudah punya akun? 
                    <a href="{{ route('loginForm') }}" class="link-primary">Masuk sekarang</a>
                </span>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Ganti iconnya
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        });
    }
</script>
@endpush
