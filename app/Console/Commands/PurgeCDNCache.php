<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PurgeCDNCache extends Command
{
    protected $signature = 'cdn:purge {url? : URL cụ thể để xóa cache}';
    protected $description = 'Xóa cache từ Bunny CDN';

    public function handle()
    {
        $apiKey = config('bunnycdn.api_key');
        $pullZoneId = config('bunnycdn.pull_zone_id');
        
        if (!$apiKey || !$pullZoneId) {
            $this->error('BUNNY_CDN_API_KEY hoặc BUNNY_CDN_PULL_ZONE_ID chưa được cấu hình!');
            return 1;
        }

        $url = $this->argument('url');
        
        if ($url) {
            // Xóa cache cho URL cụ thể
            $response = Http::withHeaders([
                'AccessKey' => $apiKey,
                'Accept' => 'application/json',
            ])->delete("https://api.bunny.net/purge?url={$url}");
            
            if ($response->successful()) {
                $this->info("Đã xóa cache cho URL: {$url}");
                return 0;
            }
            
            $this->error("Không thể xóa cache cho URL: {$url}");
            $this->error($response->body());
            return 1;
        }
        
        // Xóa toàn bộ cache của Pull Zone
        $response = Http::withHeaders([
            'AccessKey' => $apiKey,
            'Accept' => 'application/json',
        ])->post("https://api.bunny.net/pullzone/{$pullZoneId}/purgeCache");
        
        if ($response->successful()) {
            $this->info('Đã xóa toàn bộ cache của Pull Zone!');
            // Tăng version lên để thực hiện cache busting
            $this->updateCDNVersion();
            return 0;
        }
        
        $this->error('Không thể xóa cache Pull Zone!');
        $this->error($response->body());
        return 1;
    }
    
    private function updateCDNVersion()
    {
        $configPath = config_path('bunnycdn.php');
        $config = file_get_contents($configPath);
        
        $version = config('bunnycdn.version', 1.0);
        $newVersion = $version + 0.1;
        
        $config = preg_replace(
            "/('version'\s*=>\s*)([0-9.]+)/",
            "$1{$newVersion}",
            $config
        );
        
        file_put_contents($configPath, $config);
        $this->info("Cập nhật phiên bản CDN thành {$newVersion}");
    }
}