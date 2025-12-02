<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);

// Pastikan baris ini menggunakan konstanta yang baru kita edit
$client->setRedirectUri(GOOGLE_REDIRECT_URL); 

$client->addScope('email');
$client->addScope('profile');

$google_login_url = $client->createAuthUrl();
?>