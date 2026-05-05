<?php

// Warning: delete this file after use.

require_once __DIR__ . '/config.php';

$stmt = $pdo->prepare('SELECT admin_id FROM Admins WHERE username = ? LIMIT 1');
$stmt->execute(['admin']);
$admin = $stmt->fetch();

if ($admin) {
    echo 'Admin already exists';
    return;
}

$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO Admins (username, password_hash, full_name) VALUES (?, ?, ?)'
);
$stmt->execute(['admin', $passwordHash, 'Administrator']);

echo 'Admin created successfully';
