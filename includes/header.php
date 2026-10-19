<?php
$lang = getLanguage();
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentLance - <?php echo __('site_title'); ?></title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/studentlance/index.php" class="nav-logo">
                ⚡ Student<span>Lance</span>
            </a>
            <ul class="nav-links">
                <li><a href="/studentlance/pages/browse-projects.php"><?php echo __('browse_projects'); ?></a></li>
                <li><a href="/studentlance/pages/browse-freelancers.php"><?php echo __('find_freelancers'); ?></a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if ($_SESSION['user_role'] == 'student'): ?>
                        <li><a href="/studentlance/pages/dashboard-student.php"><?php echo __('dashboard'); ?></a></li>
                    <?php else: ?>
                        <li><a href="/studentlance/pages/dashboard-client.php"><?php echo __('dashboard'); ?></a></li>
                    <?php endif; ?>
                    <li><a href="/studentlance/auth/logout.php"><?php echo __('logout'); ?></a></li>
                <?php else: ?>
                    <li><a href="/studentlance/auth/login.php"><?php echo __('login'); ?></a></li>
                    <li><a href="/studentlance/auth/register.php"><?php echo __('register'); ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-actions">
                <a href="/studentlance/includes/toggle-lang.php" class="lang-toggle">
                    <?php echo __('language_toggle'); ?>
                </a>
            </div>
        </div>
    </nav>