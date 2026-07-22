@extends('themes::themepmc.layout')
<title>Chính Sách Riêng Tư</title>
@section('content')
<div class="main-content" id="static-page">
                <div class="container">
                    <div class="static-page">
                        <div class="title">
                            <h1><strong>Chính Sách Riêng Tư</strong></h1> </div>
                        <div class="news-content">
                            <h2>Cookies</h2>
                            <p>Cũng như nhiều website khác, chúng tôi thiết lập và sử dụng cookie để tìm hiểu thêm về cách bạn tương tác với nội dung của chúng tôi và giúp chúng tôi cải thiện trải nghiệm của bạn khi ghé thăm website của chúng tôi, cũng như duy trì thiết lập cá nhân của bạn... Website của chúng tôi có thể đăng quảng cáo, và trong trường hợp đó có thể thiết lập và truy cập các cookie trên máy tính của bạn và phụ thuộc vào chính sách bảo vệ sự riêng tư của các bên cung cấp quảng cáo. Tuy nhiên, các công ty quảng cáo không được truy cập vào cookie của chúng tôi. Những công ty đó thường sử dụng các đoạn mã riêng để theo dõi số lượt truy cập của bạn đến website của chúng tôi.</p>
                            <h2>Thay đổi điều khoản</h2>
                            <p>Chúng tôi có thể thay đổi chính sách bảo mật này theo thời gian. Nếu chúng tôi có bất kỳ thay đổi nào quan trọng cho chính sách bảo mật này và cách mà chúng tôi sử dụng dữ liệu cá nhân của bạn, chúng tôi sẽ đăng những thay đổi này trên trang web này và sẽ cố gắng thông báo cho bạn về bất kỳ thay đổi quan trọng nào. Vui lòng kiểm tra lại chính sách bảo mật của chúng tôi một cách thường xuyên.</p>
                            <h2>Thu thập thông tin không phải cá nhân</h2>
                            <p>Chúng tôi có thể tự động thu thập thông tin không phải cá nhân về bạn như loại trình duyệt internet mà bạn sử dụng hoặc website mà bạn đã truy cập trước khi đến website của chúng tôi. Chúng tôi cũng có thể tổng hợp thông tin chi tiết mà bạn đã gửi cho website (ví dụ, tuổi của bạn và thành phố nơi bạn sinh sống).</p>
                            <h2>Từ chối bảo đảm</h2>
                            <p>Mặc dù Chính sách bảo vệ riêng tư đặt ra những tiêu chuẩn về Dữ liệu và chúng tôi luôn cố gắng hết mình để đáp ứng, chúng tôi không bị buộc phải bảo đảm những tiêu chuẩn đó. Có thể có những nhân tố vượt ra ngoài tầm kiểm soát của chúng tôi có thể dẫn đến việc Dữ liệu bị tiết lộ. Vì thế, chúng tôi không chịu trách nhiệm bảo đảm Dữ liệu luôn được duy trì ở tình trạng hoàn hảo hoặc không bị tiết lộ.</p>
                            <h2>Sự đồng ý của bạn</h2>
                            <p>Khi sử dụng dịch vụ của chúng tôi, bạn mặc nhiên chấp nhận điều khoản trong Chính sách bảo vệ riêng tư này.</p>
                           
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