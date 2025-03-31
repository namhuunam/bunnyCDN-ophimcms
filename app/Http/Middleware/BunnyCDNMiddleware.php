<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BunnyCDNMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (!config('bunnycdn.enable')) {
            return $response;
        }

        if ($this->isHtmlResponse($response)) {
            $content = $response->getContent();
            
            // Lấy thông tin URL
            $appUrl = rtrim(config('app.url'), '/');
            $cdnUrl = 'https://' . config('bunnycdn.cdn_domain');
            
            // Thay thế URL trong thuộc tính src và data-src
            $content = preg_replace(
                '/(src|data-src)=(["\'])(\/storage\/[^"\']+)(["\'])/',
                '$1=$2' . $cdnUrl . '$3$4',
                $content
            );
            
            // Thay thế URL trong background-image styles
            $content = preg_replace(
                '/(background-image\s*:\s*url\s*\(\s*[\'"]?)(\/?storage\/[^\'")]+)([\'"]?\s*\))/',
                '$1' . $cdnUrl . '/$2$3',
                $content
            );
            
            // Thay thế URL tuyệt đối từ domain gốc thành CDN
            $appDomain = parse_url($appUrl, PHP_URL_HOST);
            if ($appDomain) {
                $pattern = '/(src|data-src)=(["\'])(https?:\/\/' . preg_quote($appDomain, '/') . '\/storage\/[^"\']+)(["\'])/';
                $replacement = function ($matches) use ($cdnUrl) {
                    $path = parse_url($matches[3], PHP_URL_PATH);
                    return $matches[1] . '=' . $matches[2] . $cdnUrl . $path . $matches[4];
                };
                $content = preg_replace_callback($pattern, $replacement, $content);
            }
            
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Kiểm tra xem response có phải là HTML không
     */
    protected function isHtmlResponse($response): bool
    {
        if (!$response instanceof Response && !$response instanceof SymfonyResponse) {
            return false;
        }
        
        $contentType = $response->headers->get('Content-Type');
        
        return $contentType && (
            strpos($contentType, 'text/html') !== false ||
            strpos($contentType, 'application/xhtml+xml') !== false
        );
    }
}