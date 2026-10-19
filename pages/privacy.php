<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head><meta charset="UTF-8"><title><?php echo __('privacy_title'); ?> - StudentLance</title><link rel="stylesheet" href="/studentlance/assets/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-container">
    <h1><?php echo __('privacy_title'); ?></h1>
    <p><?php echo __('privacy_last_update'); ?></p>
    <h2><?php echo __('privacy_section1_title'); ?></h2><p><?php echo __('privacy_section1_text'); ?> <?php echo __('privacy_list1'); ?></p>
    <h2><?php echo __('privacy_section2_title'); ?></h2><p><?php echo __('privacy_section2_text'); ?></p>
    <h2><?php echo __('privacy_section3_title'); ?></h2><p><?php echo __('privacy_section3_text'); ?></p>
    <h2><?php echo __('privacy_section4_title'); ?></h2><p><?php echo __('privacy_section4_text'); ?></p>
    <h2><?php echo __('privacy_section5_title'); ?></h2><p><?php echo __('privacy_section5_text'); ?></p>
    <h2><?php echo __('privacy_section6_title'); ?></h2><p><?php echo __('privacy_section6_text'); ?></p>
    <h2><?php echo __('privacy_section7_title'); ?></h2><p><?php echo __('privacy_section7_text'); ?></p>
    <h2><?php echo __('privacy_section8_title'); ?></h2><p><?php echo __('privacy_section8_text'); ?></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>