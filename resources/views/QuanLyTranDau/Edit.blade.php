@extends('layouts.admin')

@section('title', 'Sửa kết quả trận đấu - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    @include('QuanLyTranDau._form', ['action' => route('quanlytrandau.edit.post', ['id' => $tran->MaTranDau]), 'tran' => $tran])
</div>
@endsection
