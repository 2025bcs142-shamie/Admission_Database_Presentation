<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$success = isset($_GET['reviewed']) ? 'Application reviewed' : '';

$stmt = $pdo->query(
    'SELECT a.application_id, ap.first_name, ap.last_name, p.program_name,
            a.status, a.application_date
     FROM Applications a
     JOIN Applicants ap ON a.applicant_id = ap.applicant_id
     JOIN Programs p ON a.program_id = p.program_id
     ORDER BY a.application_date DESC'
);
$applications = $stmt->fetchAll();

require_once __DIR__ . '/../header.php';
?>
<h1>Admin Dashboard</h1>

<?php if ($success !== ''): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Applicant Name</th>
            <th>Program</th>
            <th>Status</th>
            <th>Date</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?= htmlspecialchars($application['first_name'] . ' ' . $application['last_name']) ?></td>
                <td><?= htmlspecialchars($application['program_name']) ?></td>
                <td><?= htmlspecialchars($application['status']) ?></td>
                <td><?= htmlspecialchars($application['application_date']) ?></td>
                <td>
                    <a href="view_application.php?id=<?= htmlspecialchars($application['application_id']) ?>">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../footer.php'; ?>
