<?php
require_once __DIR__ . '/../includes/functions.php';
$pdo = Database::getInstance()->getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.full_name as client_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) { header('Location: browse-projects.php'); exit; }

// تقديم عرض – للمستقلين فقط (دور client)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && $_SESSION['user_role'] === 'client') {
    $stmt = $pdo->prepare("INSERT INTO bids (project_id, student_id, amount, proposal) VALUES (?,?,?,?)");
    $stmt->execute([$id, $_SESSION['user_id'], floatval($_POST['amount']), sanitize($_POST['proposal'])]);
    $success = "تم تقديم عرضك بنجاح!";
}
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head><meta charset="UTF-8"><title><?php echo $project['title']; ?> - StudentLance</title><link rel="stylesheet" href="/studentlance/assets/style.css"></head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-container">
    <h1><?php echo $project['title']; ?></h1>
    <p><?php echo __('posted_by'); ?>: <?php echo $project['client_name']; ?></p>
    <p><?php echo nl2br($project['description']); ?></p>
    <p><?php echo __('budget'); ?>: $<?php echo $project['budget_min']; ?> - $<?php echo $project['budget_max']; ?></p>
    <p><?php echo __('category'); ?>: <?php echo $project['category']; ?></p>
    <?php if (isset($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if (isLoggedIn() && $_SESSION['user_role'] === 'client'): ?>
        <h3><?php echo __('submit_proposal'); ?></h3>
        <form method="POST">
            <div class="form-group"><label><?php echo __('bid_amount'); ?></label><input type="number" name="amount" required></div>
            <div class="form-group"><label><?php echo __('proposal_text'); ?></label><textarea name="proposal" required></textarea></div>
            <button type="submit" class="btn btn-primary"><?php echo __('apply_now'); ?></button>
        </form>
    <?php elseif (!isLoggedIn()): ?>
        <a href="/studentlance/auth/login.php" class="btn btn-primary"><?php echo __('login_to_apply'); ?></a>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>