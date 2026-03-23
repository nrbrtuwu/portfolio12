<?php

session_start();
require 'api/adatbazis.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
        
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: admin.php");
        exit;
    } else {
        echo "Hibás felhasználónév vagy jelszó!";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        .container {
            min-width: 400px;
            max-width: 400px;
            width: 100%;
        }
        .login-container {
            padding: 20px;
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: inherit;
        }
        .form-control {
            margin-bottom: 15px;
            background-color: #fff;
            color: #222;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .dark-mode .form-control {
            background-color: #222;
            color: #fff;
            border-color: rgba(255,255,255,0.1);
        }
        .dark-mode .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }
        .dark-mode .error-message {
            color: #ff6b6b;
        }
        .theme-switch-wrapper {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .dark-mode .btn-primary {
            background-color: #0066cc;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .dark-mode .btn-primary:hover {
            background-color: #0052a3;
        }
        .form-control:focus {
            background-color: transparent;
            border-color: #007bff;
            color: inherit;
            box-shadow: none;
        }
        .dark-mode .form-control:focus {
            background-color: #222;
            border-color: #0066cc;
            color: #fff;
        }
    </style>
</head>
<body>
    <a href="index.php" style="position:fixed;top:20px;left:20px;z-index:102;">Vissza</a>
    <div class="theme-switch-wrapper">
        <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
            <div class="slider"></div>
        </label>
        <em>Sötét mód</em>
    </div>

    <div class="container">
        <div class="login-container">
            <h2 class="login-title">Bejelentkezés</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <input type="" class="form-control" name="username" placeholder="Felhasználónév" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Jelszó" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Bejelentkezés</button>
            </form>
        </div>
    </div>

    <script>
        const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
        const currentTheme = localStorage.getItem('theme');

        if (currentTheme) {
            document.documentElement.setAttribute('data-theme', currentTheme);
            if (currentTheme === 'dark') {
                toggleSwitch.checked = true;
                document.body.classList.add('dark-mode');
            }
        }

        function switchTheme(e) {
            if (e.target.checked) {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                document.body.classList.add('dark-mode');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                document.body.classList.remove('dark-mode');
            }
        }

        toggleSwitch.addEventListener('change', switchTheme, false);
    </script>
</body>
</html>