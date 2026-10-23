<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare('SELECT UserID, FullName, PasswordHash FROM users WHERE Email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['PasswordHash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['user_name'] = $user['FullName'];
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | ResearchEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <section class="auth-side">
        <img src="logo.png" alt="ResearchEase logo">
        <h1>ResearchEase</h1>
        <p>Manage and track your research projects in one place.</p>
    </section>

    <main class="auth-main">
        <div class="auth-card">
            <h2>Welcome back</h2>
            <p class="sub">Sign in to open your dashboard.</p>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-ok">Account created. You can sign in now.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Sign in</button>
            </form>

            <p class="switch">New here? <a href="register.php">Create an account</a></p>
        </div>
    </main>
</body>
</html>