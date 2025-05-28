@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Login Portal Mahasiswa</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @error('login')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">NIM / Username</label>
                            <input type="text" 
                                   class="form-control @error('login') is-invalid @enderror" 
                                   name="login" 
                                   value="{{ old('login') }}" 
                                   placeholder="Masukkan NIM atau Username"
                                   required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   name="password" 
                                   placeholder="Masukkan Password"
                                   required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                            <div class="text-center mt-3">
                                <span>Belum punya akun? </span>
                                <a href="{{ route('register') }}" class="text-decoration-none">Daftar di sini</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
