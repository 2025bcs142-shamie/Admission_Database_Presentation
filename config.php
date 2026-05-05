<?php

session_start();

define('BASE_URL', '/admission_system/');

$dsn = 'mysql:host=localhost;dbname=admissions_db;charset=utf8mb4';
$username = 'root';
$password = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $username, $password, $options);
