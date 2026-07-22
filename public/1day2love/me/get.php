<?php

// --- No-cache headers to bypass Cloudflare and browser caching ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0");
header("Pragma: no-cache");
header("Expires: 0");
header("CF-Cache-Status: DYNAMIC");

if (!isset($_GET['url'])) {
    die(json_encode(["error" => "Thiếu tham số URL"]));
}

$video_url = $_GET['url'];
$cache_dir = __DIR__ . "/cache";
$cache_file = $cache_dir . "/" . md5($video_url) . ".m3u8";

$video_url = $_GET['url'];

// Tạo thư mục cache nếu chưa có
/*
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

// Kiểm tra cache (5 phút)
if (file_exists($cache_file) && time() - filemtime($cache_file) < 300) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/vnd.apple.mpegurl");
    echo file_get_contents($cache_file);
    exit;
}
*/
// Thiết lập CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $video_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$referer = $_SERVER['HTTP_REFERER'] ?? 'https://motflix.net/';
$referer = $_SERVER['HTTP_REFERER'] ?? 'https://cloudbeta.win/';
$headers = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Referer: $referer",
    "Accept: */*",
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($response === false) {
    die(json_encode(["error" => "CURL Error: $curl_error"]));
}

if ($http_code != 200) {
    die(json_encode(["error" => "Lỗi HTTP $http_code"]));
}

if (strpos($response, "#EXTM3U") === false) {
    die(json_encode(["error" => "Response không phải file .m3u8", "content" => substr($response, 0, 200)]));
}

// Lưu cache
//file_put_contents($cache_file, $response);

// Trả về nội dung file .m3u8
header("Content-Type: application/vnd.apple.mpegurl");
echo $response;
?>