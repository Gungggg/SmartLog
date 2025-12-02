<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/constants.php';
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_smartlog";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($conn, "utf8mb4");

} catch (mysqli_sql_exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die('
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <title>Gangguan Sistem | '.APP_NAME.'</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background-color: #f3f4f6; 
                display: flex; justify-content: center; align-items: center; 
                height: 100vh; margin: 0; 
            }
            .error-card { 
                background: white; padding: 40px; border-radius: 16px; 
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
                text-align: center; max-width: 400px; width: 90%;
            }
            .icon { font-size: 48px; margin-bottom: 20px; }
            h1 { color: #111827; font-size: 24px; font-weight: 700; margin: 0 0 10px 0; }
            p { color: #6b7280; line-height: 1.5; margin-bottom: 24px; }
            .btn { 
                display: inline-block; padding: 12px 24px; 
                background-color: #4f46e5; color: white; 
                text-decoration: none; border-radius: 8px; font-weight: 600; 
                transition: background 0.2s;
            }
            .btn:hover { background-color: #4338ca; }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="icon">🔌</div>
            <h1>Koneksi Terputus</h1>
            <p>Sistem tidak dapat terhubung ke database. Mohon pastikan Laragon (MySQL) sudah aktif.</p>
            <a href="'.BASE_URL.'" class="btn">Coba Muat Ulang</a>
        </div>
    </body>
    </html>
    ');
}
?>