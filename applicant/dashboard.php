<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header('Location: login.php');
    exit;
}

$success = isset($_GET['success']) ? 'Application submitted successfully' : '';

$stmt = $pdo->prepare(
    'SELECT a.application_id, p.program_name, a.status, a.application_date, a.admin_remarks
     FROM Applications a
     JOIN Programs p ON a.program_id = p.program_id
     WHERE a.applicant_id = ?
     ORDER BY a.application_date DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

require_once __DIR__ . '/../header.php';
?>
<h1>Applicant Dashboard</h1>

<?php if ($success !== ''): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if (empty($applications)): ?>
    <p>You have not submitted any applications yet.</p>
    <p><a href="apply.php">Apply for a program</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Status</th>
                <th>Date</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?= htmlspecialchars($application['program_name']) ?></td>
                    <td><?= htmlspecialchars($application['status']) ?></td>
                    <td><?= htmlspecialchars($application['application_date']) ?></td>
                    <td><?= htmlspecialchars($application['admin_remarks'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>
