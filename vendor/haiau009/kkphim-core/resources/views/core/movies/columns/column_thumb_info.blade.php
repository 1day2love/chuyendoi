@php
$thumb_url = data_get($entry, 'thumb_url'); // Lấy URL hình ảnh từ cơ sở dữ liệu
// Lấy miền hiện tại
$current_domain = $_SERVER['HTTP_HOST'];
// Kiểm tra nếu $thumb_url là một URL bắt đầu bằng http:// hoặc https://
$is_url = preg_match('/^https?:\/\//', $thumb_url);
// Chuyển đổi URL hình ảnh để sử dụng miền img.domain.com
if (!$is_url) {
    //$thumb_url = preg_replace('/^https?:\/\/[^\/]+/', 'https://img.' . $current_domain, $thumb_url);
    $thumb_url = 'https://img.' . $current_domain . '/' . ltrim(preg_replace('/^\/storage\//', '', $thumb_url), '/');
}
@endphp

<div class="">
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')

    <td><span>
      <a href="{{ $thumb_url }}" target="_blank">
        <img src="{{ $thumb_url }}" alt="Ảnh thumb" style="max-height: 100px; width: 68px; border-radius: 3px;">
    </a>
  </span>
</td>

    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</div>