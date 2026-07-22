@extends(backpack_view('blank'))

@php
    use Ophim\Core\Models\Movie;
    $widgets['before_content'] = [
        [
            'type' => 'alert',
            'class' => 'alert alert-dark mb-2 col-12',
            'heading' => '1day2love.',
            'content' =>
                '
                Phiên bản: <span class="text-danger text-break">' .
                config('ophim.version') .
                '</span><br/>
            ',
            'close_button' => true, // show close button or not
        ],
    ];
@endphp

@section('content')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"
        integrity="sha512-d8F1J2kyiRowBB/8/pAWsqUl0wSEOkG5KATkVV4slfblq9VRQ6MyDZVxWl2tWd+mPhuCbpTB4M7uU/x9FlgQ9Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 10,
                time: 500
            });
        });
    </script>

    @php
        $mostViewedByPeriod = [
            'day' => collect($top_view_day ?? [])->take(10)->values(),
            'week' => collect($top_view_week ?? [])->take(10)->values(),
            'month' => collect($top_view_month ?? [])->take(10)->values(),
        ];
        $recentlyUpdatedMovies = Movie::orderByDesc('updated_at')->take(15)->get();
    @endphp

    <style>
        .card-counter {
            box-shadow: 2px 2px 10px #dadada;
            margin: 5px 0;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }

        .card-counter:hover {
            box-shadow: 4px 4px 20px #dadada;
            transition: .3s linear all;
        }

        .card-counter.primary {
            background-color: #007bff;
            color: #fff;
        }

        .card-counter.danger {
            background-color: #ef5350;
            color: #fff;
        }

        .card-counter.success {
            background-color: #66bb6a;
            color: #fff;
        }

        .card-counter.info {
            background-color: #26c6da;
            color: #fff;
        }

        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }

        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }

        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }

        .dashboard-panel {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .dashboard-panel .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 16px 20px;
        }

        .dashboard-panel .card-title {
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .dashboard-panel .card-subtitle {
            color: #6b7280;
            font-size: 12px;
            margin-top: 2px;
        }

        .dashboard-list {
            max-height: 720px;
            overflow-y: auto;
        }

        .dashboard-list-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .dashboard-list-item:last-child {
            border-bottom: 0;
        }

        .dashboard-rank {
            min-width: 24px;
            font-weight: 800;
            font-size: 18px;
            color: #f97316;
            text-align: center;
        }

        .dashboard-poster {
            width: 44px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: #e5e7eb;
        }

        .dashboard-content {
            min-width: 0;
            flex: 1;
        }

        .dashboard-name {
            display: block;
            color: #111827;
            font-weight: 600;
            line-height: 1.35;
            text-decoration: none;
            margin-bottom: 4px;
        }

        .dashboard-name:hover {
            color: #0d6efd;
            text-decoration: none;
        }

        .dashboard-meta {
            display: block;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.4;
        }

        .dashboard-badge {
            white-space: nowrap;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .dashboard-empty {
            padding: 24px 20px;
            color: #6b7280;
            text-align: center;
        }

        .dashboard-tabs {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
        }

        .dashboard-tab {
            border: 0;
            background: transparent;
            color: #64748b;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            transition: all .2s ease;
        }

        .dashboard-tab:hover {
            color: #0f172a;
            background: #eef2ff;
        }

        .dashboard-tab.active {
            background: #1d4ed8;
            color: #fff;
            box-shadow: 0 6px 16px rgba(29, 78, 216, 0.25);
        }

        .dashboard-hidden {
            display: none;
        }
    </style>

    <div class="row">
        <div class="col-md-2">
            <div class="card-counter primary">
                <i class="la la-play-circle"></i>
                <span class="count-numbers counter">{{ $count_movies }}</span>
                <span class="count-name">Tổng số Phim</span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card-counter info">
                <i class="las la-server"></i>
                <span class="count-numbers counter">{{ $count_episodes }}</span>
                <span class="count-name">Tổng số Tập</span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card-counter danger">
                <i class="las la-bug"></i>
                <span class="count-numbers counter">{{ $count_episodes_error }}</span>
                <span class="count-name">Tập lỗi</span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card-counter success">
                <i class="las la-user"></i>
                <span class="count-numbers counter">{{ $count_users }}</span>
                <span class="count-name">Users</span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card-counter bg-primary">
                <i class="la la-paint-brush"></i>
                <span class="count-numbers counter">{{ $count_themes }}</span>
                <span class="count-name">Giao diện</span>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card-counter">
                <i class="las la-puzzle-piece"></i>
                <span class="count-numbers counter">{{ count(config('plugins', [])) }}</span>
                <span class="count-name">Plugins</span>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-panel h-100">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Phim Xem Nhiều</h3>
                        <div class="card-subtitle" id="most-viewed-subtitle">Top phim nổi bật theo lượt xem tuần này</div>
                    </div>
                    <div class="dashboard-tabs" id="most-viewed-filter">
                        <button class="dashboard-tab" type="button" data-period="day">Ngày</button>
                        <button class="dashboard-tab active" type="button" data-period="week">Tuần</button>
                        <button class="dashboard-tab" type="button" data-period="month">Tháng</button>
                    </div>
                </div>
                <div class="card-body p-0 dashboard-list">
                    @foreach ($mostViewedByPeriod as $period => $movies)
                        <div class="most-viewed-group {{ $period !== 'week' ? 'dashboard-hidden' : '' }}"
                            data-period="{{ $period }}">
                             @forelse ($movies as $index => $movie)
                                <div class="dashboard-list-item">
                                    <div class="dashboard-rank">{{ $index + 1 }}</div>
                                    <img class="dashboard-poster" src="{{ $movie->getThumbUrl() }}" alt="{{ $movie->name }}">
                                    <div class="dashboard-content">
                                        <a class="dashboard-name" href="{{ $movie->getUrl() }}" target="_blank">
                                            {{ $movie->name }}
                                        </a>
                                        <span class="dashboard-meta">{{ $movie->origin_name ?: 'Đang cập nhật tên gốc' }}</span>
                                        <span class="dashboard-meta">
                                            {{ $movie->episode_current ?: ($movie->type === 'series' ? 'Phim bộ' : 'Phim lẻ') }}
                                        </span>
                                    </div>
                                    <span class="badge badge-success dashboard-badge">
                                        <i class="las la-eye"></i>
                                        @if ($period === 'day')
                                            {{ number_format($movie->view_day ?? 0) }}
                                        @elseif ($period === 'month')
                                            {{ number_format($movie->view_month ?? 0) }}
                                        @else
                                            {{ number_format($movie->view_week ?? 0) }}
                                        @endif
                                    </span>
                                </div>
                            @empty
                                <div class="dashboard-empty">Chưa có dữ liệu lượt xem.</div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card dashboard-panel h-100">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Mới Cập Nhật</h3>
                        <div class="card-subtitle">Danh sách phim mới cập nhật gần đây</div>
                    </div>
                    <span class="badge badge-primary dashboard-badge">Mới nhất</span>
                </div>
                <div class="card-body p-0 dashboard-list">
                    @forelse ($recentlyUpdatedMovies as $movie)
                        <div class="dashboard-list-item">
                            <img class="dashboard-poster" src="{{ $movie->getThumbUrl() }}" alt="{{ $movie->name }}"> 
                            <div class="dashboard-content">
                                <a class="dashboard-name" href="{{ $movie->getUrl() }}" target="_blank">
                                    {{ $movie->name }}
                                </a>
                                <span class="dashboard-meta">
                                    {{ $movie->episode_current ?: ($movie->type === 'series' ? 'Đang cập nhật tập' : 'Bản phim lẻ') }}
                                </span>
                                <span class="dashboard-meta">
                                    Cập nhật: {{ optional($movie->updated_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="dashboard-empty">Chưa có phim nào mới cập nhật.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            var labels = {
                day: 'Top phim nổi bật theo lượt xem hôm nay',
                week: 'Top phim nổi bật theo lượt xem tuần này',
                month: 'Top phim nổi bật theo lượt xem tháng này'
            };

            $('#most-viewed-filter').on('click', '.dashboard-tab', function() {
                var period = $(this).data('period');
                $('#most-viewed-filter .dashboard-tab').removeClass('active');
                $(this).addClass('active');
                $('.most-viewed-group').addClass('dashboard-hidden');
                $('.most-viewed-group[data-period="' + period + '"]').removeClass('dashboard-hidden');
                $('#most-viewed-subtitle').text(labels[period] || labels.week);
            });
        });
    </script>
@endsection