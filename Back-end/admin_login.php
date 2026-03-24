<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email']    ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nom'] = $admin['nom'];
        session_write_close();
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: admin_login.php?error=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Johnny Barber</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="page-login-body">

    <div class="page-login">

        <div class="login-deco">
            <div class="login-deco-line"></div>
            <p class="login-deco-text">Espace Privé</p>
            <div class="login-deco-line"></div>
        </div>

        <div class="login-card">

            <div class="login-logo">
                <p class="login-eyebrow">Johnny Barber</p>
                <h1>Administration</h1>
                <span class="login-separator"></span>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="login-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Identifiants incorrects.
                </div>
            <?php endif; ?>

            <form method="POST" action="admin_login.php" class="login-form">

                <div class="login-form-group">
                    <label for="email">Email</label>
                    <div class="login-input-wrapper">
                        <i class="fa-regular fa-envelope login-input-icon"></i>
                        <input type="email" id="email" name="email"
                               placeholder="adressemail@mail.com" required>
                    </div>
                </div>

                <div class="login-form-group">
                    <label for="password">Mot de passe</label>
                    <div class="login-input-wrapper">
                        <i class="fa-solid fa-lock login-input-icon"></i>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••" required>
                        <button type="button" class="login-toggle-pw" onclick="togglePassword()">
                            <i class="fa-regular fa-eye" id="toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Se connecter</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>

            </form>

            <a href="../index.html" class="back-link">
                <i class="fa-solid fa-chevron-left"></i> Retour au site
            </a>

        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>