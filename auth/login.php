<?php
require_once __DIR__ . '/../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: /studentlance/pages/' . ($user['role'] === 'student' ? 'dashboard-student.php' : 'dashboard-client.php'));
        exit();
    } else {
        $error = __('error_invalid_credentials');
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('login_title'); ?> - StudentLance</title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="auth-container">
        <div class="auth-card">
            <h2><?php echo __('login_title'); ?></h2>
            <p><?php echo __('login_welcome'); ?></p>
            <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group"><label><?php echo __('email'); ?></label><input type="email" name="email" required></div>
                <div class="form-group"><label><?php echo __('password'); ?></label><input type="password" name="password" required></div>
                <button type="submit" class="btn btn-primary btn-block"><?php echo __('login_btn'); ?></button>
            </form>
            <div class="auth-footer"><p><?php echo __('no_account'); ?> <a href="/studentlance/auth/register.php"><?php echo __('create_account'); ?></a></p></div>
            <div style="margin-top:2rem;padding:1rem;background:var(--bg-card);border-radius:8px;">
                <h4 style="color:var(--accent);">حسابات تجريبية:</h4>
                <p style="font-size:0.9rem;">طالب: ahmed@example.com | 12345678</p>
                <p style="font-size:0.9rem;">عميل: mohamed@example.com | 12345678</p>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>