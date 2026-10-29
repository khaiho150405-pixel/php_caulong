@extends('layouts.admin')

@section('title', 'Đăng bài mới - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    @include('QuanLyBangTin._form', ['action' => route('quanlybantin.create.post'), 'tin' => null])
</div>
@endsection
