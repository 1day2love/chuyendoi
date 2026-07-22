<?php

use Illuminate\Support\Facades\Route;
use Ophim\ThemePmc\Controllers\ThemePmcController;

use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.
/*

Route::group([
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
    ),
], function () {
    Route::get('/', [ThemePmcController::class, 'index']);

    Route::get(setting('site_routes_category', '/the-loai/{category}'), [ThemePmcController::class, 'getMovieOfCategory'])
        ->where(['category' => '.+', 'id' => '[0-9]+'])
        ->name('categories.movies.index');

    Route::get(setting('site_routes_region', '/quoc-gia/{region}'), [ThemePmcController::class, 'getMovieOfRegion'])
        ->where(['region' => '.+', 'id' => '[0-9]+'])
        ->name('regions.movies.index');

    Route::get(setting('site_routes_tag', '/tu-khoa/{tag}'), [ThemePmcController::class, 'getMovieOfTag'])
        ->where(['tag' => '.+', 'id' => '[0-9]+'])
        ->name('tags.movies.index');

    Route::get(setting('site_routes_types', '/danh-sach/{type}'), [ThemePmcController::class, 'getMovieOfType'])
        ->where(['type' => '.+', 'id' => '[0-9]+'])
        ->name('types.movies.index');

    Route::get(setting('site_routes_actors', '/dien-vien/{actor}'), [ThemePmcController::class, 'getMovieOfActor'])
        ->where(['actor' => '.+', 'id' => '[0-9]+'])
        ->name('actors.movies.index');

    Route::get(setting('site_routes_directors', '/dao-dien/{director}'), [ThemePmcController::class, 'getMovieOfDirector'])
        ->where(['director' => '.+', 'id' => '[0-9]+'])
        ->name('directors.movies.index');

    Route::get(setting('site_routes_episode', '/phim/{movie}/{episode}-{id}'), [ThemePmcController::class, 'getEpisode'])
        ->where(['movie' => '.+', 'movie_id' => '[0-9]+', 'episode' => '.+', 'id' => '[0-9]+'])
        ->name('episodes.show');

    Route::post(sprintf('/%s/{movie}/{episode}-{id}/report', config('ophim.routes.movie', 'phim')), [ThemePmcController::class, 'reportEpisode'])
        ->where(['movie' => '.+', 'episode' => '.+', 'id' => '[0-9]+'])->name('episodes.report');
    Route::post(sprintf('/%s/{movie}/rate', config('ophim.routes.movie', 'phim')), [ThemePmcController::class, 'rateMovie'])->name('movie.rating');

    Route::get(setting('site_routes_movie', '/phim/{movie}'), [ThemePmcController::class, 'getMovieOverview'])
        ->where(['movie' => '.+', 'id' => '[0-9]+'])
        ->name('movies.show');
        
    Route::get('/ajax/get_content', [ThemePmcController::class, 'getContentBox'])->name('getContentBox');
    Route::post('/ajax/get_content', [ThemePmcController::class, 'getContentBox'])->name('getContentBox');
    
    Route::get('/post/contact', [ThemePmcController::class, 'contact'])->name('contact');
    Route::get('/post/copyright', [ThemePmcController::class, 'copyright'])->name('copyright');
    Route::get('/post/aboutus', [ThemePmcController::class, 'aboutus'])->name('aboutus');
    Route::get('/post/terms', [ThemePmcController::class, 'terms'])->name('terms');
    Route::get('/post/privacy', [ThemePmcController::class, 'privacy'])->name('privacy');
    Route::get('/post/huong-dan-su-dung', [ThemePmcController::class, 'huongdansudung'])->name('huongdansudung');
    
    Route::get('/robots.txt', [ThemePmcController::class, 'sitemapauto'])->name('sitemapauto');
    
});
*/
Route::middleware(
        array_merge((array) config('backpack.base.web_middleware', 'web'))
    )
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ])
    ->group(function () {
    Route::get('/', [ThemePmcController::class, 'index']);
  
    Route::get(setting('site_routes_category', '/the-loai/{category}'), [ThemePmcController::class, 'getMovieOfCategory'])
        ->where(['category' => '.+', 'id' => '[0-9]+'])
        ->name('categories.movies.index')
        ->middleware('cache.headers:public;max_age=3600;must_revalidate');

    Route::get(setting('site_routes_region', '/quoc-gia/{region}'), [ThemePmcController::class, 'getMovieOfRegion'])
        ->where(['region' => '.+', 'id' => '[0-9]+'])
        ->name('regions.movies.index')
        ->middleware('cache.headers:public;max_age=3600;must_revalidate');

    Route::get(setting('site_routes_tag', '/tu-khoa/{tag}'), [ThemePmcController::class, 'getMovieOfTag'])
        ->where(['tag' => '.+', 'id' => '[0-9]+'])
        ->name('tags.movies.index')
        ->middleware('cache.headers:public;max_age=3600;must_revalidate');

    Route::get(setting('site_routes_types', '/danh-sach/{type}'), [ThemePmcController::class, 'getMovieOfType'])
        ->where(['type' => '.+', 'id' => '[0-9]+'])
        ->name('types.movies.index')
        ->middleware('cache.headers:public;max_age=300;must_revalidate');

    Route::get(setting('site_routes_actors', '/dien-vien/{actor}'), [ThemePmcController::class, 'getMovieOfActor'])
        ->where(['actor' => '.+', 'id' => '[0-9]+'])
        ->name('actors.movies.index')
        ->middleware('cache.headers:public;max_age=3600;must_revalidate');
    

    Route::get(setting('site_routes_directors', '/dao-dien/{director}'), [ThemePmcController::class, 'getMovieOfDirector'])
        ->where(['director' => '.+', 'id' => '[0-9]+'])
        ->name('directors.movies.index')
        ->middleware('cache.headers:public;max_age=3600;must_revalidate');

    Route::get(setting('site_routes_episode', '/phim/{movie}/{episode}-{id}'), [ThemePmcController::class, 'getEpisode'])
        ->where(['movie' => '.+', 'movie_id' => '[0-9]+', 'episode' => '.+', 'id' => '[0-9]+'])
        ->name('episodes.show');

    Route::post(sprintf('/%s/{movie}/{episode}-{id}/report', config('ophim.routes.movie', 'phim')), [ThemePmcController::class, 'reportEpisode'])
        ->where(['movie' => '.+', 'episode' => '.+', 'id' => '[0-9]+'])->name('episodes.report');
    Route::post(sprintf('/%s/{movie}/rate', config('ophim.routes.movie', 'phim')), [ThemePmcController::class, 'rateMovie'])->name('movie.rating');

    Route::get(setting('site_routes_movie', '/phim/{movie}'), [ThemePmcController::class, 'getMovieOverview'])
        ->where(['movie' => '.+', 'id' => '[0-9]+'])
        ->name('movies.show');
        
    Route::get('/ajax/get_content', [ThemePmcController::class, 'getContentBox'])->name('getContentBox');
    Route::post('/ajax/get_content', [ThemePmcController::class, 'getContentBox'])->name('getContentBox');
    Route::post('/ajax/movie_update_view', [ThemePmcController::class, 'updateMovieView'])
        ->name('movie.update-view');
    
    Route::get('/post/contact', [ThemePmcController::class, 'contact'])->name('contact')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    Route::get('/post/copyright', [ThemePmcController::class, 'copyright'])->name('copyright')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    Route::get('/post/aboutus', [ThemePmcController::class, 'aboutus'])->name('aboutus')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    Route::get('/post/terms', [ThemePmcController::class, 'terms'])->name('terms')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    Route::get('/post/privacy', [ThemePmcController::class, 'privacy'])->name('privacy')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    Route::get('/post/huong-dan-su-dung', [ThemePmcController::class, 'huongdansudung'])->name('huongdansudung')->middleware('cache.headers:public;max_age=3600;must_revalidate');
    
    Route::get('/robots.txt', [ThemePmcController::class, 'sitemapauto'])->name('sitemapauto');
    
});