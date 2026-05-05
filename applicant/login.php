<?php

require_once __DIR__ . '/../config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = isset($_GET['registered']) ? 'Registration successful' : '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

    $stmt = $pdo->prepare('SELECT * FROM Applicants WHERE email = ? LIMIT 1');
    $stmt->execute([$sanitizedEmail]);
    $applicant = $stmt->fetch();

    if ($applicant && password_verify($password, $applicant['password_hash'])) {
        $_SESSION['user_id'] = $applicant['applicant_id'];
        $_SESSION['role'] = 'applicant';

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid credentials';
}

require_once __DIR__ . '/../header.php';
?>
<h1>Applicant Login</h1>

<?php if ($success !== ''): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>
