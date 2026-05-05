<?php

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$applicationId = isset($_POST['application_id']) ? (int) $_POST['application_id'] : 0;
$decision = $_POST['decision'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');

if ($applicationId <= 0 || !in_array($decision, ['Approved', 'Rejected'], true)) {
    header('Location: dashboard.php');
    exit;
}

if ($decision === 'Rejected' && $remarks === '') {
    header('Location: view_application.php?id=' . $applicationId . '&error=' . urlencode('Remarks are required when rejecting an application'));
    exit;
}

$stmt = $pdo->prepare(
    'UPDATE Applications SET status = ?, admin_remarks = ? WHERE application_id = ?'
);
$stmt->execute([$decision, $remarks, $applicationId]);

header('Location: dashboard.php?reviewed=1');
exit;
