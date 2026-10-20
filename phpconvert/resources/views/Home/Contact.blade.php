@extends('layouts.app')

@section('title', 'Liên Hệ - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .contact-page {
        background: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin: 30px auto;
        max-width: 1200px;
    }

    .contact-heading {
        margin-bottom: 25px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 12px;
        font-weight: 700;
        color: #333;
    }

    .contact-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        font-size: 15px;
    }

    .contact-info-item .icon {
        font-size: 18px;
        width: 42px;
        height: 42px;
        line-height: 42px;
        text-align: center;
        margin-right: 15px;
        color: #f36f21;
        background-color: #fff4ed;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .map-container {
        position: relative;
        padding-bottom: 60%;
        height: 0;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 20px;
    }

    .map-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .form-control {
        height: 44px;
        border-radius: 6px;
    }

    textarea.form-control {
        height: auto;
    }

    .btn-submit {
        background-color: #f36f21;
        border-color: #f36f21;
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 6px;
    }

    .btn-submit:hover {
        background-color: #d15d18;
        border-color: #d15d18;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="contact-page">
        <div class="row">
            <div class="col-md-6">
                <h2 class="contact-heading">Thông Tin Cửa Hàng</h2>
                <p style="font-size: 15px; margin-bottom: 25px; color: #666;">
                    Chúng tôi luôn sẵn sàng hỗ trợ quý khách. Vui lòng liên hệ trực tiếp hoặc để lại tin nhắn qua biểu mẫu bên cạnh.
                </p>

                <div class="contact-info-item">
                    <span class="icon"><i class="fa-solid fa-location-dot"></i></span>
                    <span>140 Lê Trọng Tấn, Phường Tây Thạnh, Quận Tân Phú, TP.HCM</span>
                </div>

                <div class="contact-info-item">
                    <span class="icon"><i class="fa-solid fa-phone"></i></span>
                    <span>0123.456.789 (Hotline tư vấn)</span>
                </div>

                <div class="contact-info-item">
                    <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                    <span>support@caulongshop.vn</span>
                </div>

                <div class="contact-info-item">
                    <span class="icon"><i class="fa-solid fa-clock"></i></span>
                    <span>Giờ mở cửa: 08:00 - 21:00 (Tất cả các ngày trong tuần)</span>
                </div>

                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.06016148011!2d106.6263050750409!3d10.806746289344404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752be3d8d506d3%3A0x2113615f206e236f!2zMTQwIEzDqiBUcuG7jW5nIFjhuqVuLCBUw6J5IFRo4bqhbmgsIFTDom4gUGjDuiwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oLCBWaWV0bmFt!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="contact-heading">Gửi Tin Nhắn Cho Chúng Tôi</h2>

                @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Cảm ơn quý khách đã gửi tin nhắn liên hệ! Chúng tôi sẽ phản hồi sớm nhất.'); this.reset();">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="HoTen" style="font-weight: 600;">Họ và Tên *</label>
                        <input type="text" id="HoTen" name="HoTen" class="form-control" placeholder="Nhập họ và tên của bạn" required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="Email" style="font-weight: 600;">Email *</label>
                        <input type="email" id="Email" name="Email" class="form-control" placeholder="Nhập địa chỉ email" required />
                    </div>

                    <div class="form-group mb-3">
                        <label for="SoDienThoai" style="font-weight: 600;">Số Điện Thoại</label>
                        <input type="tel" id="SoDienThoai" name="SoDienThoai" class="form-control" placeholder="Nhập số điện thoại" />
                    </div>

                    <div class="form-group mb-3">
                        <label for="NoiDung" style="font-weight: 600;">Nội dung tin nhắn *</label>
                        <textarea id="NoiDung" name="NoiDung" class="form-control" rows="6" placeholder="Bạn cần hỗ trợ điều gì?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
