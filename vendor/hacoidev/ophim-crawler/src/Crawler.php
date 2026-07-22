<?php

namespace Ophim\Crawler\OphimCrawler;

use Ophim\Core\Models\Movie;
use Illuminate\Support\Str;
use Ophim\Core\Models\Actor;
use Ophim\Core\Models\Category;
use Ophim\Core\Models\Director;
use Ophim\Core\Models\Episode;
use Ophim\Core\Models\Region;
use Ophim\Core\Models\Tag;
use Ophim\Crawler\OphimCrawler\Contracts\BaseCrawler;

class Crawler extends BaseCrawler
{
       public function handle()
    {
        $payload = json_decode($body = file_get_contents($this->link), true);

        $this->checkIsInExcludedList($payload);
        
          // Bộ phim tồn tại trong cơ sở dữ liệu, thông báo
         $existingMovie = Movie::where('slug', Str::slug($payload['movie']['slug']))->first();
         //  $existingMovie = Movie::where('slug', $payload['movie']['slug'])->first();
         if ($existingMovie) {
            $name = $existingMovie->name ?? 'không có tên';
            if ($existingMovie->type == 'single') {
                $episodeCurrent = $existingMovie->episode_current ?? 'không có';
                $status = $existingMovie->status ?? 'không có';
                if ($status == 'trailer') {
                    $statusMsg = 'Sắp chiếu';
                } elseif ($status == 'ongoing') {
                    $statusMsg = 'Đang chiếu';
                } elseif ($status == 'completed') {
                    $statusMsg = 'Đã hoàn thành';
                } else {
                    $statusMsg = $status;
                }
                throw new \Exception("Phim lẻ: $name Đã tồn tại. Số tập hiện tại: $episodeCurrent, Trạng thái: $statusMsg");
            } elseif ($existingMovie->type == 'series') {
                $episodeCurrent = $existingMovie->episode_current ?? 'không có';
                $status = $existingMovie->status ?? 'không có';
                if ($status == 'trailer') {
                    $statusMsg = 'Sắp chiếu';
                } elseif ($status == 'ongoing') {
                    $statusMsg = 'Đang chiếu';
                } elseif ($status == 'completed') {
                    $statusMsg = 'Đã hoàn thành';
                } else {
                    $statusMsg = $status;
                }
                throw new \Exception("Phim bộ: $name Đã tồn tại. Số tập hiện tại: $episodeCurrent, Trạng thái: $statusMsg");
            }
            return;
        }
        //end kiểm tra trùng phim
        
        $movie = Movie::where('update_handler', static::class)
            ->where('update_identity', $payload['movie']['_id'])
            ->first();

        if (!$this->hasChange($movie, md5($body)) && $this->forceUpdate == false) {
            return false;
        }
         
        $info = (new Collector($payload, $this->fields, $this->forceUpdate))->get();
        
        if ($movie) {
            $movie->updated_at = now();
            $movie->update(collect($info)->only($this->fields)->merge(['update_checksum' => md5($body)])->toArray());
        } else {
            $movie = Movie::create(array_merge($info, [
                'update_handler' => static::class,
                'update_identity' => $payload['movie']['_id'],
                'update_checksum' => md5($body)
            ]));
        }

        $this->syncActors($movie, $payload);
        $this->syncDirectors($movie, $payload);
        $this->syncCategories($movie, $payload);
        $this->syncRegions($movie, $payload);
        $this->syncTags($movie, $payload);
        $this->syncStudios($movie, $payload);
        //$this->updateEpisodes($movie, $payload);
    }

    protected function hasChange(?Movie $movie, $checksum)
    {
        return is_null($movie) || ($movie->update_checksum != $checksum);
    }

    protected function checkIsInExcludedList($payload)
    {
        $newType = $payload['movie']['type'];
        if (in_array($newType, $this->excludedType)) {
            throw new \Exception("Thuộc định dạng đã loại trừ");
        }

        $newCategories = collect($payload['movie']['category'])->pluck('name')->toArray();
        if (array_intersect($newCategories, $this->excludedCategories)) {
            throw new \Exception("Thuộc thể loại đã loại trừ");
        }

        $newRegions = collect($payload['movie']['country'])->pluck('name')->toArray();
        if (array_intersect($newRegions, $this->excludedRegions)) {
            throw new \Exception("Thuộc quốc gia đã loại trừ");
        }
    }

    protected function syncActors($movie, array $payload)
    {
        if (!in_array('actors', $this->fields)) return;

        $actors = [];
        // Giới hạn chỉ lấy 5 diễn viên đầu tiên
        $actorList = array_slice($payload['movie']['actor'], 0, 20);
        
        foreach ($actorList as $actor) {
            if (!trim($actor)) continue;
            $actors[] = Actor::firstOrCreate(['name' => trim($actor)])->id;
        }
        $movie->actors()->sync($actors);
    }

    protected function syncDirectors($movie, array $payload)
    {
        if (!in_array('directors', $this->fields)) return;

        $directors = [];
        foreach ($payload['movie']['director'] as $director) {
            if (!trim($director)) continue;
            $directors[] = Director::firstOrCreate(['name' => trim($director)])->id;
        }
        $movie->directors()->sync($directors);
    }

    protected function syncCategories($movie, array $payload)
    {
        if (!in_array('categories', $this->fields)) return;
        $categories = [];
        foreach ($payload['movie']['category'] as $category) {
            if (!trim($category['name'])) continue;
            $categories[] = Category::firstOrCreate(['name' => trim($category['name'])])->id;
        }
        if($payload['movie']['type'] === 'hoathinh') $categories[] = Category::firstOrCreate(['name' => 'Hoạt Hình'])->id;
        if($payload['movie']['type'] === 'tvshows') $categories[] = Category::firstOrCreate(['name' => 'TV Shows'])->id;
        $movie->categories()->sync($categories);
    }

    protected function syncRegions($movie, array $payload)
    {
        if (!in_array('regions', $this->fields)) return;

        $regions = [];
        foreach ($payload['movie']['country'] as $region) {
            if (!trim($region['name'])) continue;
            $regions[] = Region::firstOrCreate(['name' => trim($region['name'])])->id;
        }
        $movie->regions()->sync($regions);
    }

    protected function syncTags($movie, array $payload)
    {
        if (!in_array('tags', $this->fields)) return;

        $tags = [];
        $tags[] = Tag::firstOrCreate(['name' => trim($movie->name)])->id;
        $tags[] = Tag::firstOrCreate(['name' => trim($movie->origin_name)])->id;

        $movie->tags()->sync($tags);
    }

    protected function syncStudios($movie, array $payload)
    {
        if (!in_array('studios', $this->fields)) return;
    }

    /* Code gốc
    protected function updateEpisodes($movie, $payload)
    {
        $defaultServerName = '#Vietsub'; // mặc định server #Vietsub
        if (!in_array('episodes', $this->fields)) return;
        foreach ($payload['episodes'] as $server) {
            foreach ($server['server_data'] as $episode) {
                if ($episode['link_m3u8']) {
					$str_slug = '';
					if($episode['name'] == 'Full') {
						$str_slug = 'tap-';
					}
                    $curr_episode = $movie->episodes()
                                        ->where('server', $server['server_name'])
                                        ->where('name', $episode['name'])
                                        ->where('type', 'm3u8')
                                        ->first();
                    if ($curr_episode) {
                        $curr_episode->link = $episode['link_m3u8'];
                        $curr_episode->save();
                    } else {
                        $movie->episodes()->create([
                            'server' => $defaultServerName, // $server['server_name'],
                            'name' => $episode['name'],
                            'slug' => $str_slug . Str::slug($episode['name']),
                            'type' => 'm3u8',
                            'link' => $episode['link_m3u8'],
                        ]);
                    }
                }
                if ($episode['link_embed']) {
					$str_slug = '';
					if($episode['name'] == 'Full') {
						$str_slug = 'tap-';
					}
                    $curr_episode = $movie->episodes()
                                        ->where('server', $server['server_name'])
                                        ->where('name', $episode['name'])
                                        ->where('type', 'embed')
                                        ->first();
                    if ($curr_episode) {
                        $curr_episode->link = $episode['link_embed'];
                        $curr_episode->save();
                    } else {
                        $movie->episodes()->create([
                            'server' => $defaultServerName, // $server['server_name'],
                            'name' => $episode['name'],
                            'slug' => $str_slug . Str::slug($episode['name']),
                            'type' => 'embed',
                            'link' => $episode['link_embed'],
                        ]);
                    }
                }
            }
        }
    }
}
------------------------*/
protected function updateEpisodes($movie, $payload)
{
    $defaultServerName = '#Vietsub';
    if (!in_array('episodes', $this->fields)) return;
    foreach ($payload['episodes'] as $server) {
        foreach ($server['server_data'] as $episode) {
            $name = $episode['name'];
            $name = preg_replace('/Tập\s*0\s*|Tập\s*/', '', $name);
            $name = trim($name);
                $str_slug = '';
                if($name == 'Full') {
                    $str_slug = 'tap-';
                }

                $slug = Str::slug($name);
                if (strpos($slug, 'tap-') !== 0) {
                    $slug = 'tap-' . $slug;
                }

                if ($episode['link_m3u8']) {
                $curr_episode = $movie->episodes()
                                    ->where('server', $server['server_name'])
                                    ->where('name', $name)
                                    ->where('type', 'm3u8')
                                    ->first();
                if ($curr_episode) {
                    $curr_episode->link = $episode['link_m3u8'];
                    if ($curr_episode->slug != $slug) {
                        $curr_episode->slug = $slug;
                    }
                    $curr_episode->save();
                } else {
                    $movie->episodes()->create([
                        'server' => $defaultServerName,
                        'name' => $name,
                        'slug' => $slug,
                        'type' => 'm3u8',
                        'link' => $episode['link_m3u8'],
                    ]);
                }
            }
            if ($episode['link_embed']) {
                $curr_episode = $movie->episodes()
                                    ->where('server', $server['server_name'])
                                    ->where('name', $episode['name'])
                                    ->where('type', 'embed')
                                    ->first();
                if ($curr_episode) {
                    $curr_episode->link = $episode['link_embed'];
                    if ($curr_episode->slug != $slug) {
                        $curr_episode->slug = $slug;
                    }
                    $curr_episode->save();
                } else {
                    $movie->episodes()->create([
                        'server' => $defaultServerName,
                        'name' => $name,
                        'slug' => $slug,
                        'type' => 'embed',
                        'link' => $episode['link_embed'],
                    ]);
                }
            }
        }
    }
}
}