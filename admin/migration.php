<?php
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
            
            // Remplacer tous les chemins d'images qui commencent par "../"
            $updated_content = preg_replace('/<img src="\.\.\/([^"]+)"/', '<img src="/$1"', $content);
            
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
