<?php
// services/CloudinaryService.php

require_once __DIR__ . '/../../config/cloudinary.php';

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use GuzzleHttp\Client as GuzzleClient; // <--- WAJIB IMPORT GUZZLE ASLI

class CloudinaryService
{
    private UploadApi $uploadApi;
    private Configuration $config;

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

        // Guzzle dengan SSL on khusus Cloudinary (Penyelamat cURL Error 60 Windows)
        $brutalHttpClient = new GuzzleClient([
            'verify' => dirname(__DIR__, 2) . '/config/certs/cacert.pem'
        ]);

        // Paksa UploadApi pakai config. UploadApi hanya menerima satu argumen (configuration)
        // Guzzle client is created to ensure SSL cert is available for other Cloudinary operations if needed.
        $this->uploadApi = new UploadApi($this->config);
    }
    /**
     * Upload img ke cloudinary dengan delivery type 'private' (File asli tetap rahasia, tapi bisa dibuat link sementara yang aman)
     */
    /**
     * @param string $fileTmpPath Temporary file path or file resource string
     * @param string $folderName Destination folder name in Cloudinary
     * @return string Public ID of uploaded resource
     */
    public function uploadPrivateImage(string $fileTmpPath, string $folderName): string
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
     *  Dapatkan gambar dari public Id yg tersimpan di file_path
     */
    /**
     * @param string $publicId Public ID in Cloudinary
     * @param int $expiration Link expiration in seconds
     * @return string Signed URL
     */
    public function getPrivateImageUrl(string $publicId, int $expiration = 600): string
    {
        try {
            $cld = new Cloudinary($this->config);
            $url = $cld->image($publicId)
                ->deliveryType('private')
                ->signUrl(true, ['expires_at' => time() + $expiration])
                ->toUrl();

            return $url;
        } catch (Exception $e) {
            throw new Exception("Gagal membuat link aman: " . $e->getMessage());
        }
    }

    // public function uploadCreditDocument($fileTmpPath, $docType)
    // {
    //     // Whitelist tipe dokumen yang diizinkan
    //     $allowed = ['KTP', 'KK', 'SlipGaji'];
    //     if (!in_array($docType, $allowed, true)) {
    //         throw new InvalidArgumentException("Tipe dokumen tidak valid: {$docType}");
    //     }

    //     // Delegasikan ke method generic; folder auto-build: credit/{docType}
    //     return $this->uploadPrivateImage($fileTmpPath, "credit/{$docType}");
    // }
}
