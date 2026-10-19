<?php
session_start();

if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
    $_SESSION['lang'] = 'en';
} else {
    $_SESSION['lang'] = 'ar';
}

// العودة للصفحة السابقة
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>