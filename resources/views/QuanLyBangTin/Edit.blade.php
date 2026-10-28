@extends('layouts.admin')

@section('title', 'Sửa bài - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-primary"></i> Sửa bài viết</h2>
        <a href="{{ route('quanlybantin.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Quay lại danh sách</a>
    </div>
    <hr />
    @include('QuanLyBangTin._form', ['action' => route('quanlybantin.edit.post', ['id' => $tin->MaTin])])
</div>
@endsection
