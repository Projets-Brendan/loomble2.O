<?php
// admin/image-manager.php
require_once 'includes/auth_check.php';

// Répertoire des images
$images_dir = '../images/articles/';

// Créer le répertoire s'il n'existe pas
if (!is_dir($images_dir)) {
    mkdir($images_dir, 0755, true);
}

$message = '';
$error = '';

// Fonction pour valider les dimensions et la taille du fichier
function validateImage($file) {
    // Taille maximale (5 Mo)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return "L'image est trop volumineuse. La taille maximale est de 5 Mo.";
    }
    
    // Vérifier si c'est une image valide
    $image_info = getimagesize($file['tmp_name']);
    if (!$image_info) {
        return "Le fichier téléchargé n'est pas une image valide.";
    }
    
    // Vérifier les dimensions
    list($width, $height) = $image_info;
    if ($width > 2500 || $height > 2500) {
        return "Les dimensions de l'image sont trop grandes. La largeur et la hauteur maximales sont de 2500 pixels.";
    }
    
    return true;
}

// Traitement de l'upload d'image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    
    // Vérifier les erreurs
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_info = pathinfo($file['name']);
        $file_ext = strtolower($file_info['extension']);
        
        // Vérifier l'extension
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($file_ext, $allowed_extensions)) {
            // Générer un nom de fichier unique
            $new_filename = uniqid('img_') . '.' . $file_ext;
            $destination = $images_dir . $new_filename;
            
            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $message = "L'image a été téléchargée avec succès.";
            } else {
                $error = "Erreur lors du déplacement du fichier.";
            }
        } else {
            $error = "Extension de fichier non autorisée. Utilisez JPG, JPEG, PNG, GIF ou WEBP.";
        }
    } else {
        $error = "Erreur lors du téléchargement du fichier. Code: " . $file['error'];
    }
}

// Récupérer la liste des images
$images = [];
if (is_dir($images_dir)) {
    $files = scandir($images_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $file_path = $images_dir . $file;
            if (is_file($file_path)) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $images[] = [
                        'name' => $file,
                        'path' => str_replace('../', '', $file_path),
                        'size' => filesize($file_path),
                        'date' => date('Y-m-d', filemtime($file_path))
                    ];
                }
            }
        }
    }
    
    // Trier par date décroissante
    usort($images, function($a, $b) {
        return filemtime('../' . $b['path']) - filemtime('../' . $a['path']);
    });
}

// Déterminer si c'est une requête depuis l'éditeur
$is_popup = isset($_GET['popup']) && $_GET['popup'] === '1';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loomble - Gestionnaire d'images</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: <?php echo $is_popup ? '20px' : '100px'; ?> auto 40px;
            padding: 30px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .admin-header h1 {
            color: var(--primary);
            font-size: 1.8em;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .upload-form {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f8f8;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background-color: #003d1a;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .image-card {
            border: 1px solid #eee;
            border-radius: 5px;
            overflow: hidden;
            transition: 0.3s;
        }
        .image-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .image-preview {
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background: #f8f8f8;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-info {
            padding: 10px;
            font-size: 0.9em;
        }
        .image-name {
            font-weight: 500;
            margin-bottom: 5px;
            word-break: break-all;
        }
        .image-meta {
            color: #666;
            font-size: 0.9em;
        }
        .image-actions {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #f8f8f8;
            border-top: 1px solid #eee;
        }
        .image-actions a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9em;
        }
        .image-actions a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .copy-path {
            cursor: pointer;
            color: var(--primary);
        }
        .select-image {
            cursor: pointer;
            padding: 5px 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Gestionnaire d'images</h1>
            <?php if (!$is_popup): ?>
            <div>
                <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="upload-form">
            <h2>Télécharger une nouvelle image</h2>
            <form method="post" enctype="multipart/form-data" action="">
                <div class="form-group">
                    <label for="image">Sélectionner une image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Télécharger</button>
            </form>
        </div>
        
        <h2>Images disponibles</h2>
        
        <?php if (empty($images)): ?>
            <p>Aucune image n'a été téléchargée.</p>
        <?php else: ?>
            <div class="image-grid">
                <?php foreach ($images as $image): ?>
                <div class="image-card">
                    <div class="image-preview">
                        <img src="../<?php echo htmlspecialchars($image['path']); ?>" alt="<?php echo htmlspecialchars($image['name']); ?>">
                    </div>
                    <div class="image-info">
                        <div class="image-name"><?php echo htmlspecialchars($image['name']); ?></div>
                        <div class="image-meta">
                            <?php echo date('d/m/Y', strtotime($image['date'])); ?> · 
                            <?php echo round($image['size'] / 1024, 2); ?> KB
                        </div>
                    </div>
                    <div class="image-actions">
                        <a href="javascript:void(0);" class="copy-path" data-path="<?php echo htmlspecialchars($image['path']); ?>">Copier le chemin</a>
                        <?php if ($is_popup): ?>
                        <button class="select-image" data-path="<?php echo htmlspecialchars($image['path']); ?>">Sélectionner</button>
                        <?php else: ?>
                        <a href="delete-image.php?file=<?php echo urlencode(basename($image['path'])); ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Gestion de la copie du chemin d'image
        document.querySelectorAll('.copy-path').forEach(button => {
            button.addEventListener('click', function() {
                const path = this.getAttribute('data-path');
                navigator.clipboard.writeText(path).then(() => {
                    alert('Chemin copié dans le presse-papiers: ' + path);
                });
            });
        });
        
        <?php if ($is_popup): ?>
        // Sélection d'image pour l'éditeur
        document.querySelectorAll('.select-image').forEach(button => {
            button.addEventListener('click', function() {
                const path = this.getAttribute('data-path');
                
                // Standardiser le format du chemin d'image pour l'éditeur
                let standardPath = path;
                if (standardPath.startsWith('../')) {
                    standardPath = standardPath.substring(3);
                }
                
                window.opener.document.getElementById('featured_image').value = standardPath;
                
                // Déclencher l'événement change pour mettre à jour la prévisualisation
                const event = new Event('change');
                window.opener.document.getElementById('featured_image').dispatchEvent(event);
                window.close();
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
