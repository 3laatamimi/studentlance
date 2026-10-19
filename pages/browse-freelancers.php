<?php
require_once __DIR__ . '/../includes/functions.php';
$pdo = Database::getInstance()->getConnection();
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE role='student'";
$params = [];
if ($search) { $sql .= " AND (full_name LIKE ? OR skills LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$freelancers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('freelancers_title'); ?> - StudentLance</title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="page-container">
        <h1><?php echo __('freelancers_title'); ?></h1>
        <form method="GET" class="search-filter">
            <input type="text" name="search" placeholder="<?php echo __('search_freelancers'); ?>" value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-primary"><?php echo __('search'); ?></button>
        </form>
        <div class="freelancers-grid">
            <?php if (empty($freelancers)): ?><p><?php echo __('no_freelancers'); ?></p><?php else: ?>
                <?php foreach ($freelancers as $f): ?>
                <div class="freelancer-card">
                    <h3><?php echo $f['full_name']; ?></h3>
                    <p><?php echo $f['bio']; ?></p>
                    <div class="skills"><?php foreach (explode(',', $f['skills']) as $s): ?><span class="skill-tag"><?php echo trim($s); ?></span><?php endforeach; ?></div>
                    <div>⭐ <?php echo $f['rating']; ?>/5</div>
                    <div><?php echo __('hourly_rate'); ?>: $<?php echo $f['hourly_rate']; ?></div>
                    <a href="profile.php?id=<?php echo $f['id']; ?>" class="btn btn-outline btn-sm"><?php echo __('profile'); ?></a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>