<?php
// $base_path is not needed, we use BASE_URL for all assets.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admission</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>style.css">
</head>
<body>
    <nav>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>applicant/login.php">Applicant Login</a>
            <a href="<?= BASE_URL ?>applicant/register.php">Register</a>
            <a href="<?= BASE_URL ?>admin/login.php">Admin Login</a>
        <?php elseif ($_SESSION['role'] == 'applicant'): ?>
            <a href="<?= BASE_URL ?>applicant/dashboard.php">Dashboard</a>
            <a href="<?= BASE_URL ?>applicant/apply.php">New Application</a>
            <a href="<?= BASE_URL ?>applicant/logout.php">Logout</a>
        <?php elseif ($_SESSION['role'] == 'admin'): ?>
            <a href="<?= BASE_URL ?>admin/dashboard.php">Admin Panel</a>
            <a href="<?= BASE_URL ?>admin/logout.php">Logout</a>
        <?php endif; ?>
    </nav>
    <main>
