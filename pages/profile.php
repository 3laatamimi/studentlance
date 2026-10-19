<?php
require_once __DIR__ . '/../includes/functions.php';
$id = intval($_GET['id'] ?? 0);
$user = getUserData($id);
if (!$user) { header('Location: /studentlance/index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head><meta charset="UTF-8"><title><?php echo $user['full_name']; ?></title><link rel="stylesheet" href="/studentlance/assets/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="profile-container">
    <h1><?php echo $user['full_name']; ?></h1>
    <p><?php echo $user['bio']; ?></p>
    <?php if ($user['role'] == 'client'): // مستقل يقدم خدمات ?>
        <p><?php echo __('skills'); ?>: <?php echo $user['skills']; ?></p>
        <p><?php echo __('hourly_rate'); ?>: $<?php echo $user['hourly_rate']; ?></p>
        <p><?php echo __('rating'); ?>: ⭐ <?php echo $user['rating']; ?>/5</p>
        <p><?php echo __('completed_projects'); ?>: <?php echo $user['completed_projects']; ?></p>
        <p><?php echo __('total_earnings'); ?>: $<?php echo number_format($user['total_earnings'],2); ?></p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>