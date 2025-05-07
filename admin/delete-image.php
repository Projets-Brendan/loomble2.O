<?php
// admin/delete-image.php
require_once 'includes/auth_check.php';
// Vérification de la sécurité supplémentaire dans delete.php
$file_path = $articles_dir . $file;
$real_path = realpath($file_path);
$real_articles_dir = realpath($articles_dir);

// Vérifier que le fichier existe, est dans le bon répertoire et est un fichier HTML
if ($real_path && strpos($real_path, $real_articles_dir) === 0 && is_file($real_path) && pathinfo($real_path, PATHINFO_EXTENSION) === 'html') {
    // ... continuer avec la suppression
}


if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file = $_GET['file'];
    $images_dir = '../images/articles/';
    $file_path = $images_dir . $file;
    
    // Vérification de sécurité supplémentaire
    $real_path = realpath($file_path);
    $real_images_dir = realpath($images_dir);
    
    // Vérifier que le fichier existe et qu'il est dans le répertoire des images
    if ($real_path && strpos($real_path, $real_images_dir) === 0 && is_file($real_path)) {
        // Vérifier si c'est bien une image
        $file_info = pathinfo($real_path);
        $file_ext = strtolower($file_info['extension']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            // Supprimer le fichier
            if (unlink($real_path)) {
                // Redirection avec message de succès
                header('Location: image-manager.php?message=Image supprimée avec succès');
                exit;
            } else {
                // Erreur de suppression
                header('Location: image-manager.php?error=Impossible de supprimer l\'image');
                exit;
            }
        } else {
            // Ce n'est pas une image
            header('Location: image-manager.php?error=Le fichier n\'est pas une image valide');
            exit;
        }
    } else {
        // Fichier non trouvé ou non valide
        header('Location: image-manager.php?error=Fichier non trouvé ou non valide');
        exit;
    }
} else {
    // Pas de fichier spécifié
    header('Location: image-manager.php?error=Aucun fichier spécifié');
    exit;
}
?>
