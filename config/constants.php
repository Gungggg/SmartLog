<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

define('GROQ_API_KEY', $_ENV['GROQ_API_KEY'] ?? '');

// GOOGLE OAUTH CONFIG
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');

// PERBAIKAN: Gunakan 'localhost' agar diterima Google
define('GOOGLE_REDIRECT_URL', 'http://localhost:2005/smartlog/helpers/google_callback.php');

if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    die('Akses langsung tidak diizinkan.');
}

define('BASE_URL', 'http://localhost:2005/smartlog/');

define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

date_default_timezone_set('Asia/Makassar');

define('APP_NAME', 'SmartLog');

?>