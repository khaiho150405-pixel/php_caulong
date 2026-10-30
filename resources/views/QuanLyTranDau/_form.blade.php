
@php
    $gt = fn ($truong, $macDinh = null) => old($truong, $tran->{$truong} ?? $macDinh);
    $ngay = old('NgayThiDau', optional($tran?->NgayThiDau)->format('Y-m-d') ?? date('Y-m-d'));
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Vui lòng kiểm tra lại thông tin:</strong>
        <ul class="mb-0 mt-2 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('message'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ $action }}" method="POST" id="matchForm">
    @csrf

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-success px-4 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu trận đấu
                </button>
                <a href="{{ route('quanlytrandau.index') }}" class="btn btn-danger px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Thoát
                </a>
            </div>
            <div class="text-muted small">
                <span class="badge bg-light text-dark border">
                    <i class="fa-solid fa-table-tennis-paddle-ball me-1 text-primary"></i> {{ $tran ? 'Chỉnh sửa trận đấu #' . $tran->MaTranDau : 'Thêm mới trận đấu' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Thông tin trận đấu</h5>

                <div class="mb-3">
                    <label for="TenGiaiDau" class="form-label fw-bold text-dark">Tên giải đấu : <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg fs-6" id="TenGiaiDau" name="TenGiaiDau"
                           value="{{ $gt('TenGiaiDau') }}" maxlength="150" required placeholder="VD: Giải vô địch thế giới BWF 2026..." />
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="NoiDung" class="form-label fw-bold text-dark">Nội dung thi đấu : <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="NoiDung" name="NoiDung" value="{{ $gt('NoiDung') }}"
                               maxlength="60" list="dsNoiDung" required placeholder="VD: Đơn nam" />
                        <datalist id="dsNoiDung">
                            @foreach (['Đơn nam', 'Đơn nữ', 'Đôi nam', 'Đôi nữ', 'Đôi nam nữ'] as $nd)
                                <option value="{{ $nd }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="col-md-6">
                        <label for="Vong" class="form-label fw-bold text-dark">Vòng đấu : <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Vong" name="Vong" value="{{ $gt('Vong', 'Chung kết') }}"
                               maxlength="60" list="dsVong" required placeholder="VD: Chung kết" />
                        <datalist id="dsVong">
                            @foreach (['Chung kết', 'Bán kết', 'Tứ kết', 'Vòng 16', 'Vòng bảng'] as $v)
                                <option value="{{ $v }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-people-arrows me-1"></i> Hai bên đối đầu</h6>
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <label for="DoiA" class="form-label fw-bold text-primary mb-1">Bên A (Tuyển thủ / Đội A) : <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="DoiA" name="DoiA" value="{{ $gt('DoiA') }}"
                                   maxlength="150" required placeholder="VD: An Se-young (Hàn Quốc)" />
                        </div>
                        <div class="col-md-2 text-center text-muted fw-bold fs-5">
                            VS
                        </div>
                        <div class="col-md-5">
                            <label for="DoiB" class="form-label fw-bold text-danger mb-1">Bên B (Tuyển thủ / Đội B) : <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="DoiB" name="DoiB" value="{{ $gt('DoiB') }}"
                                   maxlength="150" required placeholder="VD: Akane Yamaguchi (Nhật Bản)" />
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="TiSo" class="form-label fw-bold text-dark">Tỉ số trận đấu :</label>
                        <input type="text" class="form-control" id="TiSo" name="TiSo" value="{{ $gt('TiSo') }}"
                               maxlength="60" placeholder="VD: 21-15, 18-21, 21-19" />
                        <div class="form-text small">Để trống nếu trận đấu chưa diễn ra / chưa có kết quả.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="DoiThang" class="form-label fw-bold text-dark">Bên chiến thắng :</label>
                        <select class="form-select" id="DoiThang" name="DoiThang">
                            <option value="">-- Chưa có kết quả --</option>
                            <option value="A" @selected($gt('DoiThang') === 'A')>Bên A thắng</option>
                            <option value="B" @selected($gt('DoiThang') === 'B')>Bên B thắng</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="DiaDiem" class="form-label fw-semibold text-dark">Địa điểm tổ chức :</label>
                    <input type="text" class="form-control" id="DiaDiem" name="DiaDiem" value="{{ $gt('DiaDiem') }}"
                           maxlength="120" placeholder="VD: Nhà thi đấu Arena Paris, Pháp..." />
                </div>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="NguonTin" class="form-label fw-semibold text-dark small">Tên nguồn tin</label>
                        <input type="text" class="form-control form-control-sm" id="NguonTin" name="NguonTin" value="{{ $gt('NguonTin') }}"
                               maxlength="150" placeholder="VD: BWF, Wikipedia..." />
                    </div>
                    <div class="col-md-7">
                        <label for="LienKetNguon" class="form-label fw-semibold text-dark small">Liên kết nguồn</label>
                        <input type="url" class="form-control form-control-sm" id="LienKetNguon" name="LienKetNguon" value="{{ $gt('LienKetNguon') }}"
                               maxlength="300" placeholder="https://..." />
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Thời gian thi đấu</h6>

                <div class="mb-3">
                    <label for="NgayThiDau" class="form-label fw-semibold text-dark mb-1">Ngày thi đấu : <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="NgayThiDau" name="NgayThiDau" value="{{ $ngay }}" required />
                </div>

                <div class="p-3 bg-light rounded-3 border text-center">
                    <div class="text-muted small mb-1">Xem trước trạng thái</div>
                    @if ($gt('DoiThang'))
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fa-solid fa-trophy me-1"></i> Bên {{ $gt('DoiThang') }} Thắng
                        </span>
                    @elseif ($gt('TiSo'))
                        <span class="badge bg-info text-dark fs-6 px-3 py-2">Đang cập nhật tỉ số</span>
                    @else
                        <span class="badge bg-secondary fs-6 px-3 py-2">Sắp diễn ra</span>
                    @endif
                </div>

                <div class="mt-3">
                    <a href="{{ route('quanlytrandau.index') }}" class="btn btn-outline-secondary btn-sm w-100 fw-semibold">
                        <i class="fa-solid fa-list me-1"></i> Danh sách các trận đấu
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
