<?php
// admin/delete.php
require_once 'includes/auth_check.php';

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = $_GET['file'];
    $articles_dir = '../articles/';
    $file_path = $articles_dir . $file;
    
    // Vérifier si le fichier existe et est dans le répertoire des articles
    if (file_exists($file_path) && is_file($file_path) && pathinfo($file_path, PATHINFO_EXTENSION) === 'html') {
        // Supprimer le fichier
        if (unlink($file_path)) {
            // Redirection avec message de succès
            header('Location: dashboard.php?message=Article supprimé avec succès');
            exit;
        } else {
            // Erreur de suppression
            header('Location: dashboard.php?error=Impossible de supprimer l\'article');
            exit;
        }
    } else {
        // Fichier non trouvé ou non valide
        header('Location: dashboard.php?error=Fichier non trouvé ou non valide');
        exit;
    }
} else {
    // Pas de fichier spécifié
    header('Location: dashboard.php?error=Aucun fichier spécifié');
    exit;
}
?>
