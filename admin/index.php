<?php

// admin/index.php - Page de connexion
session_start();

// Importer la configuration
require_once 'config.php';

$error = '';

// Rediriger si déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Vérification de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($admin_users[$username]) && password_verify($password, $admin_users[$username])) {
        // Connexion réussie
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Ajouter la date de connexion
        $_SESSION['admin_login_time'] = time();
        
        // Redirection vers le tableau de bord
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['site_name']; ?> Admin - Connexion</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .login-form h2 {
            color: var(--primary);
            margin-bottom: 20px;
            text-align: center;
        }
        .error-message {
            color: #e74c3c;
            margin-bottom: 15px;
            text-align: center;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: bold;
            color: var(--primary);
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo"><?php echo $config['site_name']; ?> Admin</div>
        <form class="login-form" method="post" action="">
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="inputBox">
                <input type="text" name="username" required>
                <span>Nom d'utilisateur</span>
            </div>
            
            <div class="inputBox">
                <input type="password" name="password" required>
                <span>Mot de passe</span>
            </div>
            
            <div class="inputBox">
                <input type="submit" value="Se connecter">
            </div>
            
            <div class="login-footer">
                Panneau d'administration <?php echo $config['site_name']; ?> &copy; <?php echo date('Y'); ?>
            </div>
        </form>
    </div>
</body>
</html>
