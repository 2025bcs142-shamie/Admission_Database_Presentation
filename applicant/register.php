<?php

require_once __DIR__ . '/../config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$formData = [
    'first_name' => '',
    'last_name' => '',
    'date_of_birth' => '',
    'gender' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'nationality' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'nationality' => trim($_POST['nationality'] ?? ''),
    ];

    $password = $_POST['password'] ?? '';
    $email = filter_var($formData['email'], FILTER_SANITIZE_EMAIL);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO Applicants (first_name, last_name, date_of_birth, gender, email, password_hash, phone, address, nationality)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $formData['first_name'],
            $formData['last_name'],
            $formData['date_of_birth'],
            $formData['gender'],
            $email,
            $passwordHash,
            $formData['phone'],
            $formData['address'],
            $formData['nationality'],
        ]);

        header('Location: login.php?registered=1');
        exit;
    } catch (PDOException $e) {
        if ((int) $e->getCode() === 23000) {
            $error = 'Email already registered';
        } else {
            $error = 'Registration failed';
        }
    }
}

require_once __DIR__ . '/../header.php';
?>
<h1>Applicant Registration</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="first_name">First Name</label>
    <input type="text" id="first_name" name="first_name" required value="<?= htmlspecialchars($formData['first_name']) ?>">

    <label for="last_name">Last Name</label>
    <input type="text" id="last_name" name="last_name" required value="<?= htmlspecialchars($formData['last_name']) ?>">

    <label for="date_of_birth">Date of Birth</label>
    <input type="date" id="date_of_birth" name="date_of_birth" required value="<?= htmlspecialchars($formData['date_of_birth']) ?>">

    <label for="gender">Gender</label>
    <select id="gender" name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male" <?= $formData['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $formData['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $formData['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
    </select>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($formData['email']) ?>">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="phone">Phone</label>
    <input type="text" id="phone" name="phone" required value="<?= htmlspecialchars($formData['phone']) ?>">

    <label for="address">Address</label>
    <textarea id="address" name="address"><?= htmlspecialchars($formData['address']) ?></textarea>

    <label for="nationality">Nationality</label>
    <input type="text" id="nationality" name="nationality" value="<?= htmlspecialchars($formData['nationality']) ?>">

    <button type="submit">Register</button>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>
