<?php
// services/CloudinaryService.php

require_once __DIR__ . '/../../config/cloudinary.php';

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use GuzzleHttp\Client as GuzzleClient; // <--- WAJIB IMPORT GUZZLE ASLI

class CloudinaryService
{
    private $uploadApi;
    private $config;

   public function __construct()
    {
        // Path absolut yang paling aman untuk Windows & Linux
        $cfgPath = dirname(__DIR__, 2) . '/config/cloudinary.php';

        if (!file_exists($cfgPath)) {
            throw new Exception("File konfigurasi tidak ditemukan di: " . $cfgPath);
        }

        $cfg = require $cfgPath;

        // Validasi isi array config agar tidak memicu "Undefined array key" (Eror 500)
        $cloudName = $cfg['cloud_name'] ?? $_ENV['CLOUDINARY_CLOUD_NAME'] ?? null;
        $apiKey    = $cfg['api_key'] ?? $_ENV['CLOUDINARY_API_KEY'] ?? null;
        $apiSecret = $cfg['api_secret'] ?? $_ENV['CLOUDINARY_API_SECRET'] ?? null;

        // Set credentials ke Configuration Cloudinary
        $this->config = Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key'    => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => ['secure' => true],
        ]);

        // Guzzle dengan SSL off khusus Cloudinary (Penyelamat cURL Error 60 Windows)
        $brutalHttpClient = new GuzzleClient(['verify' => false]);

        // Paksa UploadApi pakai config + guzzle kustom
        $this->uploadApi = new UploadApi($this->config, $brutalHttpClient);
    }
    /**
     * 1. Fungsi untuk Upload Gambar secara PRIVATE
     */
    public function uploadPrivateImage($fileTmpPath, $folderName)
    {
        try {
            $response = $this->uploadApi->upload($fileTmpPath, [
                'folder' => $folderName,
                'type'   => 'private',
                'overwrite' => true,
            ]);

            return $response['public_id'];
        } catch (Exception $e) {
            throw new Exception("Cloudinary Error: " . $e->getMessage());
        }
    }

    /**
     * 2. Fungsi untuk membuat link sementara yang bisa dibuka
     */
    public function getPrivateImageUrl($publicId, $expiration = 600)
    {
        try {
            $cld = new Cloudinary($this->config);
            $waktuHangus = time() + $expiration;

            $url = $cld->image($publicId)
                ->deliveryType('private')
                ->signUrl(true, ['expires_at' => time() + $expiration])
                ->signUrl(true)           
                ->addTransformation("expires_at=$waktuHangus") 
                ->toUrl();

            return $url;
        } catch (Exception $e) {
            throw new Exception("Gagal membuat link aman: " . $e->getMessage());
        }
    }
}