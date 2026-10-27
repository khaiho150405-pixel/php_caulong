{{-- Nội dung chi tiết trận dùng cùng cách đọc điểm với cột kết quả. --}}
@include('BangTin._bangdiem', ['ds' => collect([$tran]), 'tieuDe' => 'Kết quả trận đấu'])
