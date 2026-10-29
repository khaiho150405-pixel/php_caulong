@extends('layouts.admin')

@section('title', 'Sửa Biến Thể - Admin Shop')
@section('page-title', 'Sửa Biến Thể Sản Phẩm')

@section('content')
<div class="card card-custom p-4" style="max-width: 900px; margin: 0 auto;">
    <div class="mb-3">
        <h2 class="h4 mb-0 fw-bold">
            <i class="fa-solid fa-pen-to-square text-warning"></i>
            Sửa biến thể <code>{{ $bienThe->SKU }}</code>
        </h2>
    </div>
    <p class="text-muted mb-3">Sản phẩm: <strong>{{ $bienThe->sanPham->TenSanPham ?? 'N/A' }}</strong></p>
    <hr />

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($bienThe->HinhAnh)
        <div class="mb-3">
            <img src="{{ asset('images/' . $bienThe->HinhAnh) }}" alt="{{ $bienThe->SKU }}" class="rounded border p-1" style="max-height: 120px; object-fit: contain;" />
        </div>
    @endif

    <form action="{{ route('quanlysanpham.bienthe.update', ['maChiTiet' => $bienThe->MaChiTiet]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('QuanLySanPham._form_bien_the', ['bienThe' => $bienThe])

        <div class="mt-4">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save"></i> Lưu thay đổi</button>
            <a href="{{ route('quanlysanpham.details', ['id' => $bienThe->MaSanPham]) }}" class="btn btn-secondary px-4 ms-2">Hủy</a>
        </div>
    </form>
</div>
@endsection
