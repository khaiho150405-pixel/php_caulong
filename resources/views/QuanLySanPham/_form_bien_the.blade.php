{{-- Các trường nhập của biến thể sản phẩm. Tham số: $bienThe (null khi thêm mới), $mauSacs, $sizes --}}
<div class="row g-3">
    <div class="col-md-4">
        <label for="SKU" class="form-label fw-semibold">Mã SKU *</label>
        <input type="text" class="form-control @error('SKU') is-invalid @enderror" id="SKU" name="SKU"
               value="{{ old('SKU', $bienThe->SKU ?? '') }}" maxlength="50" required placeholder="VD: YNX-AX99-4U" />
        @error('SKU') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="MaMau" class="form-label fw-semibold">Màu sắc</label>
        <select class="form-select @error('MaMau') is-invalid @enderror" id="MaMau" name="MaMau">
            <option value="">-- Mặc định --</option>
            @foreach ($mauSacs as $mau)
                <option value="{{ $mau->MaMau }}" @selected((string) old('MaMau', $bienThe->MaMau ?? '') === (string) $mau->MaMau)>{{ $mau->TenMau }}</option>
            @endforeach
        </select>
        @error('MaMau') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="MaSize" class="form-label fw-semibold">Size</label>
        <select class="form-select @error('MaSize') is-invalid @enderror" id="MaSize" name="MaSize">
            <option value="">-- Tiêu chuẩn --</option>
            @foreach ($sizes as $sz)
                <option value="{{ $sz->MaSize }}" @selected((string) old('MaSize', $bienThe->MaSize ?? '') === (string) $sz->MaSize)>{{ $sz->TenSize }} ({{ $sz->LoaiSize }})</option>
            @endforeach
        </select>
        @error('MaSize') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="GiaBan" class="form-label fw-semibold">Giá bán (VNĐ) *</label>
        <input type="number" class="form-control @error('GiaBan') is-invalid @enderror" id="GiaBan" name="GiaBan"
               value="{{ old('GiaBan', isset($bienThe) ? (int) $bienThe->GiaBan : '') }}" min="0" step="1000" required />
        @error('GiaBan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="SoLuongTon" class="form-label fw-semibold">Số lượng tồn *</label>
        <input type="number" class="form-control @error('SoLuongTon') is-invalid @enderror" id="SoLuongTon" name="SoLuongTon"
               value="{{ old('SoLuongTon', $bienThe->SoLuongTon ?? 0) }}" min="0" required />
        @error('SoLuongTon') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label for="HinhAnhUpload" class="form-label fw-semibold">Hình ảnh riêng</label>
        <input type="file" class="form-control @error('HinhAnhUpload') is-invalid @enderror" id="HinhAnhUpload" name="HinhAnhUpload" accept="image/*" />
        @error('HinhAnhUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
