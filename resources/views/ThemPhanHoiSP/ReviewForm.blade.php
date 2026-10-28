@extends('layouts.app')

@section('title', 'Đánh giá sản phẩm - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .review-form-container {
        max-width: 550px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .review-form-container label {
        display: block;
        font-weight: 600;
        margin-top: 15px;
        margin-bottom: 8px;
        color: #333;
    }

    .review-form-container textarea {
        width: 100%;
        min-height: 120px;
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        resize: vertical;
    }

    .review-form-container textarea:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 5px rgba(243, 111, 33, 0.4);
    }

    .btn-submit-review {
        margin-top: 25px;
        padding: 12px 30px;
        background: var(--brand);
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        transition: background 0.2s;
    }

    .btn-submit-review:hover {
        background: var(--brand-700);
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 6px;
        margin-bottom: 15px;
    }

    .star-rating input[type="radio"] {
        display: none;
    }

    .star-rating label {
        margin: 0;
        font-size: 32px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input[type="radio"]:checked ~ label {
        color: var(--star);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="review-form-container">
        <h2 style="margin-top: 0; font-size: 22px; font-weight: 700;">Gửi đánh giá sản phẩm</h2>
        <hr />

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('themphanhoisp.submitreview') }}" method="POST">
            @csrf
            <input type="hidden" name="maSP" value="{{ $maSP }}" />

            <label>Đánh giá chất lượng:</label>
            <div class="star-rating">
                <input type="radio" id="star5" name="danhGia" value="5" @checked(old('danhGia', 5) == 5) required />
                <label for="star5" title="5 sao"><i class="fa fa-star"></i></label>

                <input type="radio" id="star4" name="danhGia" value="4" @checked(old('danhGia', 5) == 4) />
                <label for="star4" title="4 sao"><i class="fa fa-star"></i></label>

                <input type="radio" id="star3" name="danhGia" value="3" @checked(old('danhGia', 5) == 3) />
                <label for="star3" title="3 sao"><i class="fa fa-star"></i></label>

                <input type="radio" id="star2" name="danhGia" value="2" @checked(old('danhGia', 5) == 2) />
                <label for="star2" title="2 sao"><i class="fa fa-star"></i></label>

                <input type="radio" id="star1" name="danhGia" value="1" @checked(old('danhGia', 5) == 1) />
                <label for="star1" title="1 sao"><i class="fa fa-star"></i></label>
            </div>

            <label for="noiDung">Nội dung nhận xét:</label>
            <textarea id="noiDung" name="noiDung" placeholder="Hãy chia sẻ trải nghiệm sử dụng sản phẩm này với những người mua khác..." required minlength="5" maxlength="255">{{ old('noiDung') }}</textarea>

            <button type="submit" class="btn-submit-review">
                <i class="fa fa-paper-plane"></i> Gửi nhận xét
            </button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('home.chitietsanpham', ['id' => $maSP]) }}" style="color: #666;">&larr; Quay lại sản phẩm</a>
            </div>
        </form>
    </div>
</div>
@endsection
