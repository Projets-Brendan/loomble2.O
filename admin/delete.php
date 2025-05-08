<?php
// admin/delete.php
require_once 'includes/auth_check.php';

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = $_GET['file'];
    $articles_dir = '../articles/';
    $file_path = $articles_dir . $file;
    
    // Vérification de sécurité supplémentaire
    $real_path = realpath($file_path);
    $real_articles_dir = realpath($articles_dir);
    
    // Vérifier que le fichier existe, est dans le bon répertoire et est un fichier HTML
    if ($real_path && strpos($real_path, $real_articles_dir) === 0 && is_file($real_path) && pathinfo($real_path, PATHINFO_EXTENSION) === 'html') {
        // Supprimer le fichier
        if (unlink($real_path)) {
            // Redirection avec message de succès
            header('Location: dashboard.php?message=' . urlencode('Article supprimé avec succès'));
            exit;
        } else {
            // Erreur de suppression
            header('Location: dashboard.php?error=' . urlencode('Impossible de supprimer l\'article'));
            exit;
        }
    } else {
        // Fichier non trouvé ou non valide
        header('Location: dashboard.php?error=' . urlencode('Fichier non trouvé ou non valide'));
        exit;
    }
} else {
    // Pas de fichier spécifié
    header('Location: dashboard.php?error=' . urlencode('Aucun fichier spécifié'));
    exit;
}
?>
