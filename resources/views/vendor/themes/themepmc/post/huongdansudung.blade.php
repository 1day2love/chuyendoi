@extends('themes::themepmc.layout')
<title>Hướng Dẫn Truy Cập</title>
@section('content')
<div class="main-content" id="static-page">
                <div class="container">
                    <div class="static-page">
                        <div class="title">
                            <h1><strong>Hướng Dẫn Truy Cập</strong></h1> </div>
                        <div class="news-content">
                            <h2>Hướng dẫn khắc phục khi không truy cập được</h2>
                            <h3>Kiểm tra kết nối mạng</h3>
                            <ul>
                                <li>Đảm bảo rằng kết nối Internet của bạn ổn định.</li>
                                <li>Tải lại trang bằng cách nhấn F5 hoặc biểu tượng làm mới trình duyệt.</li>
                                <li>Xóa cache trình duyệt</li>
                                <li>Vào Cài đặt trình duyệt &gt; Lịch sử duyệt web &gt; Xóa cache.</li>
                                <li>Thử truy cập lại sau khi xóa dữ liệu trình duyệt.</li>
                            </ul>
                            <h3>Sử dụng trình duyệt khác hoặc chế độ ẩn danh</h3>
                            <p>Nếu vẫn không được, hãy thử sử dụng trình duyệt khác (Chrome, Firefox, Edge...) hoặc mở trang trong chế độ ẩn danh.</p>
                            <h3>Sử dụng VPN</h3>
                            <p>Trong trường hợp trang bị chặn tại khu vực của bạn, hãy dùng ứng dụng VPN để đổi địa chỉ IP và truy cập lại để biết tên miền đang chạy, sau đó bạn truy cập bình thường và xem phim, nếu không xem được phim bạn có thể tắt VPN để load được trình phát.</p>
                            <h2>Làm gì khi nghi ngờ truy cập nhầm trang web?</h2>
                            <p>Chúng tôi luôn có giao diện thiết kế riêng độc quyền và 1 kho phim đồ sộ do vậy bạn có thể nhận ra đâu là "riu" đâu là hàng "pha ke" tuy nhiên nếu bạn vô tình truy cập nhầm trang web giả mạo, hãy:</p>
                            <ul>
                                <li>Nhanh chóng đóng trang web đó.</li>
                                <li>Quét virus và kiểm tra máy tính của bạn bằng phần mềm bảo mật.</li>
                                <li>Không cung cấp bất kỳ thông tin cá nhân nào trên các trang lạ.</li>
                            </ul>
                            <h2>Liên hệ hỗ trợ chính thức</h2>
                            <p>Nếu bạn cần hỗ trợ, hãy tìm kiếm thông tin qua website chính thức của chúng tôi hoặc liên hệ với đội ngũ quản trị qua email tại phần <a href="../post/contact">liên hệ</a> hoặc <a href="https://t.me/+dsohJWvfjJc5NmQ1" target="_blank" rel="nofollow noopener"> tham gia nhóm Telegram</a> .</p>
                            <h2>Cần làm gì khi không sử dụng ứng dụng telegram được?</h2>
                            <p>Trong trường hợp ứng dụng bị chặn tại khu vực của bạn, hãy thử sử dụng và bật ứng dụng VPN (ví dụ: 1.1.1.1,...) để đổi địa chỉ IP và thực hiện truy cập lại ứng dụng.</p>
                            <p><strong>Bây giờ hãy bắt đầu khám phá các thể loại phim nào:</strong>
                            </p>
                            <p><a href="../">Trang chủ phimtv</a> - <a href="../vod/phim-chieu-rap">Phim Chiếu Rạp</a> -<a href="../vod/phim-bo">Phim Bộ</a> - <a href="../vod/phim-le">Phim Lẻ</a> - <a href="../vod/phim-sap-chieu">Phim Sắp Chiếu</a>
                            </p>
                            <p><strong>Chúc bạn có những giây phút xem phim thú vị và an toàn!</strong>
                            </p>
                        </div>
            </div>
            </div>
        </div>
        <!-- content -->
    </div>
    <style>
    .static-page .title h1 {
        font-size: 24px;
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
        border-bottom: 2px solid #2c3e50;
        padding-bottom: 10px;
    }
    
    .static-page .news-content p {
        margin: 10px 0;
    }
    
    .static-page .news-content h2 {
        font-size: 20px;
        color: #1abc9c;
        margin: 20px 0 10px;
        border-left: 4px solid #1abc9c;
        padding-left: 10px;
    }
    
    .static-page .news-content h3 {
        font-size: 18px;
        color: #3498db;
        margin: 15px 0 10px;
    }
    
    .static-page .news-content ul {
        margin: 10px 0 20px 20px;
        padding-left: 20px;
        list-style-type: disc;
    }
    
    .static-page .news-content ul li {
        margin: 5px 0;
    }
    
    .static-page .news-content strong {
        color: #e74c3c;
    }
    
    .static-page .news-content a {
        color: #3498db;
        text-decoration: none;
    }
    
    .static-page .news-content a:hover {
        text-decoration: underline;
    }
    
    .comment {
        margin-top: 30px;
        border-top: 1px solid #ddd;
        padding-top: 20px;
    }
    
    .comment .fb-comments {
        margin: 0 auto;
        max-width: 100%;
    }
</style>
@endsection