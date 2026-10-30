
@php
    $gt = fn ($truong, $macDinh = null) => old($truong, $tin?->{$truong} ?? $macDinh);
    $ngayDang = old('NgayDang', optional($tin?->NgayDang)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'));
    $hinhAnhUrl = $tin ? $tin->hinh_anh_url : asset('images/news/court-journal-thumb.webp');
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

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="articleForm">
    @csrf

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-success px-4 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu
                </button>
                <a href="{{ route('quanlybantin.index') }}" class="btn btn-danger px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Thoát
                </a>
            </div>
            <div class="text-muted small">
                <span class="badge bg-light text-dark border">
                    <i class="fa-regular fa-clock me-1 text-primary"></i> {{ $tin ? 'Cập nhật lần cuối: ' . optional($tin->NgayDang)->format('d/m/Y H:i') : 'Tạo mới bài viết' }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Thông tin bài viết</h5>

                <div class="mb-3">
                    <label for="TieuDe" class="form-label fw-bold text-dark">Tiêu đề : <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg fs-6" id="TieuDe" name="TieuDe"
                           value="{{ $gt('TieuDe') }}" placeholder="Nhập tiêu đề bài viết..." maxlength="200" required />
                </div>

                <div class="mb-3">
                    <label for="TomTat" class="form-label fw-bold text-dark">Mô tả tóm tắt :</label>
                    <textarea class="form-control" id="TomTat" name="TomTat" rows="3"
                              placeholder="Nhập đoạn tóm tắt ngắn cho bài viết...">{{ $gt('TomTat') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="NoiDung" class="form-label fw-bold text-dark mb-2">
                        Nội dung chi tiết : <span class="text-danger">*</span>
                    </label>

                    
                    <textarea class="form-control" id="NoiDung" name="NoiDung" rows="18" required>{{ $gt('NoiDung') }}</textarea>
                </div>

                
                <div class="accordion" id="accordionExtra">
                    <div class="accordion-item border rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold text-secondary py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExtra">
                                <i class="fa-solid fa-sliders me-2 text-primary"></i> Cài đặt nâng cao (Chủ đề, Gắn sản phẩm, Trận đấu, Nguồn tin)
                            </button>
                        </h2>
                        <div id="collapseExtra" class="accordion-collapse collapse" data-bs-parent="#accordionExtra">
                            <div class="accordion-body bg-light rounded-bottom-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="LoaiTin" class="form-label fw-semibold small">Chủ đề bài viết</label>
                                        <select class="form-select form-select-sm" id="LoaiTin" name="LoaiTin">
                                            @foreach (\App\Models\TinTuc::LOAI as $khoa => [$nhan])
                                                <option value="{{ $khoa }}" @selected($gt('LoaiTin', 'san-pham-hot') === $khoa)>{{ $nhan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="MaSanPham" class="form-label fw-semibold small">Gắn sản phẩm</label>
                                        <select class="form-select form-select-sm" id="MaSanPham" name="MaSanPham">
                                            <option value="">-- Không gắn --</option>
                                            @foreach ($sanPhams as $sp)
                                                <option value="{{ $sp->MaSanPham }}" @selected((string) $gt('MaSanPham') === (string) $sp->MaSanPham)>{{ $sp->TenSanPham }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="MaTranDau" class="form-label fw-semibold small">Gắn trận đấu</label>
                                        <select class="form-select form-select-sm" id="MaTranDau" name="MaTranDau">
                                            <option value="">-- Không gắn --</option>
                                            @foreach ($tranDaus as $td)
                                                <option value="{{ $td->MaTranDau }}" @selected((string) $gt('MaTranDau') === (string) $td->MaTranDau)>
                                                    {{ $td->NgayThiDau->format('d/m/Y') }} · {{ $td->NoiDung }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="NguonTin" class="form-label fw-semibold small">Tên nguồn tin</label>
                                        <input type="text" class="form-control form-control-sm" id="NguonTin" name="NguonTin" value="{{ $gt('NguonTin') }}" placeholder="VD: BWF, BadmintonPlanet..." />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="LienKetNguon" class="form-label fw-semibold small">Liên kết nguồn gốc</label>
                                        <input type="url" class="form-control form-control-sm" id="LienKetNguon" name="LienKetNguon" value="{{ $gt('LienKetNguon') }}" placeholder="https://..." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm p-3 mb-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Ảnh đại diện bài viết</h6>
                <div class="mb-3 text-center">
                    <div class="rounded border p-1 bg-white mb-2 overflow-hidden shadow-sm d-flex align-items-center justify-content-center" style="min-height: 180px; max-height: 220px;">
                        <img id="articlePreviewImg" src="{{ $hinhAnhUrl }}" alt="Ảnh bài viết"
                             style="max-width: 100%; max-height: 210px; object-fit: contain;" class="rounded" />
                    </div>
                </div>

                
                <label for="hinhAnhInput" id="articleDropZone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light w-100 mb-0"
                       style="border-color: #cbd5e1 !important; cursor: pointer; transition: all 0.2s ease;">
                    <div class="text-secondary mb-1">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x text-muted"></i>
                    </div>
                    <p class="mb-1 small fw-semibold text-secondary">Kéo và thả hình vào đây</p>
                    <p class="text-muted small mb-0" style="font-size: 0.75rem;">hoặc bấm để chọn hình ảnh</p>
                    <div id="selectedArticleFileName" class="text-primary small fw-semibold d-none mt-2"></div>
                </label>

                
                <div class="d-flex gap-2 mt-2">
                    <label for="hinhAnhInput" class="btn btn-outline-danger btn-sm flex-fill fw-semibold mb-0" style="cursor: pointer;">
                        <i class="fa-regular fa-image me-1"></i> Chọn hình
                    </label>
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill fw-semibold" id="btnRemoveImg">
                        <i class="fa-regular fa-trash-can me-1"></i> Xóa ảnh
                    </button>
                </div>

                <input type="file" name="HinhAnh_file" id="hinhAnhInput" class="d-none" accept="image/*" />
                <input type="hidden" name="HinhAnh" id="hinhAnhVal" value="{{ $gt('HinhAnh') }}" />

                <div class="text-muted small mt-2 text-center" style="font-size: 0.75rem;">
                    Kích thước khuyến nghị: 390 x 270 px (.jpg, .png, .webp)
                </div>
            </div>

            
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Tình trạng hiển thị</h6>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label for="NoiBat" class="form-label fw-semibold mb-0">Nổi bật :</label>
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="NoiBat" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="NoiBat" name="NoiBat" value="1"
                               @checked((bool) $gt('NoiBat', false)) style="cursor: pointer; width: 44px; height: 22px;">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label for="HienThi" class="form-label fw-semibold mb-0">Hiển thị :</label>
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="HienThi" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="HienThi" name="HienThi" value="1"
                               @checked((bool) $gt('HienThi', true)) style="cursor: pointer; width: 44px; height: 22px;">
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label for="ThuTu" class="form-label fw-semibold mb-0">Số thứ tự :</label>
                    <input type="number" class="form-control text-center" id="ThuTu" name="ThuTu"
                           value="{{ $gt('ThuTu', 1) }}" min="0" style="width: 80px;" />
                </div>

                <div class="mb-0">
                    <label for="NgayDang" class="form-label fw-semibold mb-1">Ngày hiển thị :</label>
                    <input type="datetime-local" class="form-control" id="NgayDang" name="NgayDang" value="{{ $ngayDang }}" />
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')

<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('NoiDung')) {
        CKEDITOR.replace('NoiDung', {
            height: 440,
            language: 'vi',
            filebrowserUploadUrl: "{{ route('quanlybantin.upload_image') }}?_token={{ csrf_token() }}",
            filebrowserUploadMethod: 'form',
            toolbar: [
                { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Print' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                '/',
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
            ],
            allowedContent: true
        });
    }

    const dropZone = document.getElementById('articleDropZone');
    const fileInput = document.getElementById('hinhAnhInput');
    const hiddenVal = document.getElementById('hinhAnhVal');
    const previewImg = document.getElementById('articlePreviewImg');
    const btnRemove = document.getElementById('btnRemoveImg');
    const fileNameEl = document.getElementById('selectedArticleFileName');
    const defaultPlaceholder = "{{ asset('images/news/court-journal-thumb.webp') }}";

    if (btnRemove) {
        btnRemove.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.value = '';
            hiddenVal.value = '';
            previewImg.src = defaultPlaceholder;
            if (fileNameEl) {
                fileNameEl.textContent = '';
                fileNameEl.classList.add('d-none');
            }
        });
    }

    function handleSelectedFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Vui lòng chọn một file hình ảnh hợp lệ (jpg, png, webp, gif).');
            return;
        }
        if (fileNameEl) {
            fileNameEl.textContent = 'Đã chọn: ' + file.name;
            fileNameEl.classList.remove('d-none');
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            handleSelectedFile(this.files[0]);
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#dc2626';
            dropZone.style.backgroundColor = '#fef2f2';
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#cbd5e1';
            dropZone.style.backgroundColor = '#f8fafc';
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            fileInput.files = dt.files;
            handleSelectedFile(dt.files[0]);
        }
    });
});
</script>
@endpush
