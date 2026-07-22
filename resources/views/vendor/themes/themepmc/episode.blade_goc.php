@extends('themes::themepmc.layout')

@php
if ($currentMovie->status === 'trailer') {
    header("Location: /");
    exit;
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
             <div class="block-note">Truy cập <strong>{{ request()->getHost() }}</strong> &amp; <a target="_blank" rel="nofollow" href="/post/huong-dan-su-dung">Xem Hướng Dẫn</a> nếu bạn không thể truy cập được </div>
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
            <div class="quick-info-bar">
                <div class="info-left">
                    <div class="compact-rating">
                        <input id="hint_current" type="hidden" value="">
                        <input id="score_current" type="hidden" value="{{$currentMovie->getRatingStar()}}">
                        <div id="star" data-score="{{$currentMovie->getRatingStar()}}" style="cursor: pointer;"></div>
                        <div class="rating-info">
                            <span class="score">{{$currentMovie->getRatingStar()}}</span> điểm <span class="votes">({{$currentMovie->getRatingCount()}} lượt)</span> </div>
                    </div>
                        <button class="share-btn" onclick="toggleSharePopup(); return false;"> <i class="fa fa-share-alt"></i> <span>Chia sẻ</span> </button>
                        <div class="share-popup" id="sharePopup" style="display: none;">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $currentMovie->getUrl() }}" target="_blank" class="share-option facebook"> <i class="fa fa-facebook"></i> Facebook </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $currentMovie->getUrl() }}" target="_blank" class="share-option twitter"> <i class="fa fa-twitter"></i> Twitter </a>
                            <a href="https://t.me/share/url?url={{ $currentMovie->getUrl() }}" target="_blank" class="share-option telegram"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 496 512">
                                <path d="M248,8C111.03,8,0,119.03,0,256S111.03,504,248,504,496,392.97,496,256,384.97,8,248,8ZM362.13,164.11l-44,208.14c-3.32,15-12.09,18.66-24.5,11.63l-67.68-49.93-32.69,31.46c-3.61,3.61-6.63,6.63-13.56,6.63l4.87-69.05,125.69-113.39c5.46-4.87-1.18-7.59-8.46-2.72L169.79,281.14,103,260.36c-14.9-4.65-15.18-14.9,3.12-22l242.75-93.59C357.15,141.9,366.18,149,362.13,164.11Z"/>
                                </svg>Telegram</a>
                            <a href="javascript:void(0)" onclick="copyToClipboard('{{ $currentMovie->getUrl() }}'); return false;" class="share-option copy"> <i class="fa fa-copy"></i> Copy Link </a>
                        </div>
                    <span id="hint"></span>
                    <img class="hidden" itemprop="thumbnailUrl" src="{{ $currentMovie->getPosterUrl() }}"
                            alt="{{ $currentMovie->name }} {{ $currentMovie->origin_name }}">
                    <img class="hidden" itemprop="image" src="{{ $currentMovie->getPosterUrl() }}"
                            alt="{{ $currentMovie->name }} {{ $currentMovie->origin_name }}">
                    <span class="hidden" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                        <meta itemprop="ratingValue" content="{{ $currentMovie->getRatingStar() }}">
                        <meta itemprop="ratingCount" content="{{ $currentMovie->getRatingCount() }}">
                        <meta itemprop="bestRating" content="10" />
                        <meta itemprop="worstRating" content="1" />
                    </span>
                </div>
                <div class="info-right">
                        <!--    <button class="quick-nav-link off-ads" onclick="removeAds(); return false;" title="Tắt QC"> <i class="fa fa-ban" style="color:#ff9601"></i> <span>Tắt QC</span> </button> -->
                            <button class="quick-nav-link power-lamp" onclick="toggleLamp(); return false;" title="Tắt đèn"> <i class="fa fa-power-off"></i> <span class="text-lamp-inline">Tắt đèn</span> </button>
                            <button class="quick-nav-link error-play" title="Gửi báo lỗi phim {{ $currentMovie->name }} - {{ $currentMovie->origin_name }}" onclick="toggleError(); return false;" title="Báo lỗi"> <i class="fa fa-flag"></i> <span>Báo lỗi</span> </button>
                            <a href="javascript:void(0)" onclick="scrollToSection('.film-info'); return false;" class="quick-nav-link" title="Nội dung phim"> <i class="fa fa-file-text"></i> Nội dung </a>
                    </div>
                </div>
            <!-- notee -->
            <div class="film-note" style="margin-bottom:20px;border: 1px solid #B8B612;padding: 5px;">- Hãy thử đổi sang server khác (nếu có) hoặc tải lại trang 
            nếu bạn gặp lỗi, giật/lag khi xem phim.
            <br>- Tham gia nhóm <a href="https://t.me/+sVDHrwbvwxIwNWY1" target="_blank" rel="nofollow noopener">
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
        </div>
        <div id="pm-server">
            <center>
                <ul class="server-list">
                    <li class="backup-server"> <span class="server-title">Đổi Server</span>
                        <ul class="list-episode">
                            <ul class="list-episode"><li class="episode">@php $oneServerID=null;$prefixes=['PPRO#','PNA#','PVIP#','PHN#']; @endphp @foreach($currentMovie->episodes->where('slug',$episode->slug)->where('server',$episode->server) as $server)@php if(!$oneServerID){$oneServerID=$server->id;} $serverType=$prefixes[$loop->index]??'VIP#'; @endphp<a rel="nofollow" data-id="{{ $oneServerID }}" data-link="{{ $server->link }}" data-type="{{ $server->type }}" onclick="chooseStreamingServer(this)" class="streaming-server btn-link-backup btn-episode black episode-link">{{ $serverType.($loop->index+1) }}</a>@endforeach</li></ul>
                    </li>
                </ul>
            </center>
        </div>
        @if ($currentMovie->type !== 'single')
        <div class="episode-manager" id="episode-manager">
            <div class="episode-header">
                <div class="episode-info"> <i class="fa fa-film"></i> <span class="total-episodes">Đang tải...</span> </div>
                <div class="episode-tools">
                    <div class="search-box"> <i class="fa fa-search"></i>
                        <input type="text" id="episode-search" placeholder="Tìm tập..." /> </div>
                    <div class="sort-box">
                        <select id="episode-sort">
                            <option value="asc">Tập đầu → Cuối</option>
                            <option value="desc">Tập cuối → Đầu</option>
                        </select>
                </div>
                    <button class="view-toggle" id="view-toggle" title="Đổi kiểu hiển thị"> <i class="fa fa-th"></i> </button>
            </div>
        </div>
         <div class="list-server" id="list-server">
            @foreach ($currentMovie->episodes->sortBy([['server', 'asc']])->groupBy('server') as $server => $data) <!-- 'asc' sang 'DESC'  -->
                <div class="server-group clearfix mb-4" x-data="{ isOpen: true }">
                    <div  @click="isOpen = !isOpen" title="Bấm để mở rộng/thu gọn tập phim"><i class="fa fa-database"></i> Danh sách tập {{$server}}
                                        <div  class="fa fa-chevron-down" style="float: right;" :class="{ 'fa-chevron-up': isOpen, 'fa-chevron-down': !isOpen }"></div></div>
                    <ul x-show="isOpen" class="episodes px-4 py-2">
                        @foreach ($data->sortBy('name', SORT_NATURAL)->groupBy('name') as $name => $item)<li><a href="{{ $item->sortByDesc('type')->first()->getUrl() }}" title="Xem phim {{$currentMovie->name}} {{ (stripos($name, 'Tập') !== false) ? $name : 'Tập ' . $name }}" class="@if ($item->contains($episode)) active @endif">{{ (stripos($name, 'tập') !== false) ? $name : 'Tập ' . $name }}</a></li>
                        @endforeach</ul>
                </div>
            @endforeach
        </div>
            <div class="episode-pagination" id="episode-pagination" style="display: none;"> </div>
        </div>
        @endif
        @php
            use Illuminate\Support\Str;

            $serverCount = $currentMovie->episodes->pluck('server')->unique()->count();
        @endphp

        @if ($currentMovie->type === 'single' && $serverCount > 1)
        @php
             if (!function_exists('mapServerToQuality')) {
                function mapServerToQuality($serverName) {
                    $s = Str::lower(Str::ascii($serverName));

                    if (Str::contains($s, 'viet')) {
                        return ['Vietsub', 'Vietsub'];
                    }

                if (Str::contains($s, 'thuyet')) {
                    return ['Thuyetminh', 'Thuyết Minh'];
                }

                $sNoSpace = str_replace([' ', '-'], '', $s);
                if (Str::contains($sNoSpace, 'longtieng')) {
                    return ['Longtieng', 'Lồng Tiếng'];
                }

                return [null, null];
            }
        }

        [$currentQuality, $currentLabel] = mapServerToQuality($episode->server);

        $qualityButtons = [];

        foreach ($currentMovie->episodes->groupBy('server') as $serverName => $eps) {
            [$key, $label] = mapServerToQuality($serverName);

            if (!$key) continue;
            if (isset($qualityButtons[$key])) continue;

            $sameEp = $eps->firstWhere('slug', $episode->slug);

            if ($sameEp) {
                $url = $sameEp->getUrl();
            } else {
                $url = $eps->sortBy('name', SORT_NATURAL)->first()->getUrl();
            }

            $qualityButtons[$key] = [
                'label' => $label,
                'url'   => $url,
            ];
        }

        $order = ['Vietsub', 'Thuyetminh', 'Longtieng'];
        $qualityButtons = collect($qualityButtons)
            ->sortBy(function ($v, $k) use ($order) {
                $pos = array_search($k, $order, true);
                return $pos === false ? 999 : $pos;
            })
            ->toArray();
        @endphp

         @if (!empty($qualityButtons))
            <div class="quality-bar" id="quality-bar">
                <div id="quality-links" class="quality-buttons">
                    @foreach ($qualityButtons as $key => $item)
                        @php $isActive = ($key === $currentQuality); @endphp
                            <a href="{{ $isActive ? 'javascript:;' : $item['url'] }}"
                                class="btn-link-quality {{ $isActive ? 'active disabled' : '' }}"
                                data-quality-index="{{ $loop->index }}"
                                data-type="{{ $key }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
        <!-- Thông báo DMCA -->
                 @if ($currentMovie->regions()->where('region_id', 34)->exists() || $currentMovie->regions()->where('slug', 'viet-nam')->where('name', 'Việt Nam')->exists())
            <div class="film-note" style="margin-bottom:20px;border: 1px solid #B8B612;padding: 5px;">
                Nếu bộ phim này thuộc bản quyền của bạn. Hãy <a href="../post/contact">Liên hệ ngay</a> với chúng tôi qua email. Chúng tôi sẽ gỡ nó ngay lập tức. Xin cảm ơn. </div>
                 @endif
        <div style="clear:both;"></div>
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
            <!--
            <div class="comment" itemprop="comment">
                <div style="color:#000;padding: 0 10px;font-weight: bold;">Chia sẻ cảm nhận của bạn về bộ phim bằng cách để lại bình luận phía dưới!</div>
                <div class="fb-comments" data-href="{{ $customUrl }}" data-colorscheme="dark" data-width="100%" data-order-by="reverse_time"></div>
            </div>
            -->
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
     <div id="errorModal" class="err-modal" style="display:none;">
        <div class="err-dialog" role="dialog" aria-modal="true" aria-labelledby="errTitle">
           <button class="err-close" aria-label="Đóng" onclick="toggleError()">&times;</button>
                <h3 id="errTitle" class="err-title">Báo lỗi</h3>
                <p class="err-sub">Bạn đang gặp vấn đề gì?</p>
                    <div class="err-options" id="errOptions">
                        <!-- FA4: hình ảnh = picture-o; âm thanh = volume-up; cc = cc -->
                        <button type="button" class="err-opt" data-value="image">
                            <i class="fa fa-picture-o"></i> <span>Hình ảnh</span><i class="fa fa-check err-check"></i></button>
                        <button type="button" class="err-opt" data-value="audio">
                            <i class="fa fa-volume-up"></i><span>Âm thanh</span><i class="fa fa-check err-check"></i></button>
                        <button type="button" class="err-opt" data-value="subtitle">
                            <i class="fa fa-cc"></i><span>Ngôn ngữ / Phụ đề</span><i class="fa fa-check err-check"></i></button>
                   </div>
            <textarea id="errorText" class="err-text" placeholder="Mô tả chi tiết lỗi từ 2 kí tự (*)" rows="4"></textarea>
        <div class="err-actions">
            <button class="err-send" onclick="submitError()">Gửi đi</button>      
            <button class="err-cancel" onclick="toggleError()">Đóng</button>
        </div>
    </div>
</div>
<div class="clear"></div>
@endsection

@push('scripts')

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="/build/css/jwplay.css">
    <script src="/build/js/play.public.js" type="text/javascript"></script>
    <!--<script type="text/javascript" src="{{ asset('/build/js/play.min.js?v=3.4') }}"></script> -->
    <script type="text/javascript" src="{{ asset('/build/js/playx.min.js?v=3.4') }}"></script>
    <script src="/build/player/js/p2p-media-loader-core.min.js"></script>
    <script src="/build/player/js/p2p-media-loader-hlsjs.min.js"></script>
    <script src="/js/hls.min.js"></script>
    <script src="/js/jwplayer.hlsjs.min.js"></script>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.38.3/jwplayer.js"></script>
  <!--   <script src="//cdn.jsdelivr.net/npm/devtools-detector"></script> <script type="text/javascript"> if(typeof devtoolsDetector === "undefined") { location.reload(); } else { devtoolsDetector.launch(); devtoolsDetector.addListener(function (isOpen) { if (isOpen) { location.reload(); } }); }</script> -->
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

         async function renderPlayer(type, link, id) {
            const url = new URL(link);
            const { hostname, pathname } = url;
            let urlNoAds = link;
            //const playkk = [ "phim1280","kkphimplayer1", "kkphimplayer2", "kkphimplayer3","kkphimplayer4", "kkphimplayer5", "kkphimplayer6", "kkphimplayer7" ];
            const playkk = [ "vipp" ];
            const playop = [ "vipp" ];
            const shouldProcess = 
                (playkk.some(p => link.includes(p)) || playop.some(p => link.includes(p))) &&!link.includes("phimapi") && pathname.endsWith(".m3u8");
            if (shouldProcess) {
                if (playop.some(p => link.includes(p))) {
                    urlNoAds = `https://blocks.hlstream.com/stream?url=${encodeURIComponent(link)}`;
                } else if (playkk.some(p => link.includes(p))) {
                    urlNoAds = `/1day2love/play/embed.php?url=${encodeURIComponent(link)}`;
                }
                const data = await fetch(urlNoAds)
                .then(res => res.ok ? res.text() : res.json())
                .catch(err => {
                    console.error("Lỗi Fetch:", urlNoAds, err);
                    return null;
                });
                if (typeof data === "string") {
                    type = "m3u8";
                }
            }
            if (type == 'embed') {
                if (vastAds) {
                    wrapper.innerHTML = `<div id="fake_jwplayer"></div>`;
                    const fake_player = jwplayer("fake_jwplayer");
                    const objSetupFake = {
                        key: "{{ Setting::get('jwplayer_license') }}",
                        width: "100%",
                        aspectratio: "16:9",
                        file: "/build/player/1s_blank.mp4",
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
                    width: "100%",
                    aspectratio: "16:9",
                    image: "{{ $currentMovie->getPosterUrl() }}",
                    sources: [{
                        file: urlNoAds || link,
                        type: "hls",
                    }],
                    preload: true,
                    primary: "html5",
                    hlshtml: true, // Ép dùng HTML5 HLS
                    playbackRateControls: true,
                    playbackRates: [0.5, 0.75, 1, 1.5, 2],
                    volume: 100,
                    mute: false,
                    autostart: false,
                    //stretching: "exactfit",
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
                        "/build/js/screenshot.js": {
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
                        "Phim <strong>{{$currentMovie->name}} - {{$currentMovie->origin_name}} - {{ strpos($episode->name, 'Tập') !== false ? $episode->name : 'Tập ' . $episode->name }}</strong> bạn đang xem có thể đã bị lỗi, hãy <a href='https://t.me/+sVDHrwbvwxIwNWY1' target='_blank'><span style='color: red; font-weight: bold;'>Nhấn Vào Đây</span></a> để báo cho chúng mình qua Telegram để được sửa lỗi sớm nhất có thể. Cảm ơn<3"
                                       
                    );
                    
                });

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
    <script defer type="text/javascript" src="{{ asset('/build/libs/jquery-raty/jquery.raty.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('/build/js/public.phim.js?=1.1') }}"></script>
    <script defer type="text/javascript" src="/build/js/util.js"></script>

    <script src="/build/js/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
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