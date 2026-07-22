<div class="block">
    <div class="heading">
        <a href="{{ $item['link'] ?? '/' }}">
            <h2 class="caption">{{ $item['label'] }}</h2>
        </a>
        @if ($item['link'])
            <a class="see-more" href="{{ $item['link'] }}">Xem tất cả<i class="fa fa fa-caret-right"></i></a>
        @endif
    </div>
    <ul class="list-film horizontal">
        @foreach ($item['data'] as $movie)
            @if ($loop->first)
                <li class="item large">
                    @if ($movie->status == 'trailer')
                    <span class="label">Sắp Chiếu</span>
                    @elseif ($movie->type === 'single')
                    <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->type === 'series')
                    <span class="label">{{$movie->episode_current}}</span>
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
                    <span class="label">Sắp Chiếu</span>
                    @elseif ($movie->type === 'single')
                    <span class="label">{{$movie->quality}} {{$movie->language}}</span>
                    @elseif ($movie->type === 'series')
                    <span class="label">{{$movie->episode_current}}</span>
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
