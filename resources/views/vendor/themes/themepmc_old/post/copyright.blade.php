@extends('themes::themepmc.layout')
<title>Bản quyền và trách nhiệm nội dung</title>
@section('content')
<div class="main-content" id="static-page">
                <div class="container">
                    <div class="static-page">
                        <div class="title">
                            <h1><strong>Bản quyền và trách nhiệm nội dung</strong></h1> </div>
                        <div class="news-content">
                            <h2>1.Trách nhiệm nội dung</h2>
                            <p>Chúng tôi không lưu trữ hay đăng tải phim lên hệ thống. Tất cả phim đều được sưu tầm, sử dụng máy học và Ai để thu thập từ nhiều nguồn được chia sẻ trên các nền tảng internet, không có ngoại lệ nào. Chúng tôi sẽ thường xuyên kiểm tra các nội dung và loại bỏ các nội dung vi phạm, quảng cáo, spam, nội dung xúc phạm hay các nội dung không phù hợp, vi phạm pháp luật.</p>
                            <h2>2.Bản quyền</h2>
                            <p>Chúng tôi sẽ không chịu trách nhiệm đối với bất kì nội dung nào được đăng tải trên trang website này. Là trang website giải trí, chúng tôi không thể cam kết chắc chắn rằng có thể kiểm soát mọi thông tin trên trang web. Do đó có thể vi phạm bản quyền từ các nhà sản xuất hoặc các bên sở hữu bản quyền khác. Bất cứ hành vi xâm phạm nào nếu được báo cáo sẽ bị gỡ bỏ khỏi trang web trong thời gian sớm nhất.</p>
                            <h2>3.Quy trình báo cáo vi phạm bản quyền</h2>
                            <p>Nếu có bất cứ vi phạm nào về bản quyền mà bạn là người sở hữu bản quyền đó, hãy <a href="../post/contact">Liên hệ ngay</a> với chúng tôi qua email. Sau khi xác minh tính xác thực của thông tin mà bạn cung cấp, chúng tôi sẽ xử lý thông báo và gỡ bỏ nội dung vi phạm khỏi website ngay lập tức theo quy định.</p>
                            <p>&nbsp;</p>
                            <p>Chúng tôi mong muốn hợp tác với các bên sở hữu bản quyền để giải quyết các vấn đề liên quan đến việc vi phạm bản quyền một cách hiệu quả và minh bạch. Chúng tôi cảm ơn sự thông cảm và hỗ trợ của bạn.</p>
                           
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
