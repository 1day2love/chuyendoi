https://github.com/hacoidev/ophim-core
https://github.com/hacoidev/ophim-crawler
- Cài đặt OPhimCMS trên AAPanel
- Cài đặt môi trường (nếu chưa cài đặt)
 + Php 7.4
 + Nginx nên cài 1.21
 + Mysql 5.7
 + Redis (tùy chọn) 7.0
 + Phpmyadmin 5.0

- Thêm website & tạo database

- Cấu hình PHP
 + Install extensions: fileinfo, opcache (tùy chọn), redis (tùy chọn), exif, intl
 + Xóa disable functions: putenv, symlink, proc_open

- Tiến hành cài đặt
 + Cài đặt Laravel qua terminal: composer create-project laravel/laravel cms
 + Cấu hình database, cache (redis - nếu cài redis) trong .env
 + Cài đặt OPhimcms. Xem docs trên git hoặc packagist
 + Phân quyền thư mục Project

 + Cài đặt crawler & giao diện (1 hoặc nhiều cái để thay đổi nếu muốn). Xem docs trên git hoặc packagist

- Cấu hình nginx cho domain
 + Trỏ site directory
 + Url rewrite:
  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

- set ENV production

- Trường hợp không load được giao diện hay những cái khác cần phải để ssl cloudflare Full & cấu hình cer
 + xác tực Origin Server trong cloudflare
 + Cấu hình ssl nginx

- Đã xong! Test thử. Nhớ phải Active 1 giao diện trong admin.
- Các lệnh thường dùng:
  + php artisan optimize:clear (xóa cache)
  + php artisan storage:link (khởi tạo storage mới, nhớ xóa storage cũ ở /public trước)
  + php artisan key:generate (khởi tạo lại APP KEY)