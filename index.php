<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = Database::getInstance()->getConnection();

// ------------------- للمستخدمين المسجلين -------------------
if (isLoggedIn()) {
    $user = getUserData($_SESSION['user_id']);
    $role = $_SESSION['user_role'];
    
    // بيانات خاصة بالطالب (يبحث عن خدمة)
    if ($role === 'student') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE client_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $myRequestsCount = $stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$_SESSION['user_id']]);
        $latestRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // أحدث المستقلين المقترحين
        $freelancers = $pdo->query("SELECT * FROM users WHERE role = 'client' ORDER BY rating DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
    }
    // بيانات خاصة بالمستقل (يقدم خدمة)
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bids WHERE student_id = ? AND status = 'pending'");
        $stmt->execute([$_SESSION['user_id']]);
        $activeBids = $stmt->fetchColumn();
        
        // أحدث المشاريع المفتوحة
        $openProjects = $pdo->query("SELECT p.*, u.full_name as client_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.status='open' ORDER BY p.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title>StudentLance - <?php echo __('site_title'); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <?php if ($role === 'student'): // طالب يبحث عن خدمة ?>
        <section class="hero" style="padding: 3rem 2rem;">
            <h1>🔍 مرحباً <?php echo $user['full_name']; ?>، ما الخدمة التي تحتاجها اليوم؟</h1>
            <p>انشر طلبك وسيأتيك المستقلون المناسبون بعروضهم.</p>
            <div class="hero-buttons">
                <a href="pages/dashboard-student.php" class="btn btn-primary">📝 نشر طلب جديد</a>
                <a href="pages/browse-freelancers.php" class="btn btn-outline">👥 تصفح المستقلين</a>
            </div>
        </section>
        
        <div class="stats">
            <div class="stat-card"><div class="stat-number"><?php echo $myRequestsCount; ?></div><div class="stat-label">طلباتي</div></div>
            <div class="stat-card"><a href="pages/browse-projects.php" class="btn btn-outline btn-sm">تصفح كل الطلبات</a></div>
        </div>
        
        <div class="dashboard-section" style="max-width:1400px; margin:2rem auto; padding:0 2rem;">
            <h2>📋 أحدث طلباتي</h2>
            <?php if (empty($latestRequests)): ?>
                <p>لا توجد طلبات بعد. <a href="pages/dashboard-student.php">انشر طلبك الأول</a></p>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($latestRequests as $r): ?>
                        <div class="project-card">
                            <h3><?php echo $r['title']; ?></h3>
                            <p>$<?php echo $r['budget_min']; ?> - $<?php echo $r['budget_max']; ?></p>
                            <span class="badge badge-<?php echo $r['status']; ?>"><?php echo __($r['status']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <section class="featured-projects" style="max-width:1400px; margin:2rem auto; padding:0 2rem;">
            <h2>🌟 مستقلون متميزون</h2>
            <div class="projects-grid">
                <?php foreach ($freelancers as $f): ?>
                    <div class="freelancer-card">
                        <h3><?php echo $f['full_name']; ?></h3>
                        <p><?php echo $f['skills']; ?></p>
                        <div>⭐ <?php echo $f['rating']; ?>/5 | $<?php echo $f['hourly_rate']; ?>/ساعة</div>
                        <a href="pages/profile.php?id=<?php echo $f['id']; ?>" class="btn btn-outline btn-sm">عرض الملف</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
    <?php else: // مستقل يقدم خدمة ?>
        <section class="hero" style="padding: 3rem 2rem;">
            <h1>💼 مرحباً <?php echo $user['full_name']; ?>، مستعد لاستلام مشاريع جديدة؟</h1>
            <p>تصفح الطلبات المتاحة وقدّم عروضك لتربح.</p>
            <div class="hero-buttons">
                <a href="pages/browse-projects.php" class="btn btn-primary">📋 تصفح الطلبات</a>
                <a href="pages/dashboard-client.php" class="btn btn-outline">📊 لوحة أرباحي</a>
            </div>
        </section>
        
        <div class="stats">
            <div class="stat-card"><div class="stat-number">$<?php echo number_format($user['total_earnings'],2); ?></div><div class="stat-label">أرباحك</div></div>
            <div class="stat-card"><div class="stat-number"><?php echo $activeBids; ?></div><div class="stat-label">عروض نشطة</div></div>
            <div class="stat-card"><div class="stat-number">⭐ <?php echo number_format($user['rating'],1); ?></div><div class="stat-label">تقييمك</div></div>
        </div>
        
        <section class="featured-projects" style="max-width:1400px; margin:2rem auto; padding:0 2rem;">
            <h2>📌 أحدث الطلبات المفتوحة</h2>
            <div class="projects-grid">
                <?php foreach ($openProjects as $p): ?>
                    <div class="project-card">
                        <h3><?php echo $p['title']; ?></h3>
                        <p><?php echo mb_substr($p['description'],0,80); ?>...</p>
                        <div class="project-budget">$<?php echo $p['budget_min']; ?> - $<?php echo $p['budget_max']; ?></div>
                        <a href="pages/project-details.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm">تقديم عرض</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

<?php
// ------------------- للزوار غير المسجلين -------------------
} else {
    // الإحصائيات العامة والمحتوى التعريفي (نفس السابق مع تحسينات بسيطة)
    $totalRequests = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='open'")->fetchColumn();
    $totalCompleted = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='completed'")->fetchColumn();
    $totalEarnings = $pdo->query("SELECT COALESCE(SUM(total_earnings),0) FROM users WHERE role='client'")->fetchColumn();
    $avgRating = $pdo->query("SELECT COALESCE(AVG(rating),0) FROM users WHERE role='client'")->fetchColumn();
    $stmt = $pdo->query("SELECT p.*, u.full_name as client_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.status='open' ORDER BY p.created_at DESC LIMIT 3");
    $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title>StudentLance - <?php echo __('site_title'); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <section class="hero">
        <h1><?php echo __('hero_title'); ?></h1>
        <p><?php echo __('hero_text'); ?></p>
        <div class="hero-buttons">
            <a href="auth/register.php?role=client" class="btn btn-primary"><?php echo __('start_earning'); ?></a>
            <a href="auth/register.php?role=student" class="btn btn-outline"><?php echo __('hire_talent'); ?></a>
        </div>
    </section>
    
    <div class="stats">
        <div class="stat-card"><div class="stat-number">+<?php echo $totalRequests; ?></div><div class="stat-label"><?php echo __('active_students'); ?></div></div>
        <div class="stat-card"><div class="stat-number">+<?php echo $totalCompleted; ?></div><div class="stat-label"><?php echo __('projects_completed'); ?></div></div>
        <div class="stat-card"><div class="stat-number">$<?php echo number_format($totalEarnings,2); ?></div><div class="stat-label"><?php echo __('student_earnings'); ?></div></div>
        <div class="stat-card"><div class="stat-number"><?php echo number_format($avgRating,1); ?>/5</div><div class="stat-label"><?php echo __('avg_rating'); ?></div></div>
    </div>
    
    <section class="how-it-works">
        <h2 class="section-title"><?php echo __('how_it_works'); ?></h2>
        <p class="section-subtitle"><?php echo __('how_subtitle'); ?></p>
        <div class="steps-grid">
            <div class="step-card"><div class="step-number">1</div><h3><?php echo __('step1_title'); ?></h3><p><?php echo __('step1_text'); ?></p></div>
            <div class="step-card"><div class="step-number">2</div><h3><?php echo __('step2_title'); ?></h3><p><?php echo __('step2_text'); ?></p></div>
            <div class="step-card"><div class="step-number">3</div><h3><?php echo __('step3_title'); ?></h3><p><?php echo __('step3_text'); ?></p></div>
        </div>
    </section>
    
    <section class="categories">
        <h2 class="section-title"><?php echo __('popular_categories'); ?></h2>
        <div class="categories-grid">
            <a href="pages/browse-projects.php?category=Web+Development" class="category-card"><div class="category-icon">💻</div><h4>تطوير ويب</h4></a>
            <a href="pages/browse-projects.php?category=Mobile+Apps" class="category-card"><div class="category-icon">📱</div><h4>تطبيقات جوال</h4></a>
            <a href="pages/browse-projects.php?category=Graphic+Design" class="category-card"><div class="category-icon">🎨</div><h4>تصميم جرافيك</h4></a>
            <a href="pages/browse-projects.php?category=Content+Writing" class="category-card"><div class="category-icon">✍️</div><h4>كتابة محتوى</h4></a>
        </div>
    </section>
    
    <section class="featured-projects">
        <h2 class="section-title"><?php echo __('featured_projects'); ?></h2>
        <div class="projects-grid">
            <?php foreach ($featured as $p): ?>
                <div class="project-card">
                    <h3><?php echo $p['title']; ?></h3>
                    <p><?php echo mb_substr($p['description'],0,100); ?>...</p>
                    <div class="project-budget">$<?php echo $p['budget_min']; ?> - $<?php echo $p['budget_max']; ?></div>
                    <a href="pages/project-details.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm"><?php echo __('view_details'); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
<?php } ?>