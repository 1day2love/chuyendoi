@extends('themes::themepmc.layout')
<title>Điều Khoản Sử Dụng</title>
@section('content')
<div class="main-content" id="static-page">
                <div class="container">
                    <div class="static-page">
                        <div class="title">
                            <h1><strong>Điều Khoản Sử Dụng</strong></h1> </div>
                        <div class="news-content">
                            <p>Điều khoản sử dụng là những quy định và điều kiện mà bạn phải tuân theo khi sử dụng website. Bằng việc truy cập và sử dụng website này, bạn đồng ý rằng bạn đã đọc, hiểu và chấp nhận các điều khoản sử dụng này. Nếu bạn không đồng ý với bất kỳ điều khoản nào, xin vui lòng không sử dụng website này.</p>
                            <p>&nbsp;</p>
                            <p><strong>Điều khoản sử dụng bao gồm những nội dung sau:</strong></p>
                            <p>&nbsp;</p>
                            <ul>
                                <li>1.Giới thiệu về website, mục đích và cách thức hoạt động của nó.</li>
                                <li>2.Quyền và trách nhiệm của người sử dụng, bao gồm việc tôn trọng bản quyền, không sử dụng website cho các mục đích bất hợp pháp hoặc vi phạm quyền lợi của người khác, không gửi hoặc truyền các nội dung có hại, xúc phạm, quấy rối, lừa đảo hoặc vi phạm luật pháp.</li>
                                <li>3.Quyền và trách nhiệm của chủ sở hữu website, bao gồm việc cung cấp và duy trì website, không chịu trách nhiệm về các nội dung do người dùng hoặc bên thứ ba cung cấp, có quyền thay đổi hoặc ngừng cung cấp website hoặc một phần của nó mà không cần thông báo trước.</li>
                                <li>4.Các điều khoản khác liên quan đến việc giải quyết tranh chấp, áp dụng luật pháp và giới hạn trách nhiệm.</li>
                            </ul>
                            <p>&nbsp;</p>
                            <p>Bạn có thể xem chi tiết các chính sách quyền riêng tư <a href="/post/privacy">tại đây</a>. Chúng tôi khuyên bạn nên kiểm tra lại các điều khoản sử dụng thường xuyên để cập nhật những thay đổi mới nhất. Nếu bạn có bất kỳ câu hỏi hoặc ý kiến nào về các điều khoản sử dụng, xin vui lòng <a href="../post/contact">Liên hệ ngay</a> với chúng tôi. Chúng tôi rất mong nhận được phản hồi của bạn.</p>
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
