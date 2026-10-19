<?php
require_once __DIR__ . '/../includes/functions.php';
redirectIfNotLoggedIn();
if ($_SESSION['user_role'] !== 'client') {
    header('Location: /studentlance/pages/dashboard-student.php');
    exit();
}
$pdo = Database::getInstance()->getConnection();
$userData = getUserData($_SESSION['user_id']);

$stmt = $pdo->prepare("SELECT b.*, p.title as project_title FROM bids b JOIN projects p ON b.project_id = p.id WHERE b.student_id = ? ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bids = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head><meta charset="UTF-8"><title><?php echo __('client_dashboard'); ?> - StudentLance</title><link rel="stylesheet" href="/studentlance/assets/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="dashboard-container">
    <h1><?php echo __('welcome_student'); ?>, <?php echo $userData['full_name']; ?>!</h1>
    <p>💼 أنت تقدم خدماتك كمستقل. هذه لوحة أرباحك وعروضك.</p>

    <div class="dashboard-stats">
        <div class="stat-card"><h3><?php echo __('your_earnings'); ?></h3><div class="stat-value">$<?php echo number_format($userData['total_earnings'],2); ?></div></div>
        <div class="stat-card"><h3><?php echo __('your_rating'); ?></h3><div class="stat-value"><?php echo number_format($userData['rating'],1); ?>/5</div></div>
        <div class="stat-card"><h3><?php echo __('your_completed'); ?></h3><div class="stat-value"><?php echo $userData['completed_projects']; ?></div></div>
        <div class="stat-card"><h3><?php echo __('your_bids'); ?></h3><div class="stat-value"><?php echo count($bids); ?></div></div>
    </div>

    <div class="dashboard-actions">
        <a href="/studentlance/pages/browse-projects.php" class="btn btn-primary"><?php echo __('find_projects'); ?></a>
        <a href="/studentlance/pages/profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-outline"><?php echo __('profile'); ?></a>
    </div>

    <div class="dashboard-section">
        <h2><?php echo __('your_bids'); ?></h2>
        <?php if (empty($bids)): ?>
            <p><?php echo __('no_bids'); ?> <a href="/studentlance/pages/browse-projects.php">تصفح الطلبات</a></p>
        <?php else: ?>
            <?php foreach ($bids as $bid): ?>
                <div class="bid-card">
                    <h4><?php echo $bid['project_title']; ?></h4>
                    <span>$<?php echo number_format($bid['amount'],2); ?></span>
                    <span class="badge badge-<?php echo $bid['status']; ?>"><?php echo __($bid['status']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>