### 1. Thiết lập trên Bunny CDN Dashboard
1 Tạo Pull Zone:

- Origin URL: https://phimchillz.site (URL gốc của website)
- Name: PhimChillz (hoặc tên bạn muốn đặt)
- Type: Standard Pull Zone
2 Cấu hình Pull Zone:

- Enable Smart Cache: Bật
- Enable Aggressive Tiered Cache: Bật
- Force SSL: Bật
- Query String Sort: Bật
- Disable Cookie: Bật cho static content
- Browser Cache Expiry Time: 14 days
### 2. Tạo file cấu hình cho Bunny CDN
config/bunnycdn.php
### 3. Tạo Helper Class BunnyCDN
app/Helpers/BunnyCDN.php
### 4. Tạo Middleware để xử lý HTML Output
app/Http/Middleware/BunnyCDNMiddleware.php
### 5. Tạo Command để xóa cache CDN
app/Console/Commands/PurgeCDNCache.php
### 6. Đăng ký Middleware trong Kernel
Mở file app/Http/Kernel.php và thêm middleware vào nhóm web:
```bash
protected $middlewareGroups = [
    'web' => [
        // Các middleware khác...
        \App\Http\Middleware\BunnyCDNMiddleware::class,
    ],
];
```
### 7. Cập nhật file .env
```bash
# Bunny CDN Configuration
BUNNY_CDN_ENABLE=true
BUNNY_CDN_DOMAIN=cdn.phimchillz.site
BUNNY_CDN_API_KEY=your_api_key_here
BUNNY_CDN_PULL_ZONE_ID=your_pull_zone_id_here
```
### 8. Thêm preconnect hints vào layout
Trong tệp layout chính của bạn (thường là resources/views/layouts/app.blade.php hoặc layout tương tự), thêm vào phần <head>:
```bash
<head>
    <!-- Các thẻ khác... -->
    
    <!-- Preconnect để tối ưu kết nối đến Bunny CDN -->
    <link rel="preconnect" href="https://{{ config('bunnycdn.cdn_domain') }}">
    <link rel="dns-prefetch" href="https://{{ config('bunnycdn.cdn_domain') }}">
</head>
```
### 9. Làm mới cấu hình
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
```
#### Tối ưu định kỳ: Chạy lệnh php artisan cdn:purge khi bạn cập nhật hình ảnh mới

