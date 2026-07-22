@extends('themes::themepmc.layout')

@php
    $years = Cache::remember('all_years', \Backpack\Settings\app\Models\Setting::get('site_cache_ttl', 5 * 60), function () {
        return \Ophim\Core\Models\Movie::where('is_published', true)
            ->select('publish_year')
            ->distinct()
            ->pluck('publish_year')
            ->sortDesc();
    });
@endphp

@section('content')
    <div class="container filter-page">
        <div class="block">
            @include('themes::themepmc.inc.catalog_filter')
            <div class="text"
                style="margin: 0 0 10px 0;overflow: hidden;padding: 5px 10px;list-style: none;background-color: #302e2e;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;">
                <div class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                    <li itemscope itemtype="http://schema.org/ListItem">
                        <a itemprop="item" itemprop="url" href="/" title="Trang Chủ">
                            <span itemprop="name">
                                <i class="fa fa-home"></i> Phim Mới <i class="fa fa-angle-right"></i>
                            </span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <li>{{ $section_name ?? 'Danh Sách Phim' }}</li>
                      </div><div class="clear "></div> 
                <div class="des ">Cùng khám phá<a href=""><strong> {{ $section_name ?? '' }}</strong></a> mới nhất và hấp dẫn, cập nhật liên tục trên  {{ request()->getHost() }}. Xem và tải xuống hơn 69.6969+ bộ {{ $section_name ?? '' }} Vietsub, thuyết minh đang thịnh hành và hay nhất.</div> </div> <div class="clear "></div>
            </div>

            <div class="clear"></div>

            <div id="binlist">
                <ul class="list-film horizontal">
                    @if (count($data))
                        @foreach ($data as $movie)
                            <li class="item small">
                                <span class="label">
                                    @if ($movie->status == 'trailer')
                                        <div class="status">{{ $movie->episode_current }}</div>
                                    @elseif ($movie->type == 'single')
                                        <div class="status">{{ $movie->quality }} {{ $movie->language }} </div>
                                    @elseif ($movie->type == 'series')
                                       <div class="status"> {{ $movie->episode_current }} {{ $movie->language }} </div>
                                    @endif
                                </span>
                                <a title="{{ $movie->name }} - {{ $movie->origin_name }}" href="{{ $movie->getUrl() }}"
                                    style="height: 133.875px;">
                                    <img alt="{{ $movie->name }} - {{ $movie->origin_name }}"
                                        src="{{ $movie->getPosterUrl() }}">
                                    <h3>{{ $movie->name }}</h3> <i class="icon-play"></i>
                                </a>
                            </li>
                        @endforeach
                    @else
                       <p>Không có phim nào cho mục này! Bạn có thể tìm bằng tên tiếng anh hoặc từ khóa không dấu.</p>
                        <p>Hãy thử tìm kiếm trên google với cú pháp: Tên phim + {{ request()->getHost() }}</p>
                    @endif

                </ul>
                <div class="clear"></div>
                <div class="pagination">
                    {{ $data->appends(request()->all())->links('themes::themepmc.inc.pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection