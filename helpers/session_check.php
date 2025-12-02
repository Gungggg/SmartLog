<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 180;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $duration = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($duration > $timeout_duration) {
        session_unset();
        session_destroy();
        
        header("Location: index.php?error=session_expired");
        exit;
    }
}
$_SESSION['LAST_ACTIVITY'] = time();
?>