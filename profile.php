<?php
/**
 * Gamers Hub — Player Profile
 * Assignment 1 style + PHP success/error messages
 */

$pageTitle = 'Profile | Gamers Hub';

// Map URL codes to user-friendly messages
$successMessages = [
    'registered' => 'Account created successfully. You can view all users on the Users page.',
];

$errorMessages = [
    'empty_username'     => 'Username cannot be empty.',
    'empty_contact'      => 'Email or phone number cannot be empty.',
    'empty_password'     => 'Password cannot be empty.',
    'password_short'     => 'Password must be at least 6 characters.',
    'password_letter'    => 'Password must contain at least one letter.',
    'password_number'    => 'Password must contain at least one number.',
    'duplicate_username' => 'That username is already taken. Please choose another.',
    'db_error'           => 'Something went wrong saving your account. Please try again.',
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

        .message {
            max-width: 540px;
            margin: 0 auto 20px;
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
        <h1>PLAYER PROFILE</h1>
        <p>Sign up or configure your Gamers Hub account below.</p>
    </section>

    <main class="main-content">
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

        <section class="profile-card">
            <h2>SIGN UP</h2>

            <form action="register.php" method="post" novalidate>
                <div class="field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="50"
                        placeholder="Enter your username"
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="field">
                    <label for="email_or_phone">Email or Phone Number</label>
                    <input
                        type="text"
                        id="email_or_phone"
                        name="email_or_phone"
                        maxlength="100"
                        placeholder="Email address or Phone"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Min 6 chars, 1 letter, 1 number"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn-submit">CREATE ACCOUNT</button>
            </form>
        </section>
    </main>

    <footer class="footer">
        © 2026 Gamers Hub • Web Design and Development
    </footer>

</body>
</html>