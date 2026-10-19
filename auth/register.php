<?php
require_once __DIR__ . '/../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize($_POST['role']); // 'student' = طالب يبحث عن خدمة , 'client' = عميل يقدم خدمة
    $full_name = sanitize($_POST['full_name']);
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = __('error_fields_required');
    } elseif ($password !== $confirm_password) {
        $error = __('error_password_mismatch');
    } elseif (strlen($password) < 8) {
        $error = __('error_password_length');
    } else {
        $pdo = Database::getInstance()->getConnection();
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->execute([$email, $username]);
        if ($check->fetch()) {
            $error = __('error_username_exists');
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?,?,?,?,?)");
            $stmt->execute([$username, $email, $hashed, $role, $full_name]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_role'] = $role;
            // التوجيه للوحة التحكم الصحيحة حسب الدور الجديد
            if ($role === 'student') {
                header('Location: /studentlance/pages/dashboard-student.php');
            } else {
                header('Location: /studentlance/pages/dashboard-client.php');
            }
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo getLanguage(); ?>" dir="<?php echo (getLanguage()=='ar')?'rtl':'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('register_title'); ?> - StudentLance</title>
    <link rel="stylesheet" href="/studentlance/assets/style.css">
    <style>
        .role-selection { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1.5rem 0; }
        .role-card { background: var(--bg-card); border: 2px solid var(--border); border-radius: 16px; padding: 2rem 1.5rem; cursor: pointer; transition: all 0.3s ease; text-align: center; position: relative; }
        .role-card:hover { border-color: var(--accent); transform: translateY(-3px); }
        .role-card input[type="radio"] { position: absolute; opacity: 0; }
        .role-card.selected { border-color: var(--accent); background: rgba(255,92,0,0.05); }
        .role-icon { font-size: 3rem; margin-bottom: 1rem; }
        .role-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 0.8rem; }
        .role-desc { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; }
        @media (max-width: 768px) { .role-selection { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 600px;">
            <h2><?php echo __('register_title'); ?></h2>
            <p><?php echo __('register_subtitle'); ?></p>
            <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group"><label><?php echo __('full_name'); ?></label><input type="text" name="full_name" required></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group"><label><?php echo __('username'); ?></label><input type="text" name="username" required></div>
                    <div class="form-group"><label><?php echo __('email'); ?></label><input type="email" name="email" required></div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group"><label><?php echo __('password'); ?></label><input type="password" name="password" required minlength="8"></div>
                    <div class="form-group"><label><?php echo __('confirm_password'); ?></label><input type="password" name="confirm_password" required></div>
                </div>
                <div class="form-group">
                    <label><?php echo __('role_select'); ?></label>
                    <div class="role-selection">
                        <label class="role-card selected" id="card_student">
                            <input type="radio" name="role" value="student" id="role_student" checked>
                            <div class="role-icon">🔍</div>
                            <div class="role-title"><?php echo __('role_student_title'); ?></div>
                            <div class="role-desc"><?php echo __('role_student_desc'); ?></div>
                        </label>
                        <label class="role-card" id="card_client">
                            <input type="radio" name="role" value="client" id="role_client">
                            <div class="role-icon">💼</div>
                            <div class="role-title"><?php echo __('role_client_title'); ?></div>
                            <div class="role-desc"><?php echo __('role_client_desc'); ?></div>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block"><?php echo __('register_btn'); ?></button>
            </form>
            <div class="auth-footer"><p><?php echo __('already_have_account'); ?> <a href="/studentlance/auth/login.php"><?php echo __('login'); ?></a></p></div>
        </div>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>