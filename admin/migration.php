<?php
// admin/migration.php
require_once 'includes/auth_check.php';

// Chemin vers le répertoire des articles
$articles_dir = '../articles/';

// Vérifier si le répertoire existe
if (is_dir($articles_dir)) {
    // Lire tous les fichiers du répertoire
    $files = scandir($articles_dir);
    
    // Parcourir les fichiers
    foreach ($files as $file) {
        // S'assurer que c'est un fichier .html
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $file_path = $articles_dir . $file;
            
            // Lire le contenu du fichier HTML de l'article
            $content = file_get_contents($file_path);
            
            // Remplacer les différents formats de chemins d'images
            $patterns = [
                '/<img src="\.\.\/([^"]+)"/', // Chemin ../images/...
                '/<img src="\/([^"]+)"/'      // Chemin /images/...
            ];
            $replacements = [
                '<img src="/$1"',             // Remplacer par /images/...
                '<img src="/$1"'              // Laisser tel quel
            ];
            
            $updated_content = preg_replace($patterns, $replacements, $content);
            
            // Écrire le contenu mis à jour
            if ($content !== $updated_content) {
                file_put_contents($file_path, $updated_content);
                echo "Article mis à jour : $file<br>";
            } else {
                echo "Aucune modification nécessaire pour : $file<br>";
            }
        }
    }
    
    echo "Migration terminée.";
} else {
    echo "Le répertoire des articles n'existe pas.";
}
?>
