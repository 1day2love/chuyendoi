<?php

namespace Ophim\ThemePmc\Controllers;

use Backpack\Settings\app\Models\Setting;
use Illuminate\Http\Request;
use Ophim\Core\Models\Actor;
use Ophim\Core\Models\Catalog;
use Ophim\Core\Models\Category;
use Ophim\Core\Models\Director;
use Ophim\Core\Models\Episode;
use Ophim\Core\Models\Movie;
use Ophim\Core\Models\Region;
use Ophim\Core\Models\Tag;

use Illuminate\Support\Facades\Cache;

class ThemePmcController
{
    public function index(Request $request)
    {
        if ($request['search'] || $request['filter']) {
            $data = Movie::when(!empty($request['filter']['category']), function ($movie) {
                $movie->whereHas('categories', function ($categories) {
                    $categories->where('id', request('filter')['category']);
                });
            })->when(!empty($request['filter']['region']), function ($movie) {
                $movie->whereHas('regions', function ($regions) {
                    $regions->where('id', request('filter')['region']);
                });
            })->when(!empty($request['filter']['year']), function ($movie) {
                $movie->where('publish_year', request('filter')['year']);
            })->when(!empty($request['filter']['type']), function ($movie) {
                $movie->where('type', request('filter')['type']);
            })->when(!empty($request['search']), function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('origin_name', 'like', '%' . request('search')  . '%');
                })->orderBy('name', 'desc');
            })->when(!empty($request['filter']['sort']), function ($movie) {
                if (request('filter')['sort'] == 'create') {
                    return $movie->orderBy('created_at', 'desc');
                }
                if (request('filter')['sort'] == 'update') {
                    return $movie->orderBy('updated_at', 'desc');
                }
                if (request('filter')['sort'] == 'year') {
                    return $movie->orderBy('publish_year', 'desc');
                }
                if (request('filter')['sort'] == 'view') {
                    return $movie->orderBy('view_total', 'desc');
                }
            })->paginate(25);
            //})->paginate(get_theme_option('per_page_limit'));

            return view('themes::themepmc.catalog', [
                'data' => $data,
                'search' => $request['search'],
                'section_name' => "Tìm kiếm phim: $request->search"
            ]);
        }
        return view('themes::themepmc.index', [
            'title' => Setting::get('site_homepage_title')
        ]);
    }

    public function getMovieOverview(Request $request)
    {
        /** @var Movie */
        $movie = Movie::fromCache()->find($request->movie ?: $request->id);

        if (is_null($movie)) abort(404);

        $movie->generateSeoTags();

        $movie->increment('view_total', 1);
        $movie->increment('view_day', 1);
        $movie->increment('view_week', 1);
        $movie->increment('view_month', 1);

        $movie_related_cache_key = 'movie_related:' . $movie->id;
        $movie_related = Cache::get($movie_related_cache_key);
        if(is_null($movie_related)) {
            if ($movie->categories->isNotEmpty() && $movie->regions->isNotEmpty()) {
            $movie_related = $movie->categories[0]->movies()->whereHas('regions', function($query) use ($movie) {
                $query->whereIn('id', $movie->regions->pluck('id')); // Lọc các phim cùng vùng
            })
            ->where('id', '!=', $movie->id) // Loại trừ phim hiện tại
            ->inRandomOrder()->limit(get_theme_option('movie_related_limit', 10))->get();
            Cache::put($movie_related_cache_key, $movie_related, setting('site_cache_ttl', 5 * 60));
        }
        //code sửa
        else {
            //$movie_related = collect(); // Hoặc giá trị mặc định khác
            abort(404, 'Không có phim liên quan.');
            }
        }
        return view('themes::themepmc.single', [
            'currentMovie' => $movie,
            'title' => $movie->getTitle(),
            'movie_related' => $movie_related
        ]);
    }

    public function getEpisode(Request $request)
    {
        //$movie = Movie::fromCache()->find($request->movie ?: $request->movie_id)->load('episodes'); //gốc
        $movie = Movie::fromCache()->find($request->movie ?: $request->movie_id); // hàm sửa
        if (is_null($movie)) abort(404); // gốc
       /*
        
        if (is_null($movie)) {
            abort(404); // Xử lý khi không tìm thấy bộ phim
         }
          */
         // Load episodes nếu bộ phim tồn tại
        $movie->load('episodes');
       
        
        /** @var Episode */
        $episode_id = $request->id;
        $episode = $movie->episodes->when($episode_id, function ($collection, $episode_id) {
            return $collection->where('id', $episode_id);
        })->firstWhere('slug', $request->episode);

        if (is_null($episode)) abort(404);

        $episode->generateSeoTags();

        $movie->increment('view_total', 1);
        $movie->increment('view_day', 1);
        $movie->increment('view_week', 1);
        $movie->increment('view_month', 1);

        $movie_related_cache_key = 'movie_related:' . $movie->id;
        $movie_related = Cache::get($movie_related_cache_key);
        if(is_null($movie_related)) {
            if ($movie->categories->isNotEmpty() && $movie->regions->isNotEmpty()) {
            $movie_related = $movie->categories[0]->movies()->whereHas('regions', function($query) use ($movie) {
                $query->whereIn('id', $movie->regions->pluck('id')); // Lọc các phim cùng vùng
            })
            ->where('id', '!=', $movie->id) // Loại trừ phim hiện tại
            ->inRandomOrder()->limit(get_theme_option('movie_related_limit', 10))->get();
            Cache::put($movie_related_cache_key, $movie_related, setting('site_cache_ttl', 5 * 60));
        }
        //code sửa
        else {
            //$movie_related = collect(); // Hoặc giá trị mặc định khác
            abort(404, 'Không có phim liên quan.');
            }
        }
        return view('themes::themepmc.episode', [
            'currentMovie' => $movie,
            'movie_related' => $movie_related,
            'episode' => $episode,
            'title' => $episode->getTitle()
        ]);
    }

    public function getMovieOfCategory(Request $request)
    {
        /** @var Category */
        $category = Category::fromCache()->find($request->category ?: $request->id);

        if (is_null($category)) abort(404);

        $category->generateSeoTags();

        $movies = $category->movies()->orderBy('updated_at', 'desc')->paginate(25);

        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'category' => $category,
            'title' => $category->seo_title ?: $category->getTitle(),
            'section_name' => "Phim thể loại $category->name"
        ]);
    }

    public function getMovieOfRegion(Request $request)
    {
        /** @var Region */
        $region = Region::fromCache()->find($request->region ?: $request->id);

        if (is_null($region)) abort(404);

        $region->generateSeoTags();

        $movies = $region->movies()->orderBy('updated_at', 'desc')->paginate(25);

        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'region' => $region,
            'title' => $region->seo_title ?: $region->getTitle(),
            'section_name' => "Phim quốc gia $region->name"
        ]);
    }

    public function getMovieOfActor(Request $request)
    {
        /** @var Actor */
        $actor = Actor::fromCache()->find($request->actor ?: $request->id);

        if (is_null($actor)) abort(404);

        $actor->generateSeoTags();

        $movies = $actor->movies()->orderBy('updated_at', 'desc')->paginate(25);

        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'person' => $actor,
            'title' => $actor->getTitle(),
            'section_name' => "Diễn viên $actor->name"
        ]);
    }

    public function getMovieOfDirector(Request $request)
    {
        /** @var Director */
        $director = Director::fromCache()->find($request->director ?: $request->id);

        if (is_null($director)) abort(404);

        $director->generateSeoTags();

        $movies = $director->movies()->orderBy('updated_at', 'desc')->paginate(25);

        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'person' => $director,
            'title' => $director->getTitle(),
            'section_name' => "Đạo diễn $director->name"
        ]);
    }

    public function getMovieOfTag(Request $request)
    {
        /** @var Tag */
        $tag = Tag::fromCache()->find($request->tag ?: $request->id);

        if (is_null($tag)) abort(404);

        $tag->generateSeoTags();

        $movies = $tag->movies()->orderBy('created_at', 'desc')->paginate(25);
        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'tag' => $tag,
            'title' => $tag->getTitle(),
            'section_name' => "Tags: $tag->name"
        ]);
    }

    public function getMovieOfType(Request $request)
    {
        /** @var Catalog */
        $catalog = Catalog::fromCache()->find($request->type ?: $request->id);

        if (is_null($catalog)) abort(404);

        $catalog->generateSeoTags();

        $cache_key = 'catalog:' . $catalog->id . ':page:' . ($request['page'] ?: 1);
        $movies = Cache::get($cache_key);
        if(is_null($movies)) {
            $value = explode('|', trim($catalog->value));
            [$relation_config, $field, $val, $sortKey, $alg] = array_merge($value, ['', 'is_copyright', 0, 'created_at', 'desc']);
            $relation_config = explode(',', $relation_config);

            [$relation_table, $relation_field, $relation_val] = array_merge($relation_config, ['', '', '']);
            try {
                $movies = \Ophim\Core\Models\Movie::when($relation_table, function ($query) use ($relation_table, $relation_field, $relation_val, $field, $val) {
                    $query->whereHas($relation_table, function ($rel) use ($relation_field, $relation_val, $field, $val) {
                        $rel->where($relation_field, $relation_val)->where(array_combine(explode(",", $field), explode(",", $val)));
                    });
                })->when(!$relation_table, function ($query) use ($field, $val) {
                    $query->where(array_combine(explode(",", $field), explode(",", $val)));
                })
                ->orderBy($sortKey, $alg)
                ->paginate(25);
                //->paginate($catalog->paginate);

                Cache::put($cache_key, $movies, setting('site_cache_ttl', 5 * 60));
            } catch (\Exception $e) {}
        }

        return view('themes::themepmc.catalog', [
            'data' => $movies,
            'section_name' => "Danh sách $catalog->name"
        ]);
    }

    public function reportEpisode(Request $request, $movie, $slug, $id)
    {
        $movie = Movie::fromCache()->find($movie)->load('episodes');

        $episode = $movie->episodes->when($id, function ($collection, $id) {
            return $collection->where('id', $id);
        })->firstWhere('slug', $slug);

        $episode->update([
            'report_message' => request('message', ''),
            'has_report' => true
        ]);

        return response([], 204);
    }

    public function rateMovie(Request $request, $movie)
    {

        $movie = Movie::fromCache()->find($movie);

        $movie->refresh()->increment('rating_count', 1, [
            'rating_star' => $movie->rating_star +  ((int) request('rating') - $movie->rating_star) / ($movie->rating_count + 1)
        ]);

        return response([], 204);
    }
    
    public function getMoviesByRegion($key)
{
    return Movie::with(['regions' => function($query) use ($key) {
                           $query->where('slug', $key);
                       }])
                  ->select(['id', 'title', 'type', 'status', 'updated_at'])
                  ->where('type', 'series')
                  ->where('status', '!=', 'trailer')
                  ->latest('updated_at')
                  ->limit(12)
                  ->get();
}

    
    
    public function getContentBox(Request $request)
    {  
        if ($request->ajax()) {
            $key = $request->input('key');
    
            // Kiểm tra xem key có tồn tại hay không
            if (empty($key)) {
                return response()->json(['error' => 'Invalid key'], 400);
            }
    
            $movies = collect();
    
            // Xử lý các điều kiện khác nhau dựa trên key
            switch ($key) {
                /* case 'phim-bo': // phim bộ
                    $movies = Movie::where('type', 'series')->orderBy('updated_at', 'desc')->take(7)->get();
                break;                  

                case 'phim-le': // phim lẻ
                    $movies = Movie::where('type', 'single')->orderBy('updated_at', 'desc')->take(7)->get();
                break;                  
                
                case 'phim-hoan-thanh': // phim hoàn thành
                    $movies = Movie::where('status', 'completed')->where('is_copyright', 0)->orderBy('updated_at', 'desc')->take(7)->get();
                break;                   
                
                case 'phim-2019':
                    $movies = Movie::where('is_shown_in_theater', 1)->where('publish_year', 2019)->orderBy('updated_at', 'desc')->take(12)->get();          
                break;   

                */
                case 'phim-2020':
                    $movies = Movie::where('is_shown_in_theater', 1)->where('publish_year', 2020)->orderBy('updated_at', 'desc')->take(12)->get();               
                break;
                
                case 'phim-2021':
                    $movies = Movie::where('is_shown_in_theater', 1)->where('publish_year', 2021)->orderBy('updated_at', 'desc')->take(12)->get();               
                break;
                
                case 'phim-2022':
                    $movies = Movie::where('is_shown_in_theater', 1)->where('publish_year', 2022)->orderBy('updated_at', 'desc')->take(12)->get();               
                break;
                
                case 'phim-2023':
                    $movies = Movie::where('is_shown_in_theater', 1)->where('publish_year', 2023)->orderBy('updated_at', 'desc')->take(12)->get();               
                break;
    
                case (in_array($key, ['hanh-dong', 'hoat-hinh', 'kinh-di', 'hai-huoc'])):
                    $movies = Movie::whereHas('categories', function ($query) use ($key) {
                        $query->where('slug', $key);
                    })->where('type', 'single')
                      ->where('status', '!=', 'trailer')
                      ->orderBy('updated_at', 'desc')
                      ->take(12)
                      ->get();
                    break;
    
                case (in_array($key, ['han-quoc', 'trung-quoc', 'au-my', 'thai-lan'])):
                    $movies = Movie::whereHas('regions', function ($query) use ($key) {
                        $query->where('slug', $key);
                    })->where('type', 'series')
                      ->where('status', '!=', 'trailer')
                      ->orderBy('updated_at', 'desc')
                      ->take(12)
                      ->get();
                    break;
                
                case 'le-thinh-hanh':
                    $movies = Movie::where('type', 'single')->where('is_copyright', 0)->whereNotNull('view_week')->orderBy('view_week', 'desc')->orderBy('updated_at', 'desc')->take(7)->get();            
                break;
    
                case 'bo-thinh-hanh':
                    $movies = Movie::where('type', 'series')->where('is_copyright', 0)->whereNotNull('view_week')->orderBy('view_week', 'desc')->orderBy('updated_at', 'desc')->take(7)->get();         
                break;

                default:
                    return response()->json(['error' => 'Invalid key'], 400);
            }
    
            // Kiểm tra nếu không có phim nào được tìm thấy
            if ($movies->isEmpty()) {
                return response()->json(['message' => 'No movies found'], 404);
            }
           
    // Tạo nội dung HTML cho danh sách phim
         $html = '';
    // Duyệt qua từng bộ phim và tạo HTML tương ứng
        foreach ($movies as $index => $movie) {
            $isLarge = ($index === 0); // Xác định liệu phải sử dụng lớn hay nhỏ
            $sizeClass = $isLarge ? 'large' : 'small';
        
            $html .= '<li class="item ' . $sizeClass . '">';
            //$html .= '<span class="label">' . $movie->episode_current . ' ' . $movie->quality . ' ' . $movie->language . '</span>';
            //Kiểm tra nếu type là phim bộ hoặc phim lẻ hoặc trailler
            if ($movie->type === 'series') {
                if ($movie->status === 'ongoing') {
                    $html .= '<span class="label">' . $movie->episode_current . ' ' . $movie->quality . ' ' . $movie->language . '</span>';
                } elseif ($movie->status === 'completed') {
                    $html .= '<span class="label">' . $movie->episode_current . '</span>';
                } else {
                    $html .= '<span class="label">' . $movie->episode_current . ' ' . $movie->quality . ' ' . $movie->language . '</span>';
                }
            } elseif ($movie->status === 'trailer') {
                $html .= '<span class="label">' . $movie->episode_current . ' ' . $movie->quality . '</span>';
            } elseif ($movie->type === 'single') {
                $html .= '<span class="label">' . $movie->quality . ' ' . $movie->language . '</span>';
            }
            $html .= '<a title="' . $movie->name . ' - ' . $movie->origin_name . '" href="' . $movie->getUrl() . '">';
            
            // Giải mã URL, loại bỏ các tiền tố và thêm tiền tố mới
            $posterUrl = urldecode($movie->getPosterUrl());
            $posterUrl = str_replace(['/build/image.php?url=', 'https://wsrv.nl/?url=', 'wsrv.nl/?url=', 'https://', 'http://', 'images.weserv.nl/?url='], '', $posterUrl);
            // chỉ 1 dấu / đầu tiên
            $posterUrl = preg_replace('/^\/+/', '', $posterUrl);
            //$posterUrl = preg_replace('/^storage\//', '', $posterUrl); // Loại bỏ /storage
            //$currentDomain = $_SERVER['HTTP_HOST'];
            //$posterUrl = 'https://i3.wp.com/img.' . $currentDomain . '/' . $posterUrl; // sử dụng khi tắt Proxy cho đường dẫn hình ảnh
            $posterUrl = 'https://i3.wp.com/' . $posterUrl; // sử dụng khi bật Proxy cho đường dẫn hình ảnh
            // Loại bỏ phần sau ảnh
            $posterUrl = preg_replace('/(\.jpg|\.png|\.gif|\.jpeg|\.webp|\.bmp|\.tiff)([\?&].*)?$/', '$1', $posterUrl);
            // Thêm kích thước dựa trên loại ảnh
            $posterUrl .= $isLarge ? '?w=800&h=450' : '?w=500&h=281';

            $imgSize = $isLarge ? '485px' : '238px';
            $html .= '<img width="' . $imgSize . '" height="';
            $html .= $isLarge ? '273px" class="img-1" ' : '134px" class="img-2" ';
            $html .= 'alt="' . $movie->name . ' - ' . $movie->origin_name . '" src="' . $posterUrl . '" />';
            $html .= '<p>' . $movie->name . '</p> <i class="icon-play"></i>';
            $html .= '</a>';
            $html .= '</li>';
         }
         
            // Trả về đối tượng Response với nội dung HTML và key tương ứng
            return response($html);
         } else{
            return response('<p>dmm</p>', 400)->header('Content-Type', 'text/html');
         }
    }
    public function contact()
    {
        return view("themes::themepmc.post.contact");
    }
    public function copyright()
    {
        return view("themes::themepmc.post.copyright");
    }
    public function privacy()
    {
        return view("themes::themepmc.post.privacy");
    }
    public function aboutus()
    {
        return view("themes::themepmc.post.aboutus");
    }
    public function terms()
    {
        return view("themes::themepmc.post.terms");
    }
    public function huongdansudung ()
    {
        return view("themes::themepmc.post.huongdansudung");
    }
    public function sitemapauto()
    {
        $domain = url('/'); // Lấy tên miền gốc của ứng dụng
        $sitemapUrl = $domain . '/sitemap.xml'; // Đường dẫn đến sitemap

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /report\n";
        //$content .= "Disallow: /report?id=\n";
        $content .= "Disallow: /*?search\n";
        $content .= "Disallow: /rate\n";
        $content .= "Sitemap: $sitemapUrl\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}