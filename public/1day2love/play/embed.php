<?php
// /play/embed.php?url=link
// Enhanced M3U8 processor with convertv7 removal, discontinuity cleanup and security improvements
if (empty($_SERVER['HTTP_REFERER'])) {
    http_response_code(404);
    exit('Not Found');
}
// Cấu hình bảo mật
define('MAX_REQUESTS_PER_MINUTE', 1000);
define('ALLOWED_DOMAINS', [
    // Thêm các domain được phép vào đây nếu cần
    // 'trusted-domain.com',
    // 'another-trusted.com'
]);

// Rate limiting đơn giản
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cacheFile = sys_get_temp_dir() . '/m3u8_rate_' . md5($ip);
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        $currentTime = time();
        
        // Reset counter nếu đã qua 1 phút
        if ($currentTime - $data['timestamp'] >= 600) {
            $data = ['count' => 0, 'timestamp' => $currentTime];
        }
        
        if ($data['count'] >= MAX_REQUESTS_PER_MINUTE) {
            http_response_code(429);
            exit('Rate limit exceeded. Please try again later.');
        }
        
        $data['count']++;
    } else {
        $data = ['count' => 1, 'timestamp' => time()];
    }
    
    file_put_contents($cacheFile, json_encode($data));
}

// Kiểm tra input ngay từ đầu
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    exit('URL parameter is required');
}

// Áp dụng rate limiting
//checkRateLimit();

$url = trim($_GET['url']);

// Enhanced URL validation
function validateUrl($url) {
    // Kiểm tra định dạng URL cơ bản
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Kiểm tra protocol
    $parsedUrl = parse_url($url);
    if (!in_array($parsedUrl['scheme'] ?? '', ['http', 'https'])) {
        return false;
    }
    
    // Kiểm tra có phải M3U8 không
    if (stripos($url, '.m3u8') === false) {
        return false;
    }
    
    // Kiểm tra domain whitelist nếu được cấu hình
    if (!empty(ALLOWED_DOMAINS)) {
        $domain = $parsedUrl['host'] ?? '';
        $allowed = false;
        foreach (ALLOWED_DOMAINS as $allowedDomain) {
            if ($domain === $allowedDomain || str_ends_with($domain, '.' . $allowedDomain)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return false;
        }
    }
    
    return true;
}

if (!validateUrl($url)) {
    http_response_code(400);
    exit('Invalid M3U8 URL');
}

// Headers với bảo mật tăng cường
header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, User-Agent');
header('Access-Control-Max-Age: 86400');
header('Cache-Control: public, max-age=120, must-revalidate, stale-while-revalidate=31536000');
//header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true); //block bot

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function fetchM3U8($url) {
    
    // Ưu tiên cURL
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10, // Giảm timeout
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.apple.mpegurl,*/*',
                'Accept-Encoding: gzip, deflate',
                //'Connection: close',
                'Accept-Language: en-US,en;q=0.9'
            ],
            CURLOPT_ENCODING => '',
            CURLOPT_TCP_NODELAY => 1,
            //CURLOPT_FORBID_REUSE => true
        ]);
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200 && $content !== false) {
            return $content;
        }
        
        return false;
    }
    
    // Fallback với file_get_contents
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'header' => [
                'Accept: application/vnd.apple.mpegurl,*/*',
                'Accept-Language: en-US,en;q=0.9',
                'Connection: close'
            ]
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    
    return $content;
}

function isMasterPlaylist($content) {
    return strpos($content, '#EXT-X-STREAM-INF:') !== false;
}

function getSubPlaylistUrl($content, $baseUrl) {
    $lines = explode("\n", $content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        if (strpos($line, '.m3u8') !== false) {
            if (!startsWith($line, 'http://') && !startsWith($line, 'https://')) {
                return rtrim($baseUrl, '/') . '/' . ltrim($line, '/');
            }
            return $line;
        }
    }
    
    return null;
}

function hasSegmentsToFilter($content) {
    // Kiểm tra segments 0001-0011
    $skipPatterns = [];
    for ($i = 1; $i <= 11; $i++) {
        $skipPatterns[] = sprintf('segment_%04d.ts', $i);
    }
    
    foreach ($skipPatterns as $pattern) {
        if (strpos($content, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}
// chỉ loại bỏ convertv7
/*
function removeConvertV7FromSegments($content) {
    // Loại bỏ "convertv7/" từ các segment URLs
    $lines = explode("\n", $content);
    $processedLines = [];
    
    foreach ($lines as $line) {
        $originalLine = $line;
        $line = trim($line);
        
        // Nếu không phải comment và chứa convertv7/
        if (!empty($line) && strpos($line, '#') !== 0) {
            // Loại bỏ convertv7/ từ đường dẫn
            if (strpos($line, 'convertv7/') !== false) {
                $line = str_replace('convertv7/', '', $line);
                error_log("Removed convertv7/ from segment: " . basename($line));
            }
        }
        
        $processedLines[] = $line;
    }
    
    return implode("\n", $processedLines);
}
*/
// Loại bỏ tất cả convertv7,8,9,...
function removeConvertVersionsFromSegments($content) {
    $lines = explode("\n", $content);
    $processedLines = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if (!empty($line) && strpos($line, '#') !== 0) {
            // Xóa tất cả dạng convertvX/
            $line = preg_replace('#convertv\d+/#i', '', $line);
        }

        $processedLines[] = $line;
    }

    return implode("\n", $processedLines);
}
// NEW FUNCTION: Loại bỏ tất cả các #EXT-X-DISCONTINUITY (quảng cáo)
function removeAllDiscontinuityTags($content) {
    $lines = explode("\n", $content);
    $cleanedLines = [];
    $removedCount = 0;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Loại bỏ hoàn toàn tất cả #EXT-X-DISCONTINUITY
        if ($line === '#EXT-X-DISCONTINUITY') {
            $removedCount++;
            continue; // Bỏ qua dòng này
        }
        
        $cleanedLines[] = $line;
    }
    
    //if ($removedCount > 0) {
     //   error_log("Removed $removedCount #EXT-X-DISCONTINUITY advertisement tags");
    //}
    
    return implode("\n", $cleanedLines);
}

function filterM3U8Segments($content, $baseUrl) {
    // Đầu tiên loại bỏ convertv7/
    //$content = removeConvertV7FromSegments($content); // mở hàm này ở trên thì mở lại
    $content = removeConvertVersionsFromSegments($content);
    
    // Loại bỏ tất cả các #EXT-X-DISCONTINUITY (quảng cáo)
    $content = removeAllDiscontinuityTags($content);
    
    // Kiểm tra xem có cần filter segments 0001-0011 không
    if (!hasSegmentsToFilter($content)) {
        return processRelativeUrls($content, $baseUrl);
    }
    
    $lines = explode("\n", $content);
    $filteredLines = [];
    $skipNext = false;
    //$filteredCount = 0;
    
    // Patterns để loại bỏ segments từ 0001 đến 0011
    $skipPatterns = [];
    for ($i = 1; $i <= 11; $i++) {
        $skipPatterns[] = sprintf('segment_%04d.ts', $i);
    }
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Xử lý filtering segments
        if ($skipNext) {
            $skipNext = false;
            
            $shouldSkip = false;
            foreach ($skipPatterns as $pattern) {
                if (strpos($line, $pattern) !== false) {
                    $shouldSkip = true;
                    //$filteredCount++;
                    break;
                }
            }
            
            if ($shouldSkip) {
                array_pop($filteredLines);
                continue;
            }
        }
        
        if (strpos($line, '#EXTINF:') === 0) {
            $skipNext = true;
        } else {
            $skipNext = false;
        }
        
        // Xử lý URL tương đối
        if (!empty($line) && 
            !startsWith($line, '#') && 
            !startsWith($line, 'http://') && 
            !startsWith($line, 'https://') &&
            (strpos($line, '.ts') !== false || strpos($line, '.m4s') !== false)) {
            
            $line = rtrim($baseUrl, '/') . '/' . ltrim($line, '/');
        }
        
        $filteredLines[] = $line;
    }
    
   // if ($filteredCount > 0) {
    //    error_log("Filtered $filteredCount segments (segment_0001.ts to segment_0011.ts)");
    //}
    
    return implode("\n", $filteredLines);
}

function processRelativeUrls($content, $baseUrl) {
    $lines = explode("\n", $content);
    $processedLines = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (!empty($line) && 
            !startsWith($line, '#') && 
            !startsWith($line, 'http://') && 
            !startsWith($line, 'https://') &&
            (strpos($line, '.ts') !== false || strpos($line, '.m4s') !== false)) {
            
            $line = rtrim($baseUrl, '/') . '/' . ltrim($line, '/');
        }
        
        $processedLines[] = $line;
    }
    
    return implode("\n", $processedLines);
}

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function getBaseUrl($url) {
    $parsedUrl = parse_url($url);
    if (!$parsedUrl) {
        return $url;
    }
    
    $baseUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
    
    if (isset($parsedUrl['port'])) {
        $baseUrl .= ':' . $parsedUrl['port'];
    }
    
    $path = $parsedUrl['path'] ?? '';
    $pathInfo = pathinfo($path);
    
    if (isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.') {
        $baseUrl .= $pathInfo['dirname'];
    }
    
    return $baseUrl;
}

function logError($message, $url = '') {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($url)) {
        $logMessage .= " - URL: " . $url;
    }
    error_log($logMessage);
}

// Main execution với error handling tốt hơn
try {
    $content = fetchM3U8($url);
    
    if ($content === false) {
        throw new Exception('Failed to fetch M3U8 content from URL');
    }
    
    // Kiểm tra định dạng M3U8
    if (strpos($content, '#EXTM3U') === false) {
        throw new Exception('Invalid M3U8 format - missing #EXTM3U header');
    }
    
    $baseUrl = getBaseUrl($url);
    
    if (isMasterPlaylist($content)) {
        // Xử lý master playlist
        $subPlaylistUrl = getSubPlaylistUrl($content, $baseUrl);
        
        if ($subPlaylistUrl === null) {
            throw new Exception('Could not find sub playlist URL in master playlist');
        }
        
        $subContent = fetchM3U8($subPlaylistUrl);
        
        if ($subContent === false) {
            throw new Exception('Failed to fetch sub playlist content');
        }
        
        $subBaseUrl = getBaseUrl($subPlaylistUrl);
        $processedContent = filterM3U8Segments($subContent, $subBaseUrl);
        
        //logError("Master playlist processed successfully", $url);
        
    } else {
        // Xử lý playlist thường
        $processedContent = filterM3U8Segments($content, $baseUrl);
        //logError("Regular playlist processed successfully", $url);
    }
    
    // Validate kết quả
    if (empty($processedContent)) {
        throw new Exception('Processed content is empty');
    }
    
    echo $processedContent;
    
} catch (Exception $e) {
    // Trả về lỗi dưới dạng M3U8 với thông tin chi tiết hơn
    http_response_code(500);
    
    $errorM3U8 = "#EXTM3U\n";
    $errorM3U8 .= "#EXT-X-VERSION:3\n";
    $errorM3U8 .= "#EXT-X-TARGETDURATION:10\n";
    $errorM3U8 .= "#EXT-X-MEDIA-SEQUENCE:0\n";
    $errorM3U8 .= "# Error: " . htmlspecialchars($e->getMessage()) . "\n";
    $errorM3U8 .= "# Time: " . date('Y-m-d H:i:s') . "\n";
    $errorM3U8 .= "#EXT-X-ENDLIST\n";
    
    echo $errorM3U8;
    
    // Log chi tiết
    logError("M3U8 Processing Error: " . $e->getMessage(), $url);
}
?>