<?php
/**
 * Gamers Hub — database connection (PDO)
 * Adjust DB_USER and DB_PASS below to match your XAMPP MySQL settings.
 */

// --- MySQL connection settings (XAMPP defaults) ---
$DB_HOST = '127.0.0.1';
$DB_NAME = 'gamers_hub';
$DB_USER = 'root';
$DB_PASS = ''; // empty password is common on local XAMPP
$DB_CHARSET = 'utf8mb4';

// Data Source Name tells PDO how to connect
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

// PDO options: errors as exceptions, real prepared statements, sane fetch defaults
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Do not expose raw DB errors to visitors in production; log instead.
    // For a local assignment, a simple message is enough.
    die('Database connection failed. Check db.php settings and that MySQL is running.');
}
