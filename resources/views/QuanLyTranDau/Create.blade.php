@extends('layouts.admin')

@section('title', 'Thêm trận đấu - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 850px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-plus-circle text-primary"></i> Thêm kết quả trận đấu</h2>
        <a href="{{ route('quanlytrandau.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Quay lại danh sách</a>
    </div>
    <hr />
    @include('QuanLyTranDau._form', ['action' => route('quanlytrandau.create.post'), 'tran' => null])
</div>
@endsection
