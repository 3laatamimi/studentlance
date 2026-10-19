<?php
require_once __DIR__ . '/../includes/functions.php';
redirectIfNotLoggedIn();
if ($_SESSION['user_role'] !== 'student') {
    header('Location: /studentlance/pages/dashboard-client.php');
    exit();
}
$pdo = Database::getInstance()->getConnection();
$userData = getUserData($_SESSION['user_id']);

// معالجة نشر طلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_request') {
    $stmt = $pdo->prepare("INSERT INTO projects (client_id, title, description, category, budget_min, budget_max, skills_required) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        $_SESSION['user_id'],
        sanitize($_POST['title']),
        sanitize($_POST['description']),
        sanitize($_POST['category']),
        floatval($_POST['budget_min']),
        floatval($_POST['budget_max']),
        sanitize($_POST['skills'])
    ]);
    $success = __('success_posted');
}

// جلب طلباته
$stmt = $pdo->prepare("SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$myRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('my_requests'); ?> - StudentLance</title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
    <style>
        .search-freelancers-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="dashboard-container">
    <h1><?php echo __('welcome_student'); ?>, <?php echo $userData['full_name']; ?>!</h1>
    <p>🔍 أنت هنا لأنك تبحث عن خدمة. انشر طلبك أو ابحث عن مستقل مباشرة.</p>
    <?php if (isset($success)): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <!-- قسم البحث عن المستقلين -->
    <div class="search-freelancers-box">
        <h3>ابحث عن مستقل</h3>
        <form action="/studentlance/pages/browse-freelancers.php" method="GET" class="search-filter" style="margin-bottom:0;">
            <input type="text" name="search" placeholder="اسم المستقل أو مهارة..." required>
            <button type="submit" class="btn btn-primary">بحث</button>
        </form>
        <p style="color: var(--text-secondary); margin-top: 0.8rem;">أو <a href="/studentlance/pages/browse-freelancers.php">تصفح كل المستقلين</a></p>
    </div>

    <!-- قسم نشر طلب -->
    <div class="dashboard-section">
        <h2><?php echo __('post_request'); ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="post_request">
            <div class="form-group"><label><?php echo __('request_title'); ?></label><input type="text" name="title" required></div>
            <div class="form-group"><label><?php echo __('request_description'); ?></label><textarea name="description" rows="5" required></textarea></div>
            <div class="form-group"><label><?php echo __('category'); ?></label><select name="category" required>
                <option value="">اختر التصنيف</option>
                <option value="Web Development">تطوير ويب</option>
                <option value="Mobile Apps">تطبيقات جوال</option>
                <option value="Graphic Design">تصميم جرافيك</option>
                <option value="Content Writing">كتابة محتوى</option>
            </select></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group"><label><?php echo __('min_budget'); ?></label><input type="number" name="budget_min" required></div>
                <div class="form-group"><label><?php echo __('max_budget'); ?></label><input type="number" name="budget_max" required></div>
            </div>
            <div class="form-group"><label><?php echo __('skills_required'); ?></label><input type="text" name="skills" placeholder="المهارات المطلوبة"></div>
            <button type="submit" class="btn btn-primary"><?php echo __('publish'); ?></button>
        </form>
    </div>

    <!-- طلباته السابقة -->
    <div class="dashboard-section" style="margin-top: 3rem;">
        <h2><?php echo __('my_requests'); ?></h2>
        <?php if (empty($myRequests)): ?>
            <p><?php echo __('no_requests'); ?></p>
        <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($myRequests as $r): ?>
                    <div class="project-card">
                        <h3><?php echo $r['title']; ?></h3>
                        <p><?php echo __('budget'); ?>: $<?php echo $r['budget_min']; ?> - $<?php echo $r['budget_max']; ?></p>
                        <span class="badge badge-<?php echo $r['status']; ?>"><?php echo __($r['status']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>