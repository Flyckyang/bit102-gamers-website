<?php
/**
 * Gamers Hub — delete user (DELETE)
 * Expects ?id= numeric. Uses prepared statement, then redirects to users list.
 */

require __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id < 1) {
    header('Location: users.php?status=err&code=invalid_delete');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    // rowCount() can be 0 if id did not exist — still treat as "gone" for simple UX
    header('Location: users.php?status=ok&code=deleted');
    exit;
} catch (PDOException $e) {
    header('Location: users.php?status=err&code=delete_failed');
    exit;
}
