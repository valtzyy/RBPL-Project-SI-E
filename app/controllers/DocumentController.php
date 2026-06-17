<?php
// controllers/DocumentController.php
$lokasiDicari = ROOT_PATH . '/app/services/CloudinaryService.php';

if (!file_exists($lokasiDicari)) {
    echo "<h2>🚨 DETEKTIF PHP: FILE TIDAK KETEMU!</h2>";
    echo "Aplikasi mencari ke: <code>" . $lokasiDicari . "</code> tetapi TIDAK ADA.<br><br>";

    $folderServices = ROOT_PATH . '/app/services';
    if (is_dir($folderServices)) {
        echo "<b>Isi dari folder 'app/services' saat ini adalah:</b><br><ul>";
        $files = scandir($folderServices);
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..') echo "<li>" . $f . "</li>";
        }
        echo "</ul>";
        echo "Apakah nama file di atas ada yang beda huruf kapitalnya? (Harus persis: <code>CloudinaryService.php</code>)";
    } else {
        echo "<b>Gawat:</b> Folder <code>app/services</code> ternyata tidak ditemukan/salah nama folder!";
    }
    die();
}

require_once $lokasiDicari;

class DocumentController
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new CloudinaryService();
    }

    public function tampilkanForm()
    {
        // Kita tes apakah file HTML-nya ketemu
        $pathView = __DIR__ . '/../views/form_upload.php';

        if (!file_exists($pathView)) {
            echo "Sinyal Putus! File tidak ditemukan di path: " . $pathView;
            die();
        }

        include $pathView;
    }

    public function simpanDokumen()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            
            $base64Data = $_POST['file_base64'] ?? null;

            if (empty($base64Data)) {
                die("Terjadi Kesalahan: File belum dipilih atau gagal diproses.");
            }

            try {
                // 2. KIRIM KE CLOUDINARY (File asli otomatis dirakit jadi gambar lagi di sana)
                // Menggunakan service yang sudah kita bypass SSL-nya kemarin
                $publicId = $this->cloudinary->uploadPrivateImage($base64Data, 'arsip_rahasia');

                if (!$publicId) {
                    throw new Exception("Gagal mendapatkan Public ID dari Cloudinary.");
                }
                echo "<h3>Upload Berhasil!</h3>";
                // var_dump($publicId); // Debug: Tampilkan Public ID yang diterima dari Cloudinary
                // 3. SIMPAN PUBLIC ID KE DATABASE AIVEN CLOUD
                // Ambil koneksi PDO Aiven lu (silakan sesuaikan method panggilannya, misal $this->db)
                $db = $this->getAivenConnection();

                // Contoh query: sesuaikan 'documents', 'cloudinary_id', dll dengan struktur tabel lu
                // $query = "INSERT INTO documents (cloudinary_id, nama_file, created_at) VALUES (:cloudinary_id, :nama_file, NOW())";
                // $stmt = $db->prepare($query);

                // $stmt->execute([
                //     ':cloudinary_id' => $publicId, // String pendek dari Cloudinary, misal: 'arsip_rahasia/p9x8q7...'
                //     ':nama_file'     => 'Dokumen Rahasia ' . time()
                // ]);

                // 4. REDIRECT KE HALAMAN VIEW
                header("Location: /document/view?id=" . urlencode($publicId));
                exit();
            } catch (Exception $e) {
                // Jika ada masalah di Cloudinary atau Query Aiven, eror aslinya akan tercetak di sini
                echo "<h3>Aplikasi Crash di Sini:</h3>";
                echo "<pre style='color:red; background:#fee; padding:10px;'>" . $e->getMessage() . "</pre>";
                die();
            }
        }
    }

    /**
     * Method pembantu untuk koneksi ke Aiven DB Cloud (Sesuaikan dengan milik kelompok lu)
     */
    private function getAivenConnection()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        // Pastikan SSL diaktifkan jika Aiven lu mewajibkannya
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    // PERUBAHAN DI SINI: Fungsi tidak perlu menerima argumen $id langsung dari router
    public function bukaDokumen()
    {
        // Kita ambil langsung menggunakan $_GET['id'] bawaan PHP
        $id = isset($_GET['id']) ? $_GET['id'] : '';
    
        if (empty($id)) {
            die("ID Dokumen tidak ditemukan!");
        }

        try {
            $publicId = urldecode($id);
            $linkAman = $this->cloudinary->getPrivateImageUrl($publicId, 300);

            include __DIR__ . '/../views/detail.php';
        } catch (Exception $e) {
            die("Gagal memuat dokumen: " . $e->getMessage());
        }
    }
}
