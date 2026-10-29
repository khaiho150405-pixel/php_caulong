@extends('layouts.admin')

@section('title', 'Quản lý Logo Hệ Thống - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    <form action="{{ route('admin.logo.update') }}" method="POST" enctype="multipart/form-data" id="logoForm">
        @csrf

        {{-- Thanh thao tác: Lưu và Thoát --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-success px-4 fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu
                    </button>
                    <a href="{{ route('quanlysanpham.dashboard') }}" class="btn btn-danger px-3 fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Thoát
                    </a>
                </div>
            </div>
        </div>

        {{-- Khi lưu thành công: hiện đúng 1 dòng thông báo --}}
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Vui lòng kiểm tra các lỗi sau:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Khung cài đặt Logo & Tiêu đề hệ thống --}}
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-4">
                <label for="site_name" class="form-label fw-bold text-dark">Tiêu đề hệ thống :</label>
                <input type="text" class="form-control form-control-lg fs-6" id="site_name" name="site_name"
                       value="{{ old('site_name', $siteName) }}" placeholder="Ví dụ: Shop Cầu Lông Nasani..." />
            </div>

            {{-- Thẻ input file để nhận file ảnh upload --}}
            <input type="file" name="logo_file" id="logoFileInput" class="d-none" accept="image/*" />

            {{-- Khu vực tải & xem trước logo --}}
            <div class="row g-4 align-items-center mt-1">
                {{-- Cột bên trái: Drag & Drop và nút Chọn hình dùng label for="logoFileInput" --}}
                <div class="col-md-6 col-lg-5">
                    <label class="form-label fw-bold text-dark mb-2">Tải ảnh Logo mới :</label>

                    <label for="logoFileInput" id="dropZone"
                           class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative w-100 mb-0"
                           style="border-color: #cbd5e1 !important; min-height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer; transition: all 0.2s ease;">
                        <div class="text-secondary mb-2">
                            <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted"></i>
                        </div>
                        <p class="mb-1 fw-semibold text-secondary">Kéo và thả hình vào đây</p>
                        <p class="text-muted small mb-2">Hoặc</p>
                        <span class="btn text-white btn-sm px-3 fw-semibold pointer-events-none"
                              style="background-color: #dc2626; border-color: #dc2626;">
                            <i class="fa-regular fa-image me-1"></i> Chọn hình
                        </span>
                        <div id="selectedFileName" class="mt-2 text-primary small fw-semibold d-none"></div>
                    </label>

                    <div class="text-muted small mt-2">
                        Định dạng hỗ trợ: .jpg, .jpeg, .png, .gif, .webp (Tối đa 10MB)
                    </div>
                </div>

                {{-- Cột bên phải: Xem trước logo hiện tại --}}
                <div class="col-md-6 col-lg-5 text-center">
                    <label class="form-label fw-bold text-dark mb-2 d-block">Xem trước Logo :</label>
                    <div class="d-inline-flex flex-column align-items-center">
                        <div class="p-3 bg-white rounded-circle shadow-sm border position-relative"
                             style="width: 190px; height: 190px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="logoPreview" src="{{ $logoUrl }}" alt="Logo xem trước"
                                 style="max-width: 100%; max-height: 100%; object-fit: contain;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('logoFileInput');
    var previewImg = document.getElementById('logoPreview');
    var fileNameDisplay = document.getElementById('selectedFileName');

    function handleFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Vui lòng chọn một file hình ảnh hợp lệ (jpg, png, webp, gif).');
            return;
        }
        if (fileNameDisplay) {
            fileNameDisplay.textContent = 'Đã chọn: ' + file.name;
            fileNameDisplay.classList.remove('d-none');
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });
    }

    if (dropZone) {
        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropZone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#dc2626';
                dropZone.style.backgroundColor = '#fef2f2';
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropZone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.style.borderColor = '#cbd5e1';
                dropZone.style.backgroundColor = '#f8fafc';
            });
        });

        dropZone.addEventListener('drop', function (e) {
            var dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                fileInput.files = dt.files;
                handleFile(dt.files[0]);
            }
        });
    }
});
</script>
@endsection
