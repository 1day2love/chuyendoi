<?php

declare(strict_types=1);

/*
Image Proxy + WebP Converter
Usage:
img.php?url=/storage/poster.jpg
img.php?url=https://domain.com/image.jpg
*/

$quality = isset($_GET['q']) ? max(1, min(100, (int) $_GET['q'])) : 80;
$maxConvertPixels = 12000000;
$convertibleFormats = ['jpg', 'jpeg', 'png'];

function fail(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    exit($message);
}

function supportsWebP(): bool
{
    return strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'image/webp') !== false;
}

function isRemoteUrl(string $value): bool
{
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true);
}

function isPublicIpAddress(string $ip): bool
{
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) !== false;
}

function resolveRemoteHost(string $host): array
{
    $ips = [];

    if (function_exists('dns_get_record')) {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (is_array($records)) {
            foreach ($records as $record) {
                if (!empty($record['ip'])) {
                    $ips[] = $record['ip'];
                }

                if (!empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }
    }

    if ($ips === [] && filter_var($host, FILTER_VALIDATE_IP)) {
        $ips[] = $host;
    }

    if ($ips === [] && function_exists('gethostbynamel')) {
        $resolved = @gethostbynamel($host);
        if (is_array($resolved)) {
            $ips = array_merge($ips, $resolved);
        }
    }

    return array_values(array_unique($ips));
}

function validateRemoteUrl(string $value): void
{
    $parts = parse_url($value);
    $host = strtolower((string) ($parts['host'] ?? ''));
    $requestHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $requestHost = preg_replace('/:\d+$/', '', $requestHost);

    if ($host === '') {
        fail(400, 'Invalid remote image URL');
    }

    if (!empty($parts['user']) || !empty($parts['pass'])) {
        fail(400, 'Remote credentials are not allowed');
    }

    if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
        fail(403, 'Remote host is not allowed');
    }

    $resolvedIps = resolveRemoteHost($host);
    if ($resolvedIps === []) {
        fail(404, 'Cannot resolve remote host');
    }

    foreach ($resolvedIps as $ip) {
        if (!isPublicIpAddress($ip)) {
            fail(403, 'Remote host is not allowed');
        }
    }

    $baseDomain = preg_replace('/^www\./', '', $requestHost);
    // các domain được phép
    $allowedDomains = [
    $baseDomain,        // domain hiện tại
    'googleusercontent.com',
    'gstatic.com',
    ];

    $isAllowed = false;
    foreach ($allowedDomains as $domain) {
        if (
            $host === $domain ||
            substr($host, -strlen('.' . $domain)) === '.' . $domain
        ) {
            $isAllowed = true;
            break;
        }
    }

    if (!$isAllowed) {
        fail(403, 'Tên miền không được phép.');
    }
}

function normalizeLocalPath(string $value): string
{
    $publicRoot = realpath(dirname(__DIR__));
    if ($publicRoot === false) {
        fail(500, 'Cannot resolve public root');
    }

    $relativePath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $value), DIRECTORY_SEPARATOR);
    $resolvedPath = realpath($publicRoot . DIRECTORY_SEPARATOR . $relativePath);

    if ($resolvedPath === false || !is_file($resolvedPath)) {
        fail(404, 'File not found');
    }

    $publicRootPrefix = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (strpos($resolvedPath, $publicRootPrefix) !== 0) {
        fail(403, 'Access denied');
    }

    return $resolvedPath;
}

function detectImageMetadataFromData(string $imageData): array
{
    $imageInfo = @getimagesizefromstring($imageData);
    if ($imageInfo === false || empty($imageInfo['mime'])) {
        return [
            'mime' => '',
            'width' => 0,
            'height' => 0,
        ];
    }

    return [
        'mime' => (string) $imageInfo['mime'],
        'width' => (int) ($imageInfo[0] ?? 0),
        'height' => (int) ($imageInfo[1] ?? 0),
    ];
}

function detectLocalImageMetadata(string $path): array
{
    $imageInfo = @getimagesize($path);
    if ($imageInfo === false || empty($imageInfo['mime'])) {
        return [
            'mime' => '',
            'width' => 0,
            'height' => 0,
        ];
    }

    return [
        'mime' => (string) $imageInfo['mime'],
        'width' => (int) ($imageInfo[0] ?? 0),
        'height' => (int) ($imageInfo[1] ?? 0),
    ];
}

function mimeToExt(string $mime): string
{
    switch (strtolower($mime)) {
        case 'image/jpeg':
        case 'image/jpg':
            return 'jpg';

        case 'image/png':
            return 'png';

        case 'image/gif':
            return 'gif';

        case 'image/webp':
            return 'webp';

        case 'image/bmp':
        case 'image/x-ms-bmp':
            return 'bmp';

        default:
            return '';
    }
}

function parsePhpSizeToBytes($value): int
{
    if (!is_string($value) || $value === '' || $value === '-1') {
        return 0;
    }

    $value = trim($value);
    $unit = strtolower(substr($value, -1));
    $number = (float) $value;

    switch ($unit) {
        case 'g':
            return (int) ($number * 1024 * 1024 * 1024);

        case 'm':
            return (int) ($number * 1024 * 1024);

        case 'k':
            return (int) ($number * 1024);

        default:
            return (int) $number;
    }
}

function ensureImageDimensionsAreSafe(int $width, int $height): void
{
    if ($width < 1 || $height < 1) {
        fail(400, 'Invalid image');
    }

    if (($width * $height) > 20000000) {
        fail(413, 'Image dimensions are too large');
    }

    $estimatedBytes = (int) ceil($width * $height * 5);
    $memoryLimitBytes = parsePhpSizeToBytes(ini_get('memory_limit'));

    if ($memoryLimitBytes > 0) {
        $availableBytes = $memoryLimitBytes - memory_get_usage(true);
        if ($estimatedBytes > $availableBytes) {
            fail(413, 'Image requires too much memory to process');
        }
    }
}

function fetchRemoteImage(string $url): array
{
    if (!function_exists('curl_init')) {
        fail(500, 'cURL extension is required');
    }

    $headers = [];
    $ch = curl_init($url);

    if ($ch === false) {
        fail(500, 'Failed to initialize cURL');
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_USERAGENT => 'ImageProxy/1.0',
        CURLOPT_HEADERFUNCTION => static function ($curl, string $headerLine) use (&$headers): int {
            $length = strlen($headerLine);
            $headerLine = trim($headerLine);

            if ($headerLine === '' || strpos($headerLine, ':') === false) {
                return $length;
            }

            [$name, $value] = explode(':', $headerLine, 2);
            $headers[strtolower(trim($name))] = trim($value);

            return $length;
        },
    ]);

    $data = curl_exec($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    //if ($data === false || $error !== '') {
    //    fail(404, 'Download failed');
    //}
    if ($data === false || $error !== '') {
    fail(502, 'cURL error: ' . $error);
    }

   // if ($statusCode < 200 || $statusCode >= 300) {
    //    fail(404, 'Remote image returned an invalid status');
    //}
    if ($statusCode < 200 || $statusCode >= 300) {
    fail($statusCode, 'Remote server returned HTTP ' . $statusCode);
     }

    if (strlen($data) > 20 * 1024 * 1024) {
        fail(413, 'Image too large');
    }

    return [
        'data' => $data,
        'headers' => $headers,
    ];
}

function createImage(string $ext, string $value, bool $fromString)
{
    if ($fromString) {
        return @imagecreatefromstring($value);
    }

    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            return @imagecreatefromjpeg($value);

        case 'png':
            return @imagecreatefrompng($value);

        case 'gif':
            return @imagecreatefromgif($value);

        case 'webp':
            return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($value) : false;

        case 'bmp':
            return function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($value) : false;

        default:
            return false;
    }
}

function preserveTransparency($image): void
{
    imagealphablending($image, false);
    imagesavealpha($image, true);
}

$url = trim((string) ($_GET['url'] ?? ''));

if ($url === '') {
    fail(400, 'Missing url');
}

if (!extension_loaded('gd')) {
    fail(500, 'GD extension is required');
}

$acceptWebP = supportsWebP();
$isRemote = isRemoteUrl($url);
$mime = '';
$ext = '';
$sourceVersion = '';
$sourceLastModified = null;
$data = null;
$path = null;
$width = 0;
$height = 0;

if ($isRemote) {
    validateRemoteUrl($url);
    $remote = fetchRemoteImage($url);
    $data = $remote['data'];
    $meta = detectImageMetadataFromData($data);
    ensureImageDimensionsAreSafe($meta['width'], $meta['height']);
    $mime = $meta['mime'];
    $width = $meta['width'];
    $height = $meta['height'];
    $ext = mimeToExt($mime);

    if ($ext === '') {
        fail(400, 'Invalid image');
    }

    $etagHeader = $remote['headers']['etag'] ?? '';
    $lastModifiedHeader = $remote['headers']['last-modified'] ?? '';
    $sourceVersion = is_string($etagHeader) && $etagHeader !== '' ? $etagHeader : md5($data);
    $sourceLastModified = is_string($lastModifiedHeader) ? strtotime($lastModifiedHeader) ?: null : null;
} else {
    $path = normalizeLocalPath($url);
    $meta = detectLocalImageMetadata($path);
    ensureImageDimensionsAreSafe($meta['width'], $meta['height']);
    $mime = $meta['mime'];
    $width = $meta['width'];
    $height = $meta['height'];
    $ext = mimeToExt($mime);

    if ($ext === '') {
        fail(400, 'Invalid image');
    }

    $sourceLastModified = @filemtime($path) ?: null;
    $sourceVersion = $sourceLastModified !== null ? (string) $sourceLastModified : ((string) (@md5_file($path) ?: ''));
}

$etag = md5($url . '|' . $quality . '|' . ($acceptWebP ? 'webp' : 'orig') . '|' . $sourceVersion);

header('Cache-Control: public, max-age=31536000, s-maxage=31536000');
header('Vary: Accept');
header('ETag: "' . $etag . '"');

if ($sourceLastModified !== null) {
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $sourceLastModified) . ' GMT');
}

if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    $clientEtag = trim((string) $_SERVER['HTTP_IF_NONE_MATCH'], '"');
    if ($clientEtag === $etag) {
        http_response_code(304);
        exit;
    }
}

if ($sourceLastModified !== null && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    $ifModifiedSince = strtotime((string) $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if ($ifModifiedSince !== false && $ifModifiedSince >= $sourceLastModified) {
        http_response_code(304);
        exit;
    }
}

if (!$acceptWebP || $ext === 'webp') {
    header('Content-Type: ' . ($ext === 'webp' ? 'image/webp' : $mime));

    if ($isRemote) {
        header('Content-Length: ' . strlen((string) $data));
        echo $data;
        exit;
    }

    header('Content-Length: ' . filesize((string) $path));
    readfile((string) $path);
    exit;
}

if (!in_array($ext, $convertibleFormats, true) || ($width * $height) > $maxConvertPixels) {
    header('Content-Type: ' . $mime);

    if ($isRemote) {
        header('Content-Length: ' . strlen((string) $data));
        echo $data;
        exit;
    }

    header('Content-Length: ' . filesize((string) $path));
    readfile((string) $path);
    exit;
}

$img = $isRemote ? createImage($ext, (string) $data, true) : createImage($ext, (string) $path, false);

if ($img === false) {
    fail(500, 'Image decode failed');
}

if (in_array($ext, ['png', 'webp'], true)) {
    preserveTransparency($img);
}

ob_start();
imagewebp($img, null, $quality);
$out = (string) ob_get_clean();
imagedestroy($img);

header('Content-Type: image/webp');
header('Content-Length: ' . strlen($out));

echo $out;
