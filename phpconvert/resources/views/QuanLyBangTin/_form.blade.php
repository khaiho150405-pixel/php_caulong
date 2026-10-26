{{-- Form dùng chung cho Thêm / Sửa bài. Tham số: $action, $tin (null khi thêm mới), $sanPhams, $tranDaus --}}
@php
    $gt = fn ($truong, $macDinh = null) => old($truong, $tin->{$truong} ?? $macDinh);
    $ngayDang = old('NgayDang', optional($tin?->NgayDang)->format('Y-m-d\TH:i'));
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <label for="TieuDe" class="form-label fw-semibold">Tiêu đề *</label>
            <input type="text" class="form-control" id="TieuDe" name="TieuDe" value="{{ $gt('TieuDe') }}" maxlength="200" required />
        </div>

        <div class="col-md-5">
            <label for="LoaiTin" class="form-label fw-semibold">Loại tin *</label>
            <select class="form-select" id="LoaiTin" name="LoaiTin" required>
                @foreach (\App\Models\TinTuc::LOAI as $khoa => [$nhan])
                    <option value="{{ $khoa }}" @selected($gt('LoaiTin', 'tran-dau') === $khoa)>{{ $nhan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="NgayDang" class="form-label fw-semibold">Ngày đăng</label>
            <input type="datetime-local" class="form-control" id="NgayDang" name="NgayDang" value="{{ $ngayDang }}" />
            <div class="form-text">Để trống: đăng ngay. Chọn ngày tương lai: hẹn giờ.</div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="NoiBat" name="NoiBat" value="1" @checked((bool) $gt('NoiBat', false)) />
                <label class="form-check-label" for="NoiBat">Tin nổi bật</label>
            </div>
        </div>

        <div class="col-12">
            <label for="TomTat" class="form-label fw-semibold">Tóm tắt</label>
            <input type="text" class="form-control" id="TomTat" name="TomTat" value="{{ $gt('TomTat') }}" maxlength="300" placeholder="Một hai câu hiển thị ở danh sách" />
        </div>

        <div class="col-12">
            <label for="NoiDung" class="form-label fw-semibold">Nội dung *</label>
            <textarea class="form-control" id="NoiDung" name="NoiDung" rows="9" required>{{ $gt('NoiDung') }}</textarea>
            <div class="form-text">Cách một dòng trống để tách đoạn. Hãy tự diễn đạt lại, không sao chép nguyên văn từ nguồn khác.</div>
        </div>

        <div class="col-md-6">
            <label for="MaSanPham" class="form-label fw-semibold">Gắn với sản phẩm</label>
            <select class="form-select" id="MaSanPham" name="MaSanPham">
                <option value="">-- Không gắn --</option>
                @foreach ($sanPhams as $sp)
                    <option value="{{ $sp->MaSanPham }}" @selected((string) $gt('MaSanPham') === (string) $sp->MaSanPham)>{{ $sp->TenSanPham }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="MaTranDau" class="form-label fw-semibold">Gắn với trận đấu</label>
            <select class="form-select" id="MaTranDau" name="MaTranDau">
                <option value="">-- Không gắn --</option>
                @foreach ($tranDaus as $td)
                    <option value="{{ $td->MaTranDau }}" @selected((string) $gt('MaTranDau') === (string) $td->MaTranDau)>
                        {{ $td->NgayThiDau->format('d/m/Y') }} · {{ $td->NoiDung }} · {{ $td->TenGiaiDau }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label for="NguonTin" class="form-label fw-semibold">Tên nguồn tin</label>
            <input type="text" class="form-control" id="NguonTin" name="NguonTin" value="{{ $gt('NguonTin') }}" maxlength="150" placeholder="VD: BWF, Wikipedia, Badminton Planet" />
        </div>
        <div class="col-md-7">
            <label for="LienKetNguon" class="form-label fw-semibold">Liên kết nguồn</label>
            <input type="url" class="form-control" id="LienKetNguon" name="LienKetNguon" value="{{ $gt('LienKetNguon') }}" maxlength="300" placeholder="https://..." />
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk"></i> Lưu bài viết</button>
            <a href="{{ route('quanlybantin.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </div>
</form>
