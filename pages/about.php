<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head><meta charset="UTF-8"><title><?php echo __('about_title'); ?> - StudentLance</title><link rel="stylesheet" href="/studentlance/assets/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-container">
    <h1><?php echo __('about_title'); ?></h1>
    <p><?php echo __('about_text'); ?></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>