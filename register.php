<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Enter your full name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $check = $db->prepare('SELECT UserID FROM users WHERE Email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'That email is already registered. Try signing in.';
        }
    }

    if (!$errors) {
        $insert = $db->prepare('INSERT INTO users (FullName, Email, PasswordHash) VALUES (?, ?, ?)');
        $insert->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | ResearchEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <section class="auth-side">
        <img src="logo.png" alt="ResearchEase logo">
        <h1>ResearchEase</h1>
        <p>Create an account to start organizing your research projects.</p>
    </section>

    <main class="auth-main">
        <div class="auth-card">
            <h2>Create account</h2>
            <p class="sub">It only takes a minute.</p>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e) { echo htmlspecialchars($e) . '<br>'; } ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($name); ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" required>

                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>

                <button type="submit">Create account</button>
            </form>

            <p class="switch">Already have an account? <a href="login.php">Sign in</a></p>
        </div>
    </main>
</body>
</html>