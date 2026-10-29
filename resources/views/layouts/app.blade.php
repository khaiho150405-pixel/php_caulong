<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cửa Hàng Cầu Lông Online')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap&subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/Site.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
</head>

<body class="@yield('body_class')">
    <header class="main-header">
        <div class="header-top">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ \App\Models\CauHinh::getLogoUrl() }}" alt="Logo Cửa Hàng Cầu Lông" />
                </a>
            </div>

            <form action="{{ route('sanpham.timkiem') }}" method="get" class="search-form search-absolute">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm sản phẩm..." class="search-input" />
                <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="header-actions">
                <div class="account-section">
                    @guest
                        <div class="auth-buttons">
                            <a href="{{ route('taikhoan.dangnhap') }}" class="auth-btn login-btn">
                                <i class="fa-solid fa-user"></i>
                                <span>Đăng nhập</span>
                            </a>
                            <a href="{{ route('taikhoan.dangky') }}" class="auth-btn register-btn">
                                <span>Đăng ký</span>
                            </a>
                        </div>
                    @else
                        @php
                            $user = Auth::user();
                            $maQuyen = (int)($user->MaQuyen ?? 3);
                            $hoTen = session('HoTen', $user->TenDangNhap);
                        @endphp

                        <div class="user-dropdown">
                            <button class="user-btn" type="button">
                                <i class="fa-solid fa-user"></i>
                                <span>Xin chào, {{ $hoTen }}</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="user-menu">
                                @if ($maQuyen == 1)
                                    <a href="{{ route('quanlysanpham.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-boxes-stacked"></i> Quản lý Sản phẩm
                                    </a>
                                    <a href="{{ route('quanlytaikhoan.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-users"></i> Quản lý tài khoản
                                    </a>
                                    <a href="{{ route('quanlydonhang.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-file-invoice"></i> Quản lý Đơn hàng
                                    </a>
                                    <a href="{{ route('quanlykhuyenmai.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-tags"></i> Quản lý Khuyến mãi
                                    </a>
                                    <hr />
                                    <a href="{{ route('quanlysanpham.dashboard') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-chart-line"></i> Dashboard
                                    </a>
                                    <a href="{{ route('quanlythongbao.thongbao') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-bell"></i> Thông Báo Đánh Giá Thấp
                                    </a>
                                    <hr />
                                @endif

                                @if ($maQuyen == 2)
                                    <a href="{{ route('quanlysanpham.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-boxes-stacked"></i> Quản lý Sản phẩm
                                    </a>
                                    <a href="{{ route('quanlydonhang.index') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-file-invoice"></i> Quản lý Đơn hàng
                                    </a>
                                    <a href="{{ route('quanlythongbao.thongbao') }}" class="user-menu-item is-admin">
                                        <i class="fa-solid fa-bell"></i> Thông Báo Đánh Giá Thấp
                                    </a>
                                    <hr />
                                @endif

                                <a href="{{ route('taikhoan.thongtin') }}" class="user-menu-item">
                                    <i class="fa-solid fa-user-circle"></i> Thông tin tài khoản
                                </a>
                                <a href="{{ route('taikhoan.doimatkhau') }}" class="user-menu-item">
                                    <i class="fa-solid fa-key"></i> Đổi mật khẩu
                                </a>
                                <a href="{{ route('donhang.index') }}" class="user-menu-item">
                                    <i class="fa-solid fa-clipboard-list"></i> Đơn hàng của tôi
                                </a>
                                <form action="{{ route('taikhoan.dangxuat') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="user-menu-item w-100 text-start border-0 bg-transparent">
                                        <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                <a href="{{ route('home.viewcart') }}" class="cart-btn" aria-label="Giỏ hàng" title="Giỏ hàng">
                    <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                    <span class="cart-count">{{ session('GioHangCount', 0) }}</span>
                </a>
            </div>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a href="{{ route('home') }}">TRANG CHỦ</a></li>

                <li class="mega-menu-item">
                    <a href="{{ route('sanpham.index') }}">
                        SẢN PHẨM <i class="fa-solid fa-chevron-down"></i>
                    </a>

                    <div class="mega-submenu">
                        <div class="sidebar-categories">
                            <h3>DANH MỤC SẢN PHẨM</h3>
                            <a href="{{ route('sanpham.index', ['loai' => 'Vot']) }}" class="main-cat active" data-loai="Vot">
                                VỢT CẦU LÔNG <i class="fa-solid fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('sanpham.index', ['loai' => 'Giay']) }}" class="main-cat" data-loai="Giay">
                                GIÀY CẦU LÔNG <i class="fa-solid fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('sanpham.index', ['loai' => 'AoQuan']) }}" class="main-cat" data-loai="AoQuan">
                                ÁO QUẦN THỂ THAO <i class="fa-solid fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('sanpham.index', ['loai' => 'PhuKien']) }}" class="main-cat" data-loai="PhuKien">
                                PHỤ KIỆN CẦU LÔNG <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>

                        <div class="mega-contents-wrapper">
                            <!-- Vợt -->
                            <div class="mega-content active" data-loai="Vot">
                                <div class="product-brands-grid">
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Vot']) }}">VỢT YONEX</a></p>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Vot', 'ten' => 'Astrox']) }}">Vợt Yonex Astrox</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Vot', 'ten' => 'Nanoflare']) }}">Vợt Yonex Nanoflare</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Vot', 'ten' => 'Arcsaber']) }}">Vợt Yonex Arcsaber</a>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'Vot']) }}">VỢT LINING</a></p>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'Vot', 'ten' => 'Aeronaut']) }}">Vợt Lining Aeronaut</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'Vot', 'ten' => 'Tectonic']) }}">Vợt Lining Tectonic</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'Vot', 'ten' => 'Axforce']) }}">Vợt Lining Axforce</a>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'Vot']) }}">VỢT VICTOR</a></p>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'Vot', 'ten' => 'Auraspeed']) }}">Vợt Victor Auraspeed</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'Vot', 'ten' => 'Thruster']) }}">Vợt Victor Thruster</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'Vot', 'ten' => 'Jetspeed']) }}">Vợt Victor Jetspeed</a>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Mizuno', 'loai' => 'Vot']) }}">VỢT MIZUNO</a></p>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Mizuno', 'loai' => 'Vot', 'ten' => 'Fortius']) }}">Vợt Mizuno Fortius</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Mizuno', 'loai' => 'Vot', 'ten' => 'Altius']) }}">Vợt Mizuno Altius</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Giày -->
                            <div class="mega-content" data-loai="Giay">
                                <div class="product-brands-grid">
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Giay']) }}">GIÀY YONEX</a></p>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Giay', 'ten' => '65']) }}">Giày Yonex 65</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Giay', 'ten' => 'Aerus']) }}">Giày Yonex Aerus</a>
                                        <a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'Giay', 'ten' => 'Eclipsion']) }}">Giày Yonex Eclipsion</a>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'Giay']) }}">GIÀY LINING</a></p>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'Giay']) }}">GIÀY VICTOR</a></p>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Mizuno', 'loai' => 'Giay']) }}">GIÀY MIZUNO</a></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Áo quần -->
                            <div class="mega-content" data-loai="AoQuan">
                                <div class="product-brands-grid">
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Yonex', 'loai' => 'AoQuan']) }}">ÁO QUẦN YONEX</a></p>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Lining', 'loai' => 'AoQuan']) }}">ÁO QUẦN LINING</a></p>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Victor', 'loai' => 'AoQuan']) }}">ÁO QUẦN VICTOR</a></p>
                                    </div>
                                    <div class="brand-column">
                                        <p class="brand-title"><a href="{{ route('sanpham.index', ['hang' => 'Mizuno', 'loai' => 'AoQuan']) }}">ÁO QUẦN MIZUNO</a></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phụ kiện -->
                            <div class="mega-content" data-loai="PhuKien">
                                <div class="product-brands-grid">
                                    <div class="brand-column">
                                        <p class="brand-title">PHỤ KIỆN CẦU LÔNG</p>
                                        <a href="{{ route('sanpham.index', ['loai' => 'PhuKien', 'ten' => 'Túi đựng vợt']) }}">Túi đựng vợt</a>
                                        <a href="{{ route('sanpham.index', ['loai' => 'PhuKien', 'ten' => 'Dây cuốn cán']) }}">Dây cuốn cán</a>
                                        <a href="{{ route('sanpham.index', ['loai' => 'PhuKien', 'ten' => 'Balo cầu lông']) }}">Balo cầu lông</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li><a href="{{ route('khuyenmai.index') }}">KHUYẾN MÃI</a></li>
                <li><a href="{{ route('bangtin.index') }}">BẢNG TIN</a></li>
                <li><a href="{{ route('donhang.index') }}">ĐƠN HÀNG</a></li>
                <li><a href="{{ route('home.contact') }}">LIÊN HỆ</a></li>
            </ul>
        </nav>
    </header>

    @hasSection('full')
        <main class="full-main">
            @if (session('message') || session('error'))
                <div class="container pt-4">@include('partials._flash')</div>
            @endif
            @yield('full')
        </main>
    @else
        <div class="container my-4 my-lg-5">
            <main>
                @include('partials._flash')
                @yield('content')
            </main>
        </div>
    @endif

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>THÔNG TIN LIÊN HỆ</h4>
                <p><i class="fa-solid fa-location-dot"></i> 140 Lê Trọng Tấn, Tây Thạnh, Tân Phú, TP.HCM</p>
                <p><i class="fa-solid fa-phone"></i> 0123456789</p>
                <p><i class="fa-solid fa-envelope"></i> HUIT@gmail.com</p>
            </div>
            <div class="footer-section">
                <h4>DANH MỤC SẢN PHẨM</h4>
                <a href="{{ route('sanpham.index', ['loai' => 'Vot']) }}">Vợt cầu lông</a>
                <a href="{{ route('sanpham.index', ['loai' => 'Giay']) }}">Giày cầu lông</a>
                <a href="{{ route('sanpham.index', ['loai' => 'AoQuan']) }}">Áo quần thể thao</a>
                <a href="{{ route('sanpham.index', ['loai' => 'PhuKien']) }}">Phụ kiện cầu lông</a>
            </div>
            <div class="footer-section">
                <h4>HỖ TRỢ KHÁCH HÀNG</h4>
                <a href="{{ route('home') }}">Hướng dẫn mua hàng</a>
                <a href="{{ route('home') }}">Chính sách bảo hành</a>
                <a href="{{ route('home') }}">Chính sách đổi trả</a>
                <a href="{{ route('home') }}">Câu hỏi thường gặp</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Cửa hàng Cầu Lông HUIT. Bảo lưu mọi quyền.</p>
        </div>
    </footer>

    <!-- Chatbot Button & Container -->
    <button id="chat-toggle-btn" class="btn btn-primary rounded-circle shadow-lg"
            style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; width: 60px; height: 60px; border: none; font-size: 24px;">
        <i class="fa fa-comments"></i>
    </button>

    <div id="chat-container" class="card shadow-lg"
         style="position: fixed; bottom: 100px; right: 30px; z-index: 9999; width: 380px; height: 500px; display: none; flex-direction: column; overflow: hidden; border-radius: 15px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
            <h6 class="mb-0 font-weight-bold chat-header-title d-flex align-items-center">
                <img class="chat-header-avatar rounded-circle shadow-sm"
                     src="{{ \App\Models\CauHinh::getLogoUrl() }}"
                     alt="Logo"
                     style="width: 35px; height: 35px; object-fit: cover; margin-right: 10px;" />
                Trợ lý Tư Vấn AI
            </h6>
            <button id="close-chat" class="btn btn-sm btn-link text-white" style="text-decoration: none; font-size: 20px;">&times;</button>
        </div>

        <div class="card-body" id="chat-history" style="flex-grow: 1; overflow-y: auto; background-color: #f8f9fa; padding: 15px;">
            <div class="text-muted text-center small my-2">
                Chào bạn! Tôi có thể giúp gì về vợt, giày hay quần áo cầu lông hôm nay?
            </div>
        </div>

        <div class="card-footer bg-white" style="flex-shrink: 0; padding: 10px;">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control" placeholder="Nhập câu hỏi (ví dụ: tư vấn vợt dưới 3 triệu)..." style="border-radius: 20px 0 0 20px;">
                <button id="send-btn" class="btn btn-primary" style="border-radius: 0 20px 20px 0;">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Setup CSRF token cho AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Chatbot toggle
            $("#chat-toggle-btn, #close-chat").click(function () {
                $("#chat-container").toggle();
                if ($("#chat-container").is(":visible")) {
                    $("#user-input").focus();
                }
            });

            function sendMessage() {
                var message = $("#user-input").val();
                if (message.trim() === "") return;

                $("#chat-history").append(`
                    <div class="d-flex justify-content-end mb-2">
                        <div class="bg-primary text-white px-3 py-2" style="max-width: 80%; word-wrap: break-word; border-radius: 14px 14px 4px 14px;">
                            ${escapeHtml(message)}
                        </div>
                    </div>
                `);
                $("#user-input").val("");
                scrollToBottom();

                $("#chat-history").append('<div id="loading" class="text-muted small ml-2"><i>Đang trả lời...</i></div>');
                scrollToBottom();

                $.ajax({
                    url: '{{ route("chatbot.ask") }}',
                    type: 'POST',
                    data: { message: message },
                    success: function (response) {
                        $("#loading").remove();
                        var botHtml = '';
                        if (response.success) {
                            var botText = escapeHtml(response.reply).replace(/\n/g, "<br>");
                            botHtml = `
                                <div class="d-flex justify-content-start mb-2">
                                    <div class="bg-white text-dark border px-3 py-2" style="max-width: 85%; word-wrap: break-word; border-radius: 14px 14px 14px 4px;">
                                        ${botText}
                                    </div>
                                </div>`;
                        } else {
                            botHtml = `<div class="text-danger small my-2">Lỗi: ${escapeHtml(response.reply)}</div>`;
                        }
                        $("#chat-history").append(botHtml);
                        scrollToBottom();
                    },
                    error: function (xhr, status, error) {
                        $("#loading").remove();
                        $("#chat-history").append('<div class="text-danger small my-2">Lỗi kết nối server: ' + error + '</div>');
                        scrollToBottom();
                    }
                });
            }

            function escapeHtml(text) {
                var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            function scrollToBottom() {
                var chatHistory = document.getElementById("chat-history");
                if (chatHistory) {
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }
            }

            $("#send-btn").click(sendMessage);
            $("#user-input").keypress(function (e) {
                if (e.which == 13) sendMessage();
            });

            // Mega menu
            let hoverTimeout;
            let submenuHovered = false;
            let menuHovered = false;
            const HOVER_DELAY = 300;

            function switchMegaMenuContent(activeElement) {
                const loai = $(activeElement).data('loai');
                if ($(activeElement).hasClass('active')) return;

                $('.main-cat').removeClass('active');
                $('.mega-content').removeClass('active');
                $(activeElement).addClass('active');
                $(`.mega-content[data-loai="${loai}"]`).addClass('active');
            }

            $('.main-cat').on('mouseenter', function () {
                switchMegaMenuContent(this);
            });

            $('.mega-menu-item').on('mouseenter', function () {
                menuHovered = true;
                clearTimeout(hoverTimeout);
                $('.mega-submenu').css({ display: 'flex', opacity: 1, pointerEvents: 'auto' });
            });

            $('.mega-menu-item').on('mouseleave', function () {
                menuHovered = false;
                hoverTimeout = setTimeout(function () {
                    if (!submenuHovered) {
                        $('.mega-submenu').css({ display: 'none', opacity: 0, pointerEvents: 'none' });
                    }
                }, HOVER_DELAY);
            });

            $('.mega-submenu').on('mouseenter', function () {
                submenuHovered = true;
                clearTimeout(hoverTimeout);
            });

            $('.mega-submenu').on('mouseleave', function () {
                submenuHovered = false;
                hoverTimeout = setTimeout(function () {
                    if (!menuHovered) {
                        $('.mega-submenu').css({ display: 'none', opacity: 0, pointerEvents: 'none' });
                    }
                }, HOVER_DELAY);
            });

            // User dropdown
            $('.user-btn').click(function (e) {
                e.stopPropagation();
                $(this).siblings('.user-menu').toggleClass('show');
            });

            $(document).click(function () {
                $('.user-menu').removeClass('show');
            });

            $.get('{{ route("giohang.getcartcount") }}').done(function (data) {
                $('.cart-count').text(data);
            });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
