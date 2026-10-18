<div id="bannerCarousel" class="carousel slide mb-4 shadow m-1" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <div class="carousel-inner rounded">
        <div class="carousel-item active">
            <div class="carousel-caption d-none d-md-block">
                <h3>Khám phá thế giới Cầu Lông</h3>
                <p>Hãy chọn cho mình một người bạn đồng hành</p>
            </div>
            <img src="{{ asset('images/banner2.png') }}" class="d-block w-100 banner-img" alt="Banner 1" style="max-height: 450px; object-fit: cover;">
        </div>
        <div class="carousel-item">
            <div class="carousel-caption d-none d-md-block">
                <h3>Giảm giá đến 40%</h3>
                <p>Khuyến mãi ngập tràn cho lông thủ</p>
            </div>
            <img src="{{ asset('images/banner1.png') }}" class="d-block w-100 banner-img" alt="Banner 2" style="max-height: 450px; object-fit: cover;">
        </div>
        <div class="carousel-item">
            <div class="carousel-caption d-none d-md-block">
                <h3>Vợt mới hàng tuần</h3>
                <p>Yonex, Lining, Victor, Mizuno chính hãng</p>
            </div>
            <img src="{{ asset('images/banner3.png') }}" class="d-block w-100 banner-img" alt="Banner 3" style="max-height: 450px; object-fit: cover;">
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Trước</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sau</span>
    </button>
</div>
