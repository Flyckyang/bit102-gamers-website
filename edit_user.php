<?php
/**
 * Gamers Hub — Edit User
 * Light Assignment 1 style + PHP update logic
 */

require __DIR__ . '/db.php';

$pageTitle = 'Edit User | Gamers Hub';

$errorMessages = [
    'invalid_id'         => 'Invalid user ID.',
    'not_found'          => 'User not found.',
    'empty_username'     => 'Username cannot be empty.',
    'empty_contact'      => 'Email or phone number cannot be empty.',
    'password_short'     => 'Password must be at least 6 characters.',
    'password_letter'    => 'Password must contain at least one letter.',
    'password_number'    => 'Password must contain at least one number.',
    'duplicate_username' => 'That username is already taken. Please choose another.',
    'db_error'           => 'Something went wrong updating the account. Please try again.',
];

$flashError = null;

$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} elseif (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
}

if ($id <= 0) {
    $flashError = $errorMessages['invalid_id'];
}

$user = null;

if ($flashError === null) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email_or_phone FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $flashError = $errorMessages['not_found'];
        }
    } catch (PDOException $e) {
        $flashError = $errorMessages['db_error'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $flashError === null) {
    $username = trim($_POST['username'] ?? '');
    $emailOrPhone = trim($_POST['email_or_phone'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    if ($username === '') {
        $flashError = $errorMessages['empty_username'];
    } elseif ($emailOrPhone === '') {
        $flashError = $errorMessages['empty_contact'];
    } elseif ($newPassword !== '' && strlen($newPassword) < 6) {
        $flashError = $errorMessages['password_short'];
    } elseif ($newPassword !== '' && !preg_match('/[A-Za-z]/', $newPassword)) {
        $flashError = $errorMessages['password_letter'];
    } elseif ($newPassword !== '' && !preg_match('/[0-9]/', $newPassword)) {
        $flashError = $errorMessages['password_number'];
    }

    if ($flashError === null) {
        try {
            $dupStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id <> :id LIMIT 1");
            $dupStmt->execute([
                'username' => $username,
                'id' => $id,
            ]);

            if ($dupStmt->fetch()) {
                $flashError = $errorMessages['duplicate_username'];
            }
        } catch (PDOException $e) {
            $flashError = $errorMessages['db_error'];
        }
    }

    if ($flashError === null) {
        try {
            if ($newPassword !== '') {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = $pdo->prepare("
                    UPDATE users
                    SET username = :username,
                        email_or_phone = :email_or_phone,
                        password = :password
                    WHERE id = :id
                ");

                $updateStmt->execute([
                    'username' => $username,
                    'email_or_phone' => $emailOrPhone,
                    'password' => $hashedPassword,
                    'id' => $id,
                ]);
            } else {
                $updateStmt = $pdo->prepare("
                    UPDATE users
                    SET username = :username,
                        email_or_phone = :email_or_phone
                    WHERE id = :id
                ");

                $updateStmt->execute([
                    'username' => $username,
                    'email_or_phone' => $emailOrPhone,
                    'id' => $id,
                ]);
            }

            header('Location: users.php?status=ok&code=updated');
            exit;
        } catch (PDOException $e) {
            $flashError = $errorMessages['db_error'];
        }
    }

    $user['username'] = $username;
    $user['email_or_phone'] = $emailOrPhone;
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
        }

        .top-link {
            display: block;
            max-width: 540px;
            margin: 0 auto 20px;
            color: #000;
            text-decoration: none;
            font-weight: bold;
        }

        .top-link:hover {
            text-decoration: underline;
        }

        .message {
            max-width: 540px;
            margin: 0 auto 20px;
            padding: 14px 18px;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .msg-error {
            background: #fff0f0;
            border: 1px solid #dc3545;
            color: #b02a37;
        }

        .profile-card {
            width: 100%;
            max-width: 540px;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
            padding: 40px 50px 50px;
            box-sizing: border-box;
        }

        .profile-card h2 {
            margin: 0 0 35px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
        }

        .field {
            margin-bottom: 25px;
        }

        .field label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 15px;
            color: #111;
        }

        .field input {
            width: 100%;
            padding: 14px;
            border: 2px solid #000;
            font-size: 15px;
            box-sizing: border-box;
            background: #fff;
            color: #111;
        }

        .hint {
            margin-top: 8px;
            font-size: 14px;
            color: #666;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #000;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
        }

        .btn-submit:hover {
            opacity: 0.9;
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
        <h1>EDIT USER</h1>
        <p>Update the selected account details below.</p>
    </section>

    <main class="main-content">
        <a href="users.php" class="top-link">← Back to users list</a>

        <?php if ($flashError !== null): ?>
            <div class="message msg-error" role="alert">
                <?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($user): ?>
            <section class="profile-card">
                <h2>EDIT USER</h2>

                <form action="edit_user.php" method="post" novalidate>
                    <input type="hidden" name="id" value="<?php echo (int) $user['id']; ?>">

                    <div class="field">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            maxlength="50"
                            value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="email_or_phone">Email or Phone Number</label>
                        <input
                            type="text"
                            id="email_or_phone"
                            name="email_or_phone"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($user['email_or_phone'], ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="password">New Password (optional)</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Leave blank to keep current password"
                        >
                        <p class="hint">Only fill this in if you want to change the password. New password must still contain at least 6 characters, 1 letter, and 1 number.</p>
                    </div>

                    <button type="submit" class="btn-submit">SAVE CHANGES</button>
                </form>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        © 2026 Gamers Hub • Web Design and Development
    </footer>

</body>
</html>