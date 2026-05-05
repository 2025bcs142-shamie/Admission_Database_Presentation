<?php

require_once __DIR__ . '/config.php';

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'applicant') {
    header('Location: ' . BASE_URL . 'applicant/dashboard.php');
    exit;
}

if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: ' . BASE_URL . 'admin/dashboard.php');
    exit;
}

$stmt = $pdo->query(
    'SELECT program_name, department, duration_years, available_slots
     FROM Programs
     ORDER BY program_name ASC'
);
$programs = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>
<h1>University Admission System</h1>
<p>Apply for available university programs online, upload your supporting documents, and track your admission status from one place.</p>

<p>
    <a href="<?= BASE_URL ?>applicant/register.php">Create Applicant Account</a> |
    <a href="<?= BASE_URL ?>applicant/login.php">Applicant Login</a> |
    <a href="<?= BASE_URL ?>admin/login.php">Admin Login</a>
</p>

<h2>How It Works</h2>
<ol>
    <li>Create an applicant account.</li>
    <li>Log in and submit your application.</li>
    <li>Upload supporting documents and qualifications.</li>
    <li>Track your application status from your dashboard.</li>
 </ol>

<h2>Available Programs</h2>
<?php if (empty($programs)): ?>
    <p>No programs are available at the moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Department</th>
                <th>Duration (Years)</th>
                <th>Available Slots</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($programs as $program): ?>
                <tr>
                    <td><?= htmlspecialchars($program['program_name']) ?></td>
                    <td><?= htmlspecialchars($program['department']) ?></td>
                    <td><?= htmlspecialchars($program['duration_years']) ?></td>
                    <td><?= htmlspecialchars($program['available_slots']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
