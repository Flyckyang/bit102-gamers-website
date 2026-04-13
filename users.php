<?php
/**
 * Gamers Hub — Users List
 * Assignment 1 style + PHP database read
 */

require __DIR__ . '/db.php';

$pageTitle = 'Users | Gamers Hub';

$successMessages = [
    'updated' => 'User updated successfully.',
    'deleted' => 'User deleted successfully.',
];

$errorMessages = [
    'invalid_id'    => 'Invalid user ID.',
    'not_found'     => 'User not found.',
    'db_error'      => 'Database error. Please try again.',
    'invalid_delete'=> 'Invalid delete request.',
];

$flashSuccess = null;
$flashError = null;

if (isset($_GET['status'], $_GET['code'])) {
    $code = (string) $_GET['code'];

    if ($_GET['status'] === 'ok' && isset($successMessages[$code])) {
        $flashSuccess = $successMessages[$code];
    } elseif ($_GET['status'] === 'err' && isset($errorMessages[$code])) {
        $flashError = $errorMessages[$code];
    }
}

try {
    $stmt = $pdo->query("SELECT id, username, email_or_phone, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $flashError = 'Could not load users from the database.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #222;
        }

        .navbar {
            background-color: #212529;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-size: 16px;
        }

        .navbar nav a:hover {
            text-decoration: underline;
        }

        .page-header {
            text-align: center;
            background: #f4f6f9;
            padding: 30px 20px 20px;
            border-bottom: 1px solid #ddd;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .page-header p {
            margin-top: 12px;
            font-size: 16px;
            color: #555;
        }

        .main-content {
            padding: 40px 20px 60px;
            min-height: 500px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .top-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #000;
            text-decoration: none;
            font-weight: bold;
        }

        .top-link:hover {
            text-decoration: underline;
        }

        .message {
            margin: 0 0 20px;
            padding: 14px 18px;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .msg-success {
            background: #eafaf1;
            border: 1px solid #28a745;
            color: #1e7e34;
        }

        .msg-error {
            background: #fff0f0;
            border: 1px solid #dc3545;
            color: #b02a37;
        }

        .table-card {
            background: #fff;
            border: 2px solid #000;
            padding: 25px;
            box-sizing: border-box;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #000;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .actions a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .edit-link {
            color: #0d6efd;
        }

        .delete-link {
            color: #dc3545;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .empty-box {
            background: #fff;
            border: 2px solid #000;
            padding: 25px;
            font-size: 16px;
        }

        .empty-box a {
            color: #000;
            font-weight: bold;
        }

        .empty-box a:hover {
            text-decoration: underline;
        }

        .footer {
            background-color: #212529;
            color: white;
            text-align: center;
            padding: 18px;
            font-size: 14px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="index.html" class="logo">Gamers Hub</a>
        <nav>
            <a href="index.html">Home</a>
            <a href="forum.html">Forum</a>
            <a href="resources.html">Resources</a>
            <a href="profile.php">Profile</a>
            <a href="users.php">Users</a>
        </nav>
    </header>

    <section class="page-header">
        <h1>REGISTERED USERS</h1>
        <p>All registered accounts stored in the database are shown below.</p>
    </section>

    <main class="main-content">
        <a href="profile.php" class="top-link">← Register a new user</a>

        <?php if ($flashSuccess !== null): ?>
            <div class="message msg-success" role="alert">
                <?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError !== null): ?>
            <div class="message msg-error" role="alert">
                <?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($users)): ?>
            <div class="empty-box">
                No users yet.
                <a href="profile.php">Create the first account.</a>
            </div>
        <?php else: ?>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email / Phone</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo (int) $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user['email_or_phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="actions">
                                    <a class="edit-link" href="edit_user.php?id=<?php echo (int) $user['id']; ?>">Edit</a>
                                    <a class="delete-link" href="delete_user.php?id=<?php echo (int) $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        © 2026 Gamers Hub • Web Design and Development
    </footer>

</body>
</html>