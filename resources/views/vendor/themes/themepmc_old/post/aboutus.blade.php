@extends('themes::themepmc.layout')
<title>Về Chúng Tôi</title>
@section('content')
<div class="main-content" id="static-page">
                <div class="container">
                    <div class="static-page">
                        <div class="title">
                        <h1><strong>Về Chúng Tôi</strong></h1> </div>
                        <div class="news-content">
                            <p>Chào mừng bạn đến với <a href="../">Phimtvi</a>, trang web tuyệt vời để thỏa mãn niềm đam mê với điện ảnh và thư giãn sau những ngày làm việc căng thẳng!</p>
                            <p>&nbsp;</p>
                            <p> </p>
                            <p><strong>Phimtvi</strong> là một website xem phim trực tuyến hoàn toàn miễn phí, nơi bạn có thể khám phá và tận hưởng hàng ngàn bộ phim hấp dẫn từ nhiều thể loại khác nhau. Chúng tôi tập trung vào việc mang đến cho bạn những trải nghiệm giải trí đỉnh cao, đáp ứng mọi sở thích và độ tuổi.</p>
                            <p><img style="display: block; margin-left: auto; margin-right: auto;" src="../1day2love/logoimg.png" alt="Logo" width="215" height="54" />
                            <p>&nbsp;</p>
                            <p>Với <a href="../">Phimtvi</a>, bạn sẽ tìm thấy một bộ sưu tập phong phú với hàng nghìn bộ phim từ cổ điển đến hiện đại, từ hành động, phiêu lưu đến tình cảm, hài hước hay kinh dị. Chúng tôi liên tục cập nhật và thêm mới những bộ phim hot nhất, giúp bạn không bao giờ bị lạc hậu với các xu hướng điện ảnh mới nhất.</p>
                            <p>&nbsp;</p>
                            <p>Đặc biệt, <a href="../">Phimtvi</a> là địa điểm lý tưởng cho những tín đồ của điện ảnh Việt Nam. Bạn sẽ tìm thấy những bộ phim bom tấn của làng phim Việt, cùng với những tác phẩm độc lập và phim ngắn tuyệt vời từ các nhà làm phim trẻ tài năng. Chúng tôi tự hào là một nền tảng ủng hộ sự phát triển và lan tỏa của ngành công nghiệp điện ảnh Việt Nam.</p>
                            <p>Sự an toàn và bảo mật thông tin cá nhân của bạn là ưu tiên hàng đầu của chúng tôi. Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn và không chia sẻ thông tin này với bất kỳ bên thứ ba nào.</p>
                            <p>&nbsp;</p>
                            <p>Hãy truy cập ngay <strong>{{ request()->getHost() }}</strong> &amp; bắt đầu cuộc hành trình khám phá vô tận thế giới điện ảnh ngay hôm nay!</p>
                    </div>
                <div class="comment">
               <!-- <div class="fb-comments" data-href="/post/contact" data-colorscheme="dark" data-width="980" data-order-by="reverse_time"></div> -->
                <div class="fb-comments" data-href="" data-colorscheme="dark" data-width="980" data-order-by="reverse_time"></div>
            </div>
            </div>
        </div>
        <!-- content -->
    </div>
@endsection
@push('scripts')
<script>
    var currentDomain = window.location.origin;
    var completeURL = currentDomain + "/post/contact";
    document.querySelector('.fb-comments').setAttribute('data-href', completeURL);
</script>
    {!! setting('site_scripts_facebook_sdk') !!}
@endpush
