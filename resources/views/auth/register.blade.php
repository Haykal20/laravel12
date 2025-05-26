@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Pendaftaran Mahasiswa Baru</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIM</label>
                                <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                                       name="nim" value="{{ old('nim') }}" required>
                                @error('nim')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <select class="form-select @error('prodi') is-invalid @enderror" name="prodi" required>
                                <option value="">Pilih Program Studi</option>
                                <optgroup label="Fakultas Ilmu Komputer">
                                    <option value="Informatika">Teknik Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Ilmu Komputer">Ilmu Komputer</option>
                                </optgroup>
                            </select>
                            @error('prodi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                                   name="foto" 
                                   accept="image/*"
                                   data-bs-toggle="tooltip" 
                                   title="Mendukung format: JPG, JPEG, PNG, GIF, SVG, BMP, WEBP. Maksimal 5MB">
                            <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, GIF, SVG, BMP, WEBP. Maksimal 5MB</small>
                            <div class="mt-2 text-center" id="preview-container" style="display:none;">
                                <img id="preview-image" class="rounded-circle border shadow-sm" 
                                     style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                            @error('foto')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required>
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Daftar Sekarang</button>
                            <div class="text-center mt-3">
                                <span>Sudah punya akun? </span>
                                <a href="{{ route('login') }}" class="text-decoration-none">Login di sini</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('preview-image');
    const container = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
