<?php
// admin/includes/config.php - Configuration de base de l'espace admin
// Ce fichier devrait être protégé et ne pas être accessible publiquement

// admin/index.php - Page de connexion
session_start();

// Importer la configuration
require_once 'includes/config.php';
// Configuration des utilisateurs administrateurs
// Format: 'nom_utilisateur' => 'mot_de_passe_haché'
$admin_users = [
    'admin' => password_hash('votre_mot_de_passe_sécurisé', PASSWORD_DEFAULT)
    // Vous pouvez ajouter d'autres utilisateurs ici
];

// Configurations supplémentaires
$config = [
    'site_name' => 'Loomble',
    'admin_email' => 'lien@loomble.net',
    'articles_dir' => '../articles/',
    'images_dir' => '../images/articles/',
    'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp']
];

// Fonction pour générer un slug à partir d'un titre
function generateSlug($title) {
    // Translittération des caractères accentués
    $title = transliterator_transliterate('Any-Latin; Latin-ASCII', $title);
    // Convertir en minuscules
    $title = strtolower($title);
    // Remplacer les caractères spéciaux par des tirets
    $title = preg_replace('/[^a-z0-9\-]/', '-', $title);
    // Remplacer les tirets multiples par un seul
    $title = preg_replace('/-+/', '-', $title);
    // Supprimer les tirets en début et fin
    return trim($title, '-');
}

// Fonction pour générer un nom de fichier unique pour un article
function generateArticleFilename($title) {
    $slug = generateSlug($title);
    $date_prefix = date('Ymd');
    return $date_prefix . '-' . $slug . '.html';
}

// Fonction pour vérifier si un chemin est sécuritaire
function isPathSafe($path, $base_dir) {
    $real_path = realpath($path);
    $real_base = realpath($base_dir);
    
    if ($real_path === false || $real_base === false) {
        return false;
    }
    
    return strpos($real_path, $real_base) === 0;
}
?>
