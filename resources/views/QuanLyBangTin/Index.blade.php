@extends('layouts.admin')

@section('title', 'Quản lý Bài viết - Admin Shop')

@section('content')
<div class="container-fluid px-0">
    
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('quanlybantin.create') }}" class="btn btn-success px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i> Thêm mới
            </a>
            <button type="button" class="btn text-white px-3 fw-semibold d-inline-flex align-items-center gap-2"
                    style="background-color: #b91c1c; border-color: #b91c1c;"
                    onclick="if (confirm('Bạn có chắc muốn xóa tất cả các bài viết đã chọn?')) { alert('Vui lòng chọn ít nhất một bài viết để xóa.'); }">
                <i class="fa-solid fa-trash-can"></i> Xóa tất cả
            </button>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-semibold"><i class="fa-solid fa-newspaper text-primary me-1"></i> Bài viết trên bảng tin</span>
            <a href="{{ route('quanlytrandau.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-table-tennis-paddle-ball"></i> Kết quả trận đấu
            </a>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    
    <div class="card border-0 shadow-sm p-3 mb-3">
        <form action="{{ route('quanlybantin.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="date_range" class="form-label small fw-semibold text-muted mb-1">Ngày Tạo</label>
                <input type="text" id="date_range" name="date_range" value="{{ $dateRange ?? '' }}" class="form-control"
                       placeholder="DD/MM/YYYY to DD/MM/YYYY hoặc YYYY-MM-DD" />
            </div>
            <div class="col-md-4">
                <label for="keyword" class="form-label small fw-semibold text-muted mb-1">Tin tức</label>
                <input type="text" id="keyword" name="keyword" value="{{ $keyword }}" class="form-control"
                       placeholder="Tìm kiếm tiêu đề, tóm tắt, nội dung..." />
            </div>
            <div class="col-md-3">
                <label for="loai" class="form-label small fw-semibold text-muted mb-1">Chủ đề</label>
                <select name="loai" id="loai" class="form-select">
                    <option value="">-- Tất cả chủ đề --</option>
                    @foreach (\App\Models\TinTuc::LOAI as $khoa => [$nhan])
                        <option value="{{ $khoa }}" @selected(($loai ?? '') === $khoa)>{{ $nhan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn text-white px-3 fw-semibold" style="background-color: #dc2626; border-color: #dc2626;" title="Tìm kiếm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if ($keyword !== '' || !empty($loai) || !empty($dateRange))
                    <a href="{{ route('quanlybantin.index') }}" class="btn btn-outline-secondary" title="Xóa lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive mb-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase fw-bold">
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="checkAll" class="form-check-input" />
                        </th>
                        <th style="width: 70px;" class="text-center">STT</th>
                        <th>TIÊU ĐỀ</th>
                        <th style="width: 110px;" class="text-center">HÌNH ẢNH</th>
                        <th style="width: 100px;" class="text-center">NỔI BẬT</th>
                        <th style="width: 100px;" class="text-center">HIỂN THỊ</th>
                        <th style="width: 100px;" class="text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list as $t)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $t->MaTin }}" class="form-check-input item-check" />
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center mx-auto"
                                       value="{{ $t->ThuTu ?? $loop->iteration }}" min="0" style="width: 58px;" readonly />
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $t->TieuDe }}</div>
                                @if ($t->NgayDang->isFuture())
                                    <span class="badge bg-info text-white mt-1" style="font-size: 0.72rem;">Hẹn giờ: {{ $t->NgayDang->format('d/m/Y H:i') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <img src="{{ $t->hinh_anh_url }}" alt="{{ $t->TieuDe }}"
                                     class="rounded border shadow-sm" style="width: 72px; height: 50px; object-fit: cover;" />
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block mb-0">
                                    <input class="form-check-input js-switch-toggle" type="checkbox" role="switch"
                                           data-url="{{ route('quanlybantin.togglestatus', ['id' => $t->MaTin]) }}"
                                           data-field="NoiBat"
                                           @checked((bool) $t->NoiBat)
                                           title="Bật/tắt gạt Nổi bật (hiển thị ở vị trí chính)"
                                           style="cursor: pointer; width: 42px; height: 21px;">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block mb-0">
                                    <input class="form-check-input js-switch-toggle" type="checkbox" role="switch"
                                           data-url="{{ route('quanlybantin.togglestatus', ['id' => $t->MaTin]) }}"
                                           data-field="HienThi"
                                           @checked((bool) ($t->HienThi ?? true))
                                           title="Bật/tắt Hiển thị (tắt sẽ ẩn bài khỏi website)"
                                           style="cursor: pointer; width: 42px; height: 21px;">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('quanlybantin.edit', ['id' => $t->MaTin]) }}" class="text-danger fs-5" title="Chỉnh sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <a href="{{ route('quanlybantin.delete', ['id' => $t->MaTin]) }}" class="text-danger fs-5" title="Xóa">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                @include('partials._empty', ['message' => 'Chưa có bài viết nào phù hợp với bộ lọc tìm kiếm.'])
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light">
            @include('partials._pagination', ['items' => $list])
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const itemChecks = document.querySelectorAll('.item-check');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            itemChecks.forEach(cb => cb.checked = checkAll.checked);
        });
    }

    document.querySelectorAll('.js-switch-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            var url = this.getAttribute('data-url');
            var field = this.getAttribute('data-field');
            var value = this.checked ? 1 : 0;
            var self = this;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ field: field, value: value })
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) {
                    alert(data.message || 'Lỗi cập nhật');
                    self.checked = !self.checked;
                }
            })
            .catch(function (err) {
                alert('Lỗi kết nối máy chủ!');
                self.checked = !self.checked;
            });
        });
    });
});
</script>
@endpush
@endsection
