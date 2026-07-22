<div class="block">
    <div class="heading">
        <a href="{{ $item['link'] ?? '/' }}">
            <h2 class="caption">{{ $item['label'] }}</h2>
        </a>
        @if ($item['link'])
            <a class="see-more" href="{{ $item['link'] }}">Xem tất cả<i class="fa fa fa-caret-right"></i></a>
        @endif
        <ul class="sub-heading">
                            <li><a rel="nofollow" class="tab-filter" data-href="hanh-dong" href="javascript:void(0)">Hành động</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="hoat-hinh" href="javascript:void(0)">Hoạt hình</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="kinh-di" href="javascript:void(0)">Kinh dị</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="hai-huoc" href="javascript:void(0)">Hài hước</a></li>
                            </ul>
    </div>
    <ul class="list-film horizontal">
        @foreach ($item['data'] as $movie)
            @if ($loop->first)
                <li class="item large">
                    @if ($movie->status == 'trailer')
                    <span class="label">{{$movie->episode_current}} {{$movie->quality}}</span>
                    @else
                    <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @endif
                    <a title="{{ $movie->name }} - {{ $movie->origin_name }}" href="{{ $movie->getUrl() }}">
                        <img width="485px" height="273px" class="img-1 lazyload"
                            alt="{{ $movie->name }} - {{ $movie->origin_name }}"
                            data-src="{{ $movie->getPosterUrl() }}" />
                        <p>{{ $movie->name }}</p> <i class="icon-play"></i>
                    </a>
                </li>
            @else
                <li class="item small">
                    @if ($movie->status == 'trailer')
                    <span class="label">{{$movie->episode_current}}</span>
                    @else
                    <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @endif
                    <a title="{{ $movie->name }} - {{ $movie->origin_name }}" href="{{ $movie->getUrl() }}">
                        <img width="238px" height="134px" class="img-2 lazyload"
                            alt="{{ $movie->name }} - {{ $movie->origin_name }}"
                            data-src="{{ $movie->getPosterUrl() }}"
                            src="{{ $movie->getPosterUrl() }}" />
                        <p>{{ $movie->name }}</p>
                        <i class="icon-play"></i>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
