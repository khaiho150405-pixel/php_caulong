
@php
    $ds = isset($dsNoiBat) && $dsNoiBat instanceof \Illuminate\Support\Collection && $dsNoiBat->isNotEmpty()
        ? $dsNoiBat
        : (isset($t) && $t ? collect([$t]) : collect());
@endphp

@if ($ds->count() > 1)
    <section class="bt-featured-slider-box" id="featuredSliderBox" aria-label="Tin nổi bật">
        <div class="bt-featured-bar">
            <div class="bt-featured-title">
                <span class="bt-ribbon"><i class="fa-solid fa-fire text-danger me-1"></i> Tin nổi bật</span>
            </div>
            <div class="bt-featured-nav">
                <button type="button" class="bt-nav-btn" id="btnPrevSlide" title="Bài trước (trượt xuống)" aria-label="Bài trước">
                    <i class="fa-solid fa-chevron-up"></i>
                </button>
                <span class="bt-featured-counter">
                    <b id="featuredCurrentIdx">1</b> / {{ $ds->count() }}
                </span>
                <button type="button" class="bt-nav-btn" id="btnNextSlide" title="Bài tiếp theo (trượt lên)" aria-label="Bài tiếp theo">
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
        </div>

        <div class="bt-featured-viewport" id="featuredViewport">
            <div class="bt-featured-track" id="featuredTrack">
                @foreach ($ds as $index => $item)
                    <div class="bt-featured-slide" data-index="{{ $index }}">
                        <article class="bt-lead loai-{{ $item->LoaiTin }}">
                            <a href="{{ route('bangtin.show', ['id' => $item->MaTin]) }}" class="bt-lead-media {{ $item->sanPham ? 'is-product' : 'is-editorial' }}" aria-hidden="true" tabindex="-1">
                                <img src="{{ $item->hinh_anh_url }}" alt="{{ $item->TieuDe }}" loading="lazy" decoding="async" width="800" height="500" onerror="this.onerror=null;this.src='{{ asset('images/news/court-journal-800.webp') }}'">
                                @unless ($item->sanPham)<span class="bt-photo-credit">Ảnh bài viết</span>@endunless
                            </a>
                            <div class="bt-lead-body">
                                <div class="bt-item-top">
                                    <span class="bt-pill">{{ $item->nhanLoai }}</span>
                                    <time datetime="{{ $item->NgayDang->toIso8601String() }}">{{ $item->NgayDang->format('d/m/Y') }}</time>
                                </div>
                                <h2><a href="{{ route('bangtin.show', ['id' => $item->MaTin]) }}">{{ $item->TieuDe }}</a></h2>
                                @if ($item->TomTat)<p>{{ $item->TomTat }}</p>@endif
                                <a href="{{ route('bangtin.show', ['id' => $item->MaTin]) }}" class="bt-more">Đọc bài viết <span aria-hidden="true">↗</span></a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bt-featured-indicators" id="featuredIndicators">
            @foreach ($ds as $index => $item)
                <button type="button" class="bt-indicator-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}" aria-label="Đến tin {{ $index + 1 }}"></button>
            @endforeach
        </div>
    </section>

    <script>
    (function () {
        function initFeaturedSlider() {
            var box = document.getElementById('featuredSliderBox');
            var viewport = document.getElementById('featuredViewport');
            var track = document.getElementById('featuredTrack');
            var counter = document.getElementById('featuredCurrentIdx');
            var btnPrev = document.getElementById('btnPrevSlide');
            var btnNext = document.getElementById('btnNextSlide');
            var dots = document.querySelectorAll('.bt-indicator-dot');
            if (!box || !viewport || !track) return;

            var originalSlides = Array.prototype.slice.call(track.children);
            var total = originalSlides.length;
            if (total <= 1) return;

            var firstClone = originalSlides[0].cloneNode(true);
            firstClone.setAttribute('data-clone', 'true');
            firstClone.setAttribute('aria-hidden', 'true');
            track.appendChild(firstClone);

            var allSlides = Array.prototype.slice.call(track.children);
            var currentIndex = 0;
            var isTransitioning = false;
            var autoInterval = null;
            var slideDuration = 4500; // 4.5 giây tự động trượt lên bài tiếp theo

            function updateViewportHeight() {
                var activeSlide = allSlides[currentIndex] || allSlides[0];
                if (activeSlide && activeSlide.offsetHeight > 0) {
                    viewport.style.height = activeSlide.offsetHeight + 'px';
                }
            }

            function goToSlide(targetIdx, animate) {
                if (animate === undefined) animate = true;
                if (isTransitioning) return;

                if (animate) {
                    isTransitioning = true;
                    track.style.transition = 'transform 0.65s cubic-bezier(0.25, 1, 0.5, 1)';
                } else {
                    track.style.transition = 'none';
                }

                currentIndex = targetIdx;
                var targetSlide = allSlides[currentIndex];
                var offset = targetSlide ? targetSlide.offsetTop : 0;
                track.style.transform = 'translateY(-' + offset + 'px)';
                updateViewportHeight();

                var displayIdx = (currentIndex >= total) ? 0 : currentIndex;
                if (counter) counter.textContent = (displayIdx + 1);
                dots.forEach(function (d, i) {
                    d.classList.toggle('active', i === displayIdx);
                });

                if (animate) {
                    setTimeout(function () {
                        if (currentIndex >= total) {
                            track.style.transition = 'none';
                            currentIndex = 0;
                            track.style.transform = 'translateY(0px)';
                            updateViewportHeight();
                        }
                        isTransitioning = false;
                    }, 680);
                }
            }

            function nextSlide() {
                if (isTransitioning) return;
                goToSlide(currentIndex + 1, true);
            }

            function prevSlide() {
                if (isTransitioning) return;
                if (currentIndex === 0) {
                    track.style.transition = 'none';
                    currentIndex = total;
                    var offset = allSlides[currentIndex].offsetTop;
                    track.style.transform = 'translateY(-' + offset + 'px)';
                    track.offsetHeight; // Force reflow
                    goToSlide(total - 1, true);
                } else {
                    goToSlide(currentIndex - 1, true);
                }
            }

            if (btnNext) btnNext.addEventListener('click', function () { nextSlide(); resetTimer(); });
            if (btnPrev) btnPrev.addEventListener('click', function () { prevSlide(); resetTimer(); });

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    var target = parseInt(this.getAttribute('data-slide'), 10);
                    if (!isNaN(target) && target !== currentIndex) {
                        goToSlide(target, true);
                        resetTimer();
                    }
                });
            });

            function startTimer() {
                stopTimer();
                autoInterval = setInterval(nextSlide, slideDuration);
            }

            function stopTimer() {
                if (autoInterval) {
                    clearInterval(autoInterval);
                    autoInterval = null;
                }
            }

            function resetTimer() {
                stopTimer();
                startTimer();
            }

            box.addEventListener('mouseenter', stopTimer);
            box.addEventListener('mouseleave', startTimer);

            window.addEventListener('resize', function () {
                updateViewportHeight();
                var targetSlide = allSlides[currentIndex];
                if (targetSlide) {
                    track.style.transition = 'none';
                    track.style.transform = 'translateY(-' + targetSlide.offsetTop + 'px)';
                }
            });

            setTimeout(function () {
                updateViewportHeight();
                startTimer();
            }, 150);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFeaturedSlider);
        } else {
            initFeaturedSlider();
        }
    })();
    </script>
@elseif ($ds->isNotEmpty())
    @php $single = $ds->first(); @endphp
    <article class="bt-lead loai-{{ $single->LoaiTin }}">
        <a href="{{ route('bangtin.show', ['id' => $single->MaTin]) }}" class="bt-lead-media {{ $single->sanPham ? 'is-product' : 'is-editorial' }}" aria-hidden="true" tabindex="-1">
            <img src="{{ $single->hinh_anh_url }}" alt="" loading="lazy" decoding="async" width="800" height="500" onerror="this.onerror=null;this.src='{{ asset('images/news/court-journal-800.webp') }}'">
            @unless ($single->sanPham)<span class="bt-photo-credit">Ảnh bài viết</span>@endunless
        </a>
        <div class="bt-lead-body">
            <span class="bt-ribbon">Tin nổi bật</span>
            <div class="bt-item-top">
                <span class="bt-pill">{{ $single->nhanLoai }}</span>
                <time datetime="{{ $single->NgayDang->toIso8601String() }}">{{ $single->NgayDang->format('d/m/Y') }}</time>
            </div>
            <h2><a href="{{ route('bangtin.show', ['id' => $single->MaTin]) }}">{{ $single->TieuDe }}</a></h2>
            @if ($single->TomTat)<p>{{ $single->TomTat }}</p>@endif
            <a href="{{ route('bangtin.show', ['id' => $single->MaTin]) }}" class="bt-more">Đọc bài viết <span aria-hidden="true">↗</span></a>
        </div>
    </article>
@endif
