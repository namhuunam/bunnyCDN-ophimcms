<?php

namespace App\Helpers;

class BunnyCDN
{
    /**
     * Chuyển đổi URL gốc thành URL Bunny CDN
     *
     * @param string|null $url
     * @return string|null
     */
    public static function convertUrl(?string $url): ?string
    {
        if (empty($url) || !config('bunnycdn.enable')) {
            return $url;
        }

        // Kiểm tra nếu URL đã là CDN URL
        if (strpos($url, config('bunnycdn.cdn_domain')) !== false) {
            return $url;
        }
        
        // Kiểm tra để tránh lặp đường dẫn
        if (substr_count($url, '/storage/') > 1) {
            $parts = explode('/storage/', $url);
            if (count($parts) > 1) {
                $url = $parts[0] . '/storage/' . $parts[1];
            }
        }

        // Đảm bảo URL là đầy đủ
        if (strpos($url, 'http') !== 0) {
            if (strpos($url, '/') === 0) {
                $url = rtrim(config('app.url'), '/') . $url;
            } else {
                $url = rtrim(config('app.url'), '/') . '/' . $url;
            }
        }

        // Thay thế domain chính bằng domain CDN
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        $cdnDomain = config('bunnycdn.cdn_domain');
        
        return str_replace($mainDomain, $cdnDomain, $url);
    }

    /**
     * Chỉ chuyển đổi URL mà không thêm tham số tối ưu hóa
     *
     * @param string|null $url
     * @return string|null
     */
    public static function optimizeImage(?string $url): ?string
    {
        // Chỉ chuyển đổi URL, không thêm tham số tối ưu hóa
        return self::convertUrl($url);
    }
}