@extends('layouts.admin')

@section('title', 'Sửa bài viết - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    @include('QuanLyBangTin._form', ['action' => route('quanlybantin.edit.post', ['id' => $tin->MaTin]), 'tin' => $tin])
</div>
@endsection
