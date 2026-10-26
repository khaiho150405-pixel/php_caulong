{{-- Form dùng chung cho Thêm / Sửa trận đấu. Tham số: $action, $tran (null khi thêm mới) --}}
@php
    $gt = fn ($truong, $macDinh = null) => old($truong, $tran->{$truong} ?? $macDinh);
    $ngay = old('NgayThiDau', optional($tran?->NgayThiDau)->format('Y-m-d') ?? date('Y-m-d'));
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
        <div class="col-md-8">
            <label for="TenGiaiDau" class="form-label fw-semibold">Tên giải đấu *</label>
            <input type="text" class="form-control" id="TenGiaiDau" name="TenGiaiDau" value="{{ $gt('TenGiaiDau') }}" maxlength="150" required placeholder="VD: Giải vô địch thế giới BWF 2026" />
        </div>
        <div class="col-md-4">
            <label for="NgayThiDau" class="form-label fw-semibold">Ngày thi đấu *</label>
            <input type="date" class="form-control" id="NgayThiDau" name="NgayThiDau" value="{{ $ngay }}" required />
        </div>

        <div class="col-md-6">
            <label for="NoiDung" class="form-label fw-semibold">Nội dung thi đấu *</label>
            <input type="text" class="form-control" id="NoiDung" name="NoiDung" value="{{ $gt('NoiDung') }}" maxlength="60" list="dsNoiDung" required placeholder="VD: Đơn nam" />
            <datalist id="dsNoiDung">
                @foreach (['Đơn nam', 'Đơn nữ', 'Đôi nam', 'Đôi nữ', 'Đôi nam nữ'] as $nd)<option value="{{ $nd }}">@endforeach
            </datalist>
        </div>
        <div class="col-md-6">
            <label for="Vong" class="form-label fw-semibold">Vòng đấu *</label>
            <input type="text" class="form-control" id="Vong" name="Vong" value="{{ $gt('Vong', 'Chung kết') }}" maxlength="60" list="dsVong" required />
            <datalist id="dsVong">
                @foreach (['Chung kết', 'Bán kết', 'Tứ kết', 'Vòng 16', 'Vòng bảng'] as $v)<option value="{{ $v }}">@endforeach
            </datalist>
        </div>

        <div class="col-md-6">
            <label for="DoiA" class="form-label fw-semibold">Bên A *</label>
            <input type="text" class="form-control" id="DoiA" name="DoiA" value="{{ $gt('DoiA') }}" maxlength="150" required placeholder="VD: An Se-young (Hàn Quốc)" />
        </div>
        <div class="col-md-6">
            <label for="DoiB" class="form-label fw-semibold">Bên B *</label>
            <input type="text" class="form-control" id="DoiB" name="DoiB" value="{{ $gt('DoiB') }}" maxlength="150" required />
        </div>

        <div class="col-md-6">
            <label for="TiSo" class="form-label fw-semibold">Tỉ số (theo góc nhìn bên A trước)</label>
            <input type="text" class="form-control" id="TiSo" name="TiSo" value="{{ $gt('TiSo') }}" maxlength="60" placeholder="VD: 21-11, 21-11" />
            <div class="form-text">Để trống nếu chưa thi đấu / chưa có tỉ số.</div>
        </div>
        <div class="col-md-6">
            <label for="DoiThang" class="form-label fw-semibold">Bên thắng</label>
            <select class="form-select" id="DoiThang" name="DoiThang">
                <option value="">-- Chưa có --</option>
                <option value="A" @selected($gt('DoiThang') === 'A')>Bên A</option>
                <option value="B" @selected($gt('DoiThang') === 'B')>Bên B</option>
            </select>
        </div>

        <div class="col-12">
            <label for="DiaDiem" class="form-label fw-semibold">Địa điểm</label>
            <input type="text" class="form-control" id="DiaDiem" name="DiaDiem" value="{{ $gt('DiaDiem') }}" maxlength="120" />
        </div>
        <div class="col-md-5">
            <label for="NguonTin" class="form-label fw-semibold">Tên nguồn tin</label>
            <input type="text" class="form-control" id="NguonTin" name="NguonTin" value="{{ $gt('NguonTin') }}" maxlength="150" />
        </div>
        <div class="col-md-7">
            <label for="LienKetNguon" class="form-label fw-semibold">Liên kết nguồn</label>
            <input type="url" class="form-control" id="LienKetNguon" name="LienKetNguon" value="{{ $gt('LienKetNguon') }}" maxlength="300" placeholder="https://..." />
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk"></i> Lưu trận đấu</button>
            <a href="{{ route('quanlytrandau.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </div>
</form>
