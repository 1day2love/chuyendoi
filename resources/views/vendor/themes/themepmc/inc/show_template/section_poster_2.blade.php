<div class="block">
    <div class="heading">
        <a href="{{ $item['link'] ?? '/' }}">
            <h2 class="caption">{{ $item['label'] }}</h2>
        </a>
        @if ($item['link'])
            <a class="see-more" href="{{ $item['link'] }}">Xem tất cả<i class="fa fa fa-caret-right"></i></a>
        @endif
        <ul class="sub-heading">
                            <li><a rel="nofollow" class="tab-filter" data-href="phim-2025" href="javascript:void(0)">2025</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="phim-2024" href="javascript:void(0)">2024</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="phim-2023" href="javascript:void(0)">2023</a></li>
                            <li><a rel="nofollow" class="tab-filter" data-href="phim-2022" href="javascript:void(0)">2022</a></li>
                            </ul>
    </div>
    <ul class="list-film horizontal">
        @foreach ($item['data'] as $movie)
            @if ($loop->first)
                <li class="item large">
                    @if ($movie->status == 'trailer')
                        <span class="label">{{$movie->episode_current}} {{$movie->quality}}</span>
                    @elseif ($movie->type == 'single')
                        <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->type == 'series')
                        @if ($movie->status == 'ongoing')
                            <span class="label">{{$movie->episode_current}} {{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->status == 'completed')
                        <span class="label">{{$movie->episode_current}}</span>
                        @else
                            <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @endif
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
                        <span class="label">{{$movie->episode_current}} {{$movie->quality}}</span>
                    @elseif ($movie->type == 'single')
                        <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->type == 'series')
                        @if ($movie->status == 'ongoing')
                            <span class="label">{{$movie->episode_current}} {{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->status == 'completed')
                        <span class="label">{{$movie->episode_current}}</span>
                        @else
                            <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @endif
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