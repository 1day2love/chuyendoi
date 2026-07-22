@extends('themes::themepmc.layout')

@php
if ($currentMovie->status === 'trailer') {
    abort(404);
    }
@endphp

@php
    $customUrl = str_replace(parse_url($currentMovie->getUrl(), PHP_URL_HOST), 'phimtvi.net', $currentMovie->getUrl()); // Đổi domain plugin fb
@endphp
@section('content')
    <div class="container" id="page-player">
        <!-- Thông báo 
         <div class="block-note">Xem phim tại <strong>{{ request()->getHost() }}</strong> &amp; chia sẻ để ủng hộ tụi mình nhé! <3 </div>
         -->
             <div class="block-note">Hãy lưu bookmark &amp; truy cập <strong>{{ request()->getHost() }}</strong> nếu bạn không vào được {{ request()->getHost() }} nhé <3 </div>
             <!-- ADS -->
             <div id="chilladv" class="gnarty-offads">
             <div class="hidedesktop">
                        <div id="topplayermbads"></div>
             </div>
             <div class="hidemobile">
                        <div id="topplayerads"></div>
             </div>
        </div>
        <div class="breadcrumb" style="float: none;" itemscope itemtype="http://schema.org/BreadcrumbList">
             <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item"
                itemprop="url" href="/" title="Phimtvi"><span itemprop="name"><i class="fa fa-home"></i> Phimtvi <i class="fa fa-angle-right"></i></span></a>
                    <meta itemprop="position" content="1" />
                </li>
                @foreach ($currentMovie->categories as $category)
                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="{{ $category->getUrl() }}" title="{{ $category->name }}">
                            <span itemprop="name">
                                {{ $category->name }} <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                        <meta itemprop="position" content="2">
                    </li>
                @endforeach
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a
                        href="{{ $currentMovie->getUrl() }}" itemprop="item"><span
                            itemprop="name">{{ $currentMovie->name }} <i class="fa fa-angle-right"></i></span></a>
                    <meta itemprop="position" content="3" />
                </li>
               <li>{{ strpos($episode->name, 'Tập') !== false ? $episode->name : 'Tập ' . $episode->name }}</li>
                
            </div>
            <!-- Thông báo/ghi chú-->
                 @if ($currentMovie->notify && $currentMovie->notify != '')
                 @php
                     $checkhtml = strip_tags($currentMovie->notify) != $currentMovie->notify;
                @endphp
            <div class="block-note">
            @if($checkhtml)
                <span class="">{!! $currentMovie->notify !!}</span>
            @else
                <strong> Thông báo:</strong> <span class="text-white">{{ strip_tags($currentMovie->notify) }}</span>
            @endif
            </div>
                 @endif
                 <!-- Thông báo lịch chiếu -->
                @if (!empty($currentMovie->showtimes) && ($currentMovie->type != 'series' || $currentMovie->status != 'completed'))
            <div class="block-note">
                Lịch chiếu: <span class="">{{ strip_tags($currentMovie->showtimes) }}</span>
            </div>
                @endif
        <div class="box-player" id="box-player">
            <div id="player">
                <div id="player-area">
                </div>
            </div>
            <!-- notee -->
            <div class="film-note" style="margin-bottom:20px;border: 1px solid #B8B612;padding: 5px;">- Hãy thử đổi sang server khác (nếu có) hoặc tải lại trang 
            nếu bạn gặp lỗi, giật/lag khi xem phim.
            <br>- Tham gia nhóm <a href="https://t.me/+dsohJWvfjJc5NmQ1" target="_blank" rel="nofollow noopener">
                <span style="color:#e62117"><strong>Telegram</strong></span></a> của chúng mình để được hỗ trợ kịp thời khi gặp lỗi nha.
                </div>
                 <!-- end note  -->
                 <!-- ADS  -->
                 <div id="chilladv" class="gnarty-offads">
                        <div class="hidedesktop">
                            <div id="botplayeradsmb"></div>
                        </div>
                        <div class="hidemobile">
                            <div id="botplayerads"></div>
                        </div>
                </div>
            <div class="options">
                <ul class="tool">
                    <li class="off-ads"> <a href="javascript:()" id="btn-remove-ad" rel="nofollow" style="color: #ff9601;font-size: 20px;"  title="Tắt quảng cáo" >Tắt ADS <img src="/1day2love/dev/player/icon-noads.png" alt="off ads"/></a> </li>
                    <li class="error-play" id="report_play" data-toggle="modal" data-target="#report_error" title="Báo lỗi phim {{$currentMovie->name}} - {{$currentMovie->origin_name}}">
                        <span class="text-error">Báo lỗi</span>
                        <em class="radial-center"> 
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i></em></li>
                
                    <li class="power-lamp" title="Tắt đèn" >
                        <span class="text-lamp">Tắt đèn</span>
                        <em class="radial-center">
                            <i class="fa fa-power-off"></i>
                        </em>
                    </li>
                </ul>
            </div>
        </div>
        <div id="pm-server">
            <center>
                <ul class="server-list">
                    <li class="backup-server"> <span class="server-title">Đổi Server</span>
                        <ul class="list-episode">
                            <li class="episode">@php $oneServerID = null; @endphp @foreach ($currentMovie->episodes->where('slug', $episode->slug)->where('server', $episode->server) as $server) @php if (!$oneServerID) {$oneServerID = $server->id; } $serverType = ($loop->index + 1 === 1) ? 'PPRO#' : (($loop->index + 1 === 2) ? 'PNA#' : (($loop->index + 1 === 3) ? 'PVIP#' : (($loop->index + 1 === 4) ? 'PHN#' : 'VIP#'))); @endphp<a rel="nofollow" data-id="{{ $oneServerID }}" data-link="{{ $server->link }}"data-type="{{ $server->type }}" onclick="chooseStreamingServer(this)"class="streaming-server btn-link-backup btn-episode black episode-link">{{ $serverType . ($loop->index + 1) }}</a> @endforeach
                            </li>
                        </ul>
                    </li>
                </ul>
            </center>
        </div>
        <div class="list-server" id="list-server">
        @if ($currentMovie->type !== 'single' || $currentMovie->episodes->contains('server', '#zThuyết Minh') || $currentMovie->episodes->contains('server', 'Thuyết Minh') || $currentMovie->episodes->contains('server', 'Thuyết Minh #1') || $currentMovie->episodes->contains('server', '#2 Thuyết Minh') || $currentMovie->episodes->contains('server', '#Thuyết Minh') || $currentMovie->episodes->contains('server', '#zThuyết minh')|| $currentMovie->episodes->contains('server', '#2 Thuyết minh')|| $currentMovie->episodes->contains('server', '#2 Lồng Tiếng')|| $currentMovie->episodes->contains('server', '#2 Lồng tiếng'))
           @foreach ($currentMovie->episodes->sortBy([['server', 'asc']])->groupBy('server') as $server => $data) <!-- 'asc' sang 'DESC'  -->
                <div class="server-group clearfix" x-data="{ isOpen: true }" class="mb-4">
                    <div  @click="isOpen = !isOpen" title="Bấm để mở rộng/thu gọn tập phim"><i class="fa fa-database"></i> Danh sách tập {{$server}}
                                        <div  class="fa fa-chevron-down" style="float: right;" :class="{ 'fa-chevron-up': isOpen, 'fa-chevron-down': !isOpen }"></div></div>
                    <ul x-show="isOpen" class="episodes px-4 py-2">
                        @foreach ($data->sortBy('name', SORT_NATURAL)->groupBy('name') as $name => $item)<li><a href="{{ $item->sortByDesc('type')->first()->getUrl() }}" title="Xem phim {{$currentMovie->name}} {{ (stripos($name, 'Tập') !== false) ? $name : 'Tập ' . $name }}" class="@if ($item->contains($episode)) active @endif">{{ (stripos($name, 'tập') !== false) ? $name : 'Tập ' . $name }}</a></li>
                        @endforeach</ul>
                </div>
            @endforeach
            @endif
        </div>
        <!-- Thông báo DMCA -->
                 @if ($currentMovie->regions()->where('region_id', 34)->exists() || $currentMovie->regions()->where('slug', 'viet-nam')->where('name', 'Việt Nam')->exists())
            <div class="film-note" style="margin-bottom:20px;border: 1px solid #B8B612;padding: 5px;">
                Nếu bộ phim này thuộc bản quyền của bạn. Hãy <a href="../post/contact">Liên hệ ngay</a> với chúng tôi qua email. Chúng tôi sẽ gỡ nó ngay lập tức. Xin cảm ơn. </div>
                 @endif
        <div style="clear:both;"></div>
        <div class="box-rating">
            <input id="hint_current" type="hidden" value="">
            <input id="score_current" type="hidden" value="{{$currentMovie->getRatingStar()}}">
            <p>Đánh giá phim <span class="text">({{$currentMovie->getRatingStar()}}đ / {{$currentMovie->getRatingCount()}} lượt)</span></p>
            <div id="star" data-score="{{$currentMovie->getRatingStar()}}"
                style="cursor: pointer; float: left; width: 200px;">
            </div>
            <span id="hint"></span>
            <img class="hidden" itemprop="thumbnailUrl" src="{{ $currentMovie->getPosterUrl() }}"
                alt="{{ $currentMovie->name }} {{ $currentMovie->origin_name }}"> <img class="hidden" itemprop="image"
                src{{ $currentMovie->getPosterUrl() }}"
                alt="{{ $currentMovie->name }} {{ $currentMovie->origin_name }}">
            <span class="hidden" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"> <span
                    itemprop="ratingValue">5</span>
                <meta itemprop="ratingcount" content="{{$currentMovie->getRatingCount()}}">
                <meta itemprop="bestRating" content="10" />
                <meta itemprop="worstRating" content="1" />
            </span>
        </div>
        <div class="social">
            <div class="fb-like" style="margin:auto;width: 100%;" data-href="{{ $currentMovie->getUrl() }}"
                data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"
                data-colorscheme="light"></div>
            <div class="fb-save" data-uri="{{ $currentMovie->getUrl() }}"></div>
        </div>
        <div class="clear"></div>
        <div class="film-info">
            <h1 itemprop="name"><a title="Xem phim {{$currentMovie->name}}" href="">{{$currentMovie->name}}</a> -
               {{ strpos($episode->name, 'Tập') !== false ? $episode->name : 'Tập ' . $episode->name }}</h1>
            <h2 style="margin: 0px;font-size: 15px;"> <a title="{{$currentMovie->origin_name}}" href="{{$currentMovie->getUrl()}}"> {{$currentMovie->origin_name}} </a></h2>
            <img class="hidden" itemprop="thumbnailUrl"
                src="{{ $currentMovie->getPosterUrl() }}"
                alt="{{ $currentMovie->name }}-{{ $currentMovie->origin_name }}"> <img class="hidden" itemprop="image"
                src="{{ $currentMovie->getPosterUrl() }}"
                alt="{{ $currentMovie->name }}-{{ $currentMovie->origin_name }}">
            <p
                style="padding: 4px 4px;margin: 5px 0 20px 0;line-height: 26px;font-size: 12px;color: #BBB;background: #322b2b;">
                {!! mb_substr(strip_tags(trim($currentMovie->content)), 0, 260, 'UTF-8') !!}...
                [<a href="{{ $currentMovie->getUrl() }}"
                    title="{{ $currentMovie->name }} - {{ $currentMovie->origin_name }}">Xem thêm</a>]</p>

            <div class="comment" itemprop="comment">
                <div style="color:#000;padding: 0 10px;font-weight: bold;">Chia sẻ cảm nhận của bạn về bộ phim bằng cách để lại bình luận phía dưới!</div>
                <div class="fb-comments" data-href="{{ $customUrl }}" data-colorscheme="dark" data-width="100%" data-order-by="reverse_time"></div>
            </div>
            <div class="block film-related">
                <div class="heading">
                    <p class="caption">Có thể bạn cũng muốn xem</p>
                </div>
                <ul class="list-film horizontal top-slide" id="list-film-realted">
                    @foreach ($movie_related as $movie)
                        <li class="item ">
                            <span class="label"></span> <span class="label-quality">{{ $movie->publish_year }}</span>
                            <a title="{{ $movie->name }} - {{ $movie->origin_name }}" href="{{ $movie->getUrl() }}">
                                <img alt="{{ $movie->name }}" class="lazyload"
                                    data-src="{{ $movie->getPosterUrl() }}" />
                                <p>{{ $movie->name }}</p> <i class="icon-play"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
@endsection

@push('scripts')
<div id="report_error" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Báo Lỗi</h4>
                </div>
                <div class="modal-body" id="p_content">
                    
                    <div class="form-group">
                    <p class="alert-danger" id="show_msg"></p>
                        <label class="rp-err" for="log_des">Phim "{{ $currentMovie->name }} ({{ $currentMovie->origin_name }}) ({{ $currentMovie->publish_year }})"</label>
                        <textarea name="log_des" id="log_des" class="form-control" style="width:100%; height: 50px;"
                            placeholder="Viết đoạn ngắn mô tả lỗi bạn gặp phải của phim {{ $currentMovie->name }} ({{ $currentMovie->origin_name }}) ({{ $currentMovie->publish_year }})"required></textarea>
                    </div>
                    <label class="rp-err">Tham gia nhóm <a href="https://t.me/+dsohJWvfjJc5NmQ1" target="_blank" rel="nofollow noopener">
                    <span style="color:#e62117"><strong>Telegram</strong></span></a> để được hỗ trợ kịp thời nhất.</label></br>
                    <a href="javascript:;" class="btn btn-warning" name="but_send_report" id="but_send_report" onclick="submitReport()">Gửi</a>
                </div>
            </div>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="/1day2love/css/jwplay.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{ asset('/1day2love/js/play.min.js?v=3.4') }}"></script>
    <script src="/1day2love/dev/player/js/p2p-media-loader-core.min.js"></script>
    <script src="/1day2love/dev/player/js/p2p-media-loader-hlsjs.min.js"></script>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.27.1/jwplayer.js"></script>
    <script src="//cdn.jsdelivr.net/npm/devtools-detector"></script> <script type="text/javascript"> if(typeof devtoolsDetector === "undefined") { location.reload(); } else { devtoolsDetector.launch(); devtoolsDetector.addListener(function (isOpen) { if (isOpen) { location.reload(); } }); }</script>
    <script src="/js/hls.min.js"></script>
    <script src="/js/jwplayer.hlsjs.min.js"></script>
    <script>
        $(document).ready(function() {
            $('html, body').animate({
                scrollTop: $('#player-area').offset().top
            }, 'slow');
        });
    </script>

    <script>
        var episode_id = {{$episode->id}};
        const wrapper = document.getElementById('player-area');
        const vastAds = "{{ Setting::get('jwplayer_advertising_file') }}";

       function chooseStreamingServer(el) {
            const type = el.dataset.type;
            const link = el.dataset.link.replace(/^http:\/\//i, 'https://');
            const id = el.dataset.id;
            
            const newUrl =
                location.protocol +
                "//" +
                location.host +
                location.pathname.replace(`-${episode_id}`, `-${id}`);
                
            history.pushState({
                path: newUrl
            }, "", newUrl);
            episode_id = id;

            Array.from(document.getElementsByClassName('streaming-server')).forEach(server => {
                server.classList.remove('active');
            })
            el.classList.add('active');
            
            link.replace('http://', 'https://');
            renderPlayer(type, link, id);
        }

        function renderPlayer(type, link, id) {
            if (type == 'embed') {
                if (vastAds) {
                    wrapper.innerHTML = `<div id="fake_jwplayer"></div>`;
                    const fake_player = jwplayer("fake_jwplayer");
                    const objSetupFake = {
                        key: "{{ Setting::get('jwplayer_license') }}",
                        height: "100%",
                        width: "100%",
                        aspectratio: "16:9",
                        file: "/1day2love/dev/player/1s_blank.mp4",
                        volume: 100,
                        mute: false,
                        autostart: true,
                        stretching: "exactfit",
                        advertising: {
                            tag: "{{ Setting::get('jwplayer_advertising_file') }}",
                            client: "vast",
                            vpaidmode: "insecure",
                            skipoffset: {{ (int) Setting::get('jwplayer_advertising_skipoffset') ?: 5 }}, // Bỏ qua quảng cáo trong vòng 5 giây
                            skipmessage: "Bỏ qua sau xx giây",
                            skiptext: "Bỏ qua"
                        }
                    };
                    fake_player.setup(objSetupFake);
                    fake_player.on('complete', function(event) {
                        $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                    allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });

                    fake_player.on('adSkipped', function(event) {
                        $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                    allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });
                
                    fake_player.on('adComplete', function(event) {
                       $("#fake_jwplayer").remove();
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                    allowfullscreen="" allow='autoplay'></iframe>`
                        fake_player.remove();
                    });
                } else {
                    if (wrapper) {
                        wrapper.innerHTML = `<iframe width="100%" height="100%" src="${link}" frameborder="0" scrolling="no"
                    allowfullscreen="" allow='autoplay'></iframe>`
                    }
                }
                return;
            }

            if (type == 'm3u8' || type == 'mp4') {
                wrapper.innerHTML = `<div id="jwplayer"></div>`;
                const player = jwplayer("jwplayer");
                const objSetup = {
                    key: "{{ Setting::get('jwplayer_license') }}",
                    height: "100%",
                    width: "100%",
                    aspectratio: "16:9",
                    image: "{{ $currentMovie->getPosterUrl() }}",
                    sources: [{
                        file: link,
                        type: "hls"
                    }],
                    preload: true,
                    primary: "html5",
                    playbackRateControls: true,
                    playbackRates: [0.5, 0.75, 1, 1.5, 2],
                    sharing: {
                        sites: [
                            "reddit",
                            "facebook",
                            "twitter",
                            "googleplus",
                            "email",
                            "linkedin",
                        ],
                    },
                    volume: 100,
                    mute: false,
                    autostart: false,
                    stretching: "exactfit",
                    logo: {
                        file: "{{ Setting::get('jwplayer_logo_file') }}",
                        link: "{{ Setting::get('jwplayer_logo_link') }}",
                        position: "{{ Setting::get('jwplayer_logo_position') }}",
                    },
                    advertising: {
                        tag: "{{ Setting::get('jwplayer_advertising_file') }}",
                        client: "vast",
                        vpaidmode: "insecure",
                        skipoffset: {{ (int) Setting::get('jwplayer_advertising_skipoffset') ?: 5 }}, // Bỏ qua quảng cáo trong vòng 5 giây
                        skipmessage: "Bỏ qua sau xx giây",
                        skiptext: "Bỏ qua"
                    },
                    //tracks: [{
                    //    file: "https://phimtvchill.net/1day2love/ads/VietNam.srt",
                    //    label: "Tiếng Việt",
                    //    kind: "captions",
                    //    "default": true
                   // }],
                    plugins: {
                        "/1day2love/js/screenshot.js": {
                        enabled: true,
                        name: "screenshot"
                        }
                    }
                };

                if (type == 'm3u8') {
                    const segments_in_queue = 50;

                    var engine_config = {
                        debug: !1,
                        segments: {
                            forwardSegmentCount: 50,
                        },
                        loader: {
                            cachedSegmentExpiration: 864e5,
                            cachedSegmentsCount: 1e3,
                            requiredSegmentsPriority: segments_in_queue,
                            httpDownloadMaxPriority: 9,
                            httpDownloadProbability: 0.06,
                            httpDownloadProbabilityInterval: 1e3,
                            httpDownloadProbabilitySkipIfNoPeers: !0,
                            p2pDownloadMaxPriority: 50,
                            httpFailedSegmentTimeout: 500,
                            simultaneousP2PDownloads: 20,
                            simultaneousHttpDownloads: 2,
                            // httpDownloadInitialTimeout: 12e4,
                            // httpDownloadInitialTimeoutPerSegment: 17e3,
                            httpDownloadInitialTimeout: 0,
                            httpDownloadInitialTimeoutPerSegment: 17e3,
                            httpUseRanges: !0,
                            maxBufferLength: 300,
                            // useP2P: false,
                        },
                    };
                   // if (Hls.isSupported() && p2pml.hlsjs.Engine.isSupported()) {
                   //     var engine = new p2pml.hlsjs.Engine(engine_config);
                  //     player.setup(objSetup);
                 //      jwplayer_hls_provider.attach();
                 //       p2pml.hlsjs.initJwPlayer(player, {
                 //          liveSyncDurationCount: segments_in_queue, // To have at least 7 segments in queue
                 //           maxBufferLength: 300,
                 //           loader: engine.createLoaderClass(),
                  //      });
                  //  } else {
                        player.setup(objSetup);
                   // }
                } else {
                    player.setup(objSetup);
                }
                player.addButton('<svg xmlns="http://www.w3.org/2000/svg" class="jw-svg-icon jw-svg-icon-rewind" viewBox="0 0 1024 1024" focusable="false"><path d="M561.948444 262.712889l67.015112 79.644444 206.961777-174.08-56.832-38.627555a468.48 468.48 0 1 0 201.216 328.817778l-103.310222 13.141333a364.487111 364.487111 0 0 1-713.557333 139.605333 364.373333 364.373333 0 0 1 479.971555-435.541333l14.904889 5.973333-96.369778 81.066667zM329.955556 379.505778h61.610666v308.167111H329.955556zM564.167111 364.088889c61.269333 0 110.933333 45.511111 110.933333 101.717333v135.566222c0 56.149333-49.664 101.660444-110.933333 101.660445s-110.933333-45.511111-110.933333-101.660445V465.749333c0-56.149333 49.664-101.660444 110.933333-101.660444z m0 56.490667c-27.249778 0-49.322667 20.252444-49.322667 45.226666v135.566222c0 24.974222 22.072889 45.169778 49.322667 45.169778 27.192889 0 49.265778-20.195556 49.265778-45.169778V465.749333c0-24.917333-22.072889-45.169778-49.265778-45.169777z"></path></svg>', "Tua tới 10 giây", () => player.seek(player.getPosition() + 10), "Forward 10 Seconds");
                player.addButton('<svg xmlns="http://www.w3.org/2000/svg" class="jw-svg-icon jw-svg-icon-rewind" viewBox="0 0 1024 1024" focusable="false"><path d="M455.68 262.712889l-67.072 79.644444-206.904889-174.08 56.775111-38.627555a468.48 468.48 0 1 1-201.216 328.817778l103.310222 13.141333a364.487111 364.487111 0 0 0 713.614223 139.605333 364.373333 364.373333 0 0 0-479.971556-435.541333l-14.904889 5.973333 96.312889 81.066667zM329.955556 379.505778h61.610666v308.167111H329.955556zM564.167111 364.088889c61.269333 0 110.933333 45.511111 110.933333 101.717333v135.566222c0 56.149333-49.664 101.660444-110.933333 101.660445s-110.933333-45.511111-110.933333-101.660445V465.749333c0-56.149333 49.664-101.660444 110.933333-101.660444z m0 56.490667c-27.249778 0-49.322667 20.252444-49.322667 45.226666v135.566222c0 24.974222 22.072889 45.169778 49.322667 45.169778 27.192889 0 49.265778-20.195556 49.265778-45.169778V465.749333c0-24.917333-22.072889-45.169778-49.265778-45.169777z"></path></svg>', "Tua lại 10 giây", () => player.seek(player.getPosition() - 10), "Rewind 10 Seconds");

                const resumeData = 'CMS-PlayerPosition-' + id;
            
                player.on('error', function() {
                    // Thông báo lỗi player
                     fx.messageBox(
                        "Lỗi",
                        "Phim <strong>{{$currentMovie->name}} - {{$currentMovie->origin_name}} - {{ strpos($episode->name, 'Tập') !== false ? $episode->name : 'Tập ' . $episode->name }}</strong> bạn đang xem có thể đã bị lỗi, hãy <a href='https://t.me/+dsohJWvfjJc5NmQ1' target='_blank'><span style='color: red; font-weight: bold;'>Nhấn Vào Đây</span></a> để báo cho chúng mình qua Telegram để được sửa lỗi sớm nhất có thể. Cảm ơn<3",
                                       
                    );
                    
                });
                // Function để mở link Telegram trong tab mới
                function openTelegram() {
                    window.open("https://t.me/+dsohJWvfjJc5NmQ1", "_blank");
                }

                player.on('ready', function() {
                    if (typeof(Storage) !== 'undefined') {
                        if (localStorage[resumeData] == '' || localStorage[resumeData] == 'undefined') {
                            console.log("No cookie for position found");
                            var currentPosition = 0;
                        } else {
                            if (localStorage[resumeData] == "null") {
                                localStorage[resumeData] = 0;
                            } else {
                                var currentPosition = localStorage[resumeData];
                            }
                            console.log("Position cookie found: " + localStorage[resumeData]);
                        }
                        player.once('play', function() {
                            console.log('Checking position cookie!');
                            console.log(Math.abs(player.getDuration() - currentPosition));
                            if (currentPosition > 180 && Math.abs(player.getDuration() - currentPosition) >
                                5) {
                                player.seek(currentPosition);
                            }
                        });
                        window.onunload = function() {
                            localStorage[resumeData] = player.getPosition();
                        }
                    } else {
                        console.log('Your browser is too old!');
                    }
                });

                player.on('complete', function() {
                    if (typeof(Storage) !== 'undefined') {
                        localStorage.removeItem(resumeData);
                    } else {
                        console.log('Your browser is too old!');
                    }
                })

                function formatSeconds(seconds) {
                    var date = new Date(1970, 0, 1);
                    date.setSeconds(seconds);
                    return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
                }
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const episode = '{{$episode->id}}';
            let playing = document.querySelector(`[data-id="${episode}"]`);
            if (playing) {
                playing.click();
                return;
            }

            const servers = document.getElementsByClassName('streaming-server');
            if (servers[0]) {
                servers[0].click();
            }
        });
    </script>

    <script>
        var rated = false;
        var URL_POST_RATING = '{{ route('movie.rating', ['movie' => $currentMovie->slug]) }}';
        var URL_POST_REPORT = '{{ route('episodes.report', ['movie' => $currentMovie->slug, 'episode' => $episode->slug, 'id' => $episode->id]) }}';
    </script>
    <script defer type="text/javascript" src="{{ asset('/1day2love/dev/libs/jquery-raty/jquery.raty.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('/1day2love/dev/js/public.phim.js') }}"></script>
    <script defer type="text/javascript" src="/1day2love/js/util.js"></script>

    <script src="/1day2love/dev/js/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".power-lamp").click(function() {
                var $overlay = '<div id="background_lamp"></div>';
                if ($(this).hasClass('off')) {
                    $(this).removeClass('off');
                    $(".text-lamp").text('Tắt đèn');
                    $("#background_lamp").remove();
                } else {
                    $(this).addClass('off');
                    $(".text-lamp").text('Bật đèn');
                    $('body').append($overlay);
                }
            });
            $("#list-film-realted").owlCarousel({
                items: 5,
                itemsTablet: [700, 3],
                itemsMobile: [479, 2],
                scrollPerPage: true,
                navigation: true,
                slideSpeed: 800,
                paginationSpeed: 400,
                stopOnHover: true,
                pagination: false,
                autoPlay: 8000,
                lazyLoad: true,
                navigationText: ['<i class="fa fa fa-caret-left"></i>',
                    '<i class="fa fa fa-caret-right"></i>'
                ],
            });
        })
    </script>
    <!-- Remove ads Script -->
    <script>
            $(document).ready(function() {
                jQuery("#btn-remove-ad").on("click", function() {
                    return jQuery("div.gnarty-offads").remove(), jQuery(this).remove(), !1
                })
            });
        </script>
    {!! setting('site_scripts_facebook_sdk') !!}
@endpush