<?php
/**
 * Gamers Hub — process registration (CREATE)
 * Expects POST from profile.php. Validates, hashes password, inserts row, redirects with status.
 */

require __DIR__ . '/db.php';

// Only accept form posts (not direct GET visits)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$username = trim((string) ($_POST['username'] ?? ''));
$emailOrPhone = trim((string) ($_POST['email_or_phone'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

/**
 * Check password rules: min 6 chars, at least one letter and one number.
 * Returns null if OK, or an error code string if not.
 */
function passwordErrorCode(string $password): ?string
{
    if ($password === '') {
        return 'empty_password';
    }
    if (strlen($password) < 6) {
        return 'password_short';
    }
    if (!preg_match('/[A-Za-z]/', $password)) {
        return 'password_letter';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'password_number';
    }
    return null;
}

// --- Server-side validation ---
if ($username === '') {
    header('Location: profile.php?status=err&code=empty_username');
    exit;
}
if ($emailOrPhone === '') {
    header('Location: profile.php?status=err&code=empty_contact');
    exit;
}
$pwdErr = passwordErrorCode($password);
if ($pwdErr !== null) {
    header('Location: profile.php?status=err&code=' . urlencode($pwdErr));
    exit;
}

// Hash password for storage (never store plain text)
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Prepared statement prevents SQL injection
    $stmt = $pdo->prepare(
        'INSERT INTO users (username, email_or_phone, password) VALUES (:username, :email_or_phone, :password)'
    );
    $stmt->execute([
        ':username'       => $username,
        ':email_or_phone' => $emailOrPhone,
        ':password'       => $hash,
    ]);
} catch (PDOException $e) {
    // MySQL duplicate username → error code 23000
    if ((int) $e->errorInfo[1] === 1062) {
        header('Location: profile.php?status=err&code=duplicate_username');
        exit;
    }
    header('Location: profile.php?status=err&code=db_error');
    exit;
}

header('Location: profile.php?status=ok&code=registered');
exit;
