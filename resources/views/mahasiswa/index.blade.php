<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Mahasiswa ILKOM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    .table-max-height {
        max-height: 70vh;
        overflow-y: auto;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
    .preview-image {
        max-width: 200px;
        height: 200px;
        object-fit: cover;
        margin: 0 auto;
        display: block;
    }
    .modal-header {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .btn-group .btn {
        padding: .25rem .5rem;
        margin: 0 2px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Sambutan -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted mb-0">
                    NIM: {{ Auth::user()->nim }} | Program Studi: {{ Auth::user()->prodi }}
                </p>
            </div>
            <div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" 
                            onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Mahasiswa -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Daftar Mahasiswa</h5>
        </div>
        <div class="card-body table-max-height">
            <table id="mahasiswaTable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center">Foto</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mahasiswa)
                    <tr>
                        <td class="text-center">
                            @if($mahasiswa->foto)
                                <img src="{{ asset('storage/'.$mahasiswa->foto) }}" 
                                     class="rounded-circle border border-2 border-primary shadow-sm avatar-circle">
                            @else
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center avatar-circle">
                                    {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $mahasiswa->nim }}</td>
                        <td>{{ $mahasiswa->nama }}</td>
                        <td>{{ $mahasiswa->user->prodi }}</td>
                        <td>{{ $mahasiswa->semester }}</td>
                        <td>
                            @if($mahasiswa->user_id == Auth::id())
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $mahasiswa->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $mahasiswa->id }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteModal{{ $mahasiswa->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus data mahasiswa ini?</p>
                                                <small class="text-danger"><strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('mahasiswa.destroy', $mahasiswa->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus Data</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal{{ $mahasiswa->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">NIM</label>
                                            <input type="text" name="nim" class="form-control" value="{{ $mahasiswa->nim }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama</label>
                                            <input type="text" name="nama" class="form-control" value="{{ $mahasiswa->nama }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Semester</label>
                                            <input type="number" name="semester" class="form-control" value="{{ $mahasiswa->semester }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Program Studi</label>
                                            <select name="prodi" class="form-select" required>
                                                <option value="Informatika" {{ $mahasiswa->user->prodi == 'Informatika' ? 'selected' : '' }}>Informatika</option>
                                                <option value="Sistem Informasi" {{ $mahasiswa->user->prodi == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                                                <option value="Ilmu Komputer" {{ $mahasiswa->user->prodi == 'Ilmu Komputer' ? 'selected' : '' }}>Ilmu Komputer</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Foto Profil</label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   name="foto" 
                                                   accept="image/*,.heic,.heif,.tiff,.raw"
                                                   data-bs-toggle="tooltip" 
                                                   title="Mendukung semua format foto populer. Max 10MB">
                                            <small class="text-muted">
                                                Format yang didukung: JPG, PNG, GIF, SVG, BMP, WEBP, HEIC/HEIF, TIFF, RAW.
                                                <br>Maksimal 10MB
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#mahasiswaTable').DataTable({
        "pageLength": 10,
        "order": [[1, "asc"]],
        "language": {
          "lengthMenu": "Tampilkan _MENU_ data per halaman",
          "zeroRecords": "Data tidak ditemukan",
          "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
          "infoEmpty": "Tidak ada data tersedia",
          "infoFiltered": "(difilter dari _MAX_ total data)",
          "search": "Cari:",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          }
        }
      });

      // Edit button click handler
      $('.edit-btn').click(function() {
        let id = $(this).data('id');
        let nim = $(this).data('nim');
        let nama = $(this).data('nama');
        let semester = $(this).data('semester');
        let prodi = $(this).data('prodi');
        
        $(`#editModal${id} input[name="nim"]`).val(nim);
        $(`#editModal${id} input[name="nama"]`).val(nama);
        $(`#editModal${id} input[name="semester"]`).val(semester);
        $(`#editModal${id} select[name="prodi"]`).val(prodi);
      });
    });

    // Cropper.js logic
    let cropper;
    let image = document.getElementById('preview-crop-image');
    let input = document.getElementById('foto');
    input.addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
          image.src = ev.target.result;
          image.style.display = 'block';
          if (cropper) cropper.destroy();
          cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            cropBoxResizable: true,
            cropBoxMovable: true,
            minContainerWidth: 200,
            minContainerHeight: 200,
            ready() {
              cropper.crop();
            }
          });
        }
        reader.readAsDataURL(e.target.files[0]);
      }
    });

    // Saat submit form tambah, ambil hasil crop dan kirim sebagai base64
    document.querySelector('#modalTambahMahasiswa form').addEventListener('submit', function(e) {
      if (cropper) {
        e.preventDefault();
        cropper.getCroppedCanvas({
          width: 300,
          height: 300,
          imageSmoothingQuality: 'high'
        }).toBlob((blob) => {
          let reader = new FileReader();
          reader.onloadend = function() {
            document.getElementById('cropped_foto').value = reader.result;
            // Hapus input file agar backend tidak memproses file asli
            input.value = '';
            e.target.submit();
          }
          reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9);
      }
    });

    // Edit Mahasiswa Modal Logic
    let editCropper;
    let editImage = document.getElementById('edit-preview-crop-image');
    let editInput = document.getElementById('edit_foto');
    $('.btn-edit-mahasiswa').on('click', function() {
      // Set form action
      let id = $(this).data('id');
      $('#formEditMahasiswa').attr('action', '/mahasiswa/' + id);

      // Set value
      $('#edit_id').val(id);
      $('#edit_nim').val($(this).data('nim'));
      $('#edit_nama').val($(this).data('nama'));
      $('#edit_email').val($(this).data('email'));
      $('#edit_semester').val($(this).data('semester'));
      $('#edit_prodi').val($(this).data('prodi'));

      // Set foto preview
      let foto = $(this).data('foto');
      if (foto) {
        editImage.src = "{{ asset('storage') }}/" + foto;
        editImage.style.display = 'block';
      } else {
        editImage.style.display = 'none';
      }
      if (editCropper) editCropper.destroy();
      editCropper = null;
      $('#edit_cropped_foto').val('');
      editInput.value = '';
    });

    editInput.addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(ev) {
          editImage.src = ev.target.result;
          editImage.style.display = 'block';
          if (editCropper) editCropper.destroy();
          editCropper = new Cropper(editImage, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            cropBoxResizable: true,
            cropBoxMovable: true,
            minContainerWidth: 200,
            minContainerHeight: 200,
            ready() {
              editCropper.crop();
            }
          });
        }
        reader.readAsDataURL(e.target.files[0]);
      }
    });

    // Saat submit form edit, ambil hasil crop dan kirim sebagai base64
    document.getElementById('formEditMahasiswa').addEventListener('submit', function(e) {
      if (editCropper) {
        e.preventDefault();
        editCropper.getCroppedCanvas({
          width: 300,
          height: 300,
          imageSmoothingQuality: 'high'
        }).toBlob((blob) => {
          let reader = new FileReader();
          reader.onloadend = function() {
            document.getElementById('edit_cropped_foto').value = reader.result;
            e.target.submit();
            editInput.value = '';            // Hapus input file agar backend tidak memproses file asli
            editInput.value = '';
          }
          reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.9);
      }
    });

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview.tagName.toLowerCase() === 'img') {
                    preview.src = e.target.result;
                } else {
                    // Jika sebelumnya tidak ada foto (menggunakan div)
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = preview.className;
                    img.id = preview.id;
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // Handle file input change for preview
        $('.photo-input').change(function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const previewId = this.id.replace('foto', 'photoPreview');
                
                reader.onload = function(e) {
                    $(`#${previewId}`).attr('src', e.target.result);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle form submission
        $('.edit-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const id = form.data('id');
            const formData = new FormData(this);

            $.ajax({
                url: `/mahasiswa/${id}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $(`#alertContainer${id}`).html(`
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                        
                        // Refresh page after 1 second
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    // Show error message
                    $(`#alertContainer${id}`).html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${xhr.responseJSON?.message || 'Terjadi kesalahan'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                }
            });
        });
    });

    function previewFoto(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto hide alerts after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
  </script>
</body>
</html>