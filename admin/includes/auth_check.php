<?php
// admin/includes/auth_check.php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirection vers la page de connexion
    header('Location: index.php');
    exit;
}
