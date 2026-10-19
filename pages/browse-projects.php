<?php
require_once __DIR__ . '/../includes/functions.php';
$pdo = Database::getInstance()->getConnection();
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sql = "SELECT p.*, u.full_name as client_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.status='open'";
$params = [];
if ($search) { $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($category) { $sql .= " AND p.category = ?"; $params[] = $category; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('available_projects'); ?> - StudentLance</title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="page-container">
        <h1><?php echo __('available_projects'); ?></h1>
        <form method="GET" class="search-filter">
            <input type="text" name="search" placeholder="<?php echo __('search_projects'); ?>" value="<?php echo $search; ?>">
            <select name="category">
                <option value=""><?php echo __('all_categories'); ?></option>
                <option value="Web Development" <?php if($category=='Web Development') echo 'selected'; ?>>تطوير ويب</option>
                <option value="Mobile Apps" <?php if($category=='Mobile Apps') echo 'selected'; ?>>تطبيقات جوال</option>
                <option value="Graphic Design" <?php if($category=='Graphic Design') echo 'selected'; ?>>تصميم جرافيك</option>
            </select>
            <button type="submit" class="btn btn-primary"><?php echo __('search'); ?></button>
        </form>
        <div class="projects-grid">
            <?php if (empty($projects)): ?><p><?php echo __('no_projects_available'); ?></p><?php else: ?>
                <?php foreach ($projects as $p): ?>
                <div class="project-card">
                    <h3><?php echo $p['title']; ?></h3>
                    <p><?php echo __('budget'); ?>: $<?php echo $p['budget_min']; ?> - $<?php echo $p['budget_max']; ?></p>
                    <a href="project-details.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm"><?php echo __('view_details'); ?></a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>