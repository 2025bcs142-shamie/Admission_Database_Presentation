<?php

require_once __DIR__ . '/../config.php';

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM Admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['user_id'] = $admin['admin_id'];
        $_SESSION['role'] = 'admin';

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid credentials';
}

require_once __DIR__ . '/../header.php';
?>
<h1>Admin Login</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username) ?>">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
