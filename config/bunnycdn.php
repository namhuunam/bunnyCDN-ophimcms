<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bunny CDN Configuration
    |--------------------------------------------------------------------------
    */

    // Bật/tắt Bunny CDN
    'enable' => env('BUNNY_CDN_ENABLE', true),
    
    // Domain của Bunny CDN
    'cdn_domain' => env('BUNNY_CDN_DOMAIN', 'cdn.phimchillz.site'),
    
    // API key để sử dụng khi xóa cache
    'api_key' => env('BUNNY_CDN_API_KEY', ''),
    
    // ID Pull Zone
    'pull_zone_id' => env('BUNNY_CDN_PULL_ZONE_ID', ''),
    
    // Version cho cache busting
    'version' => '1.0',
];