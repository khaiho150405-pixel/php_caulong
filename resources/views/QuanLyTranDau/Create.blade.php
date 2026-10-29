@extends('layouts.admin')

@section('title', 'Thêm kết quả trận đấu - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    @include('QuanLyTranDau._form', ['action' => route('quanlytrandau.create.post'), 'tran' => null])
</div>
@endsection
