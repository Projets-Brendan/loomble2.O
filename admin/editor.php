<?php
// admin/editor.php
require_once 'includes/auth_check.php';
require_once 'includes/config.php';


// Initialisation des variables
$title = '';
$content = '';
$featured_image = '';
$meta_description = '';
$slug = '';
$edit_mode = false;
$file_to_edit = '';
$message = '';
$error = '';
$tags = '';

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

// Vérifier si on est en mode édition
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $file_to_edit = $_GET['edit'];
    $articles_dir = $config['articles_dir'];
    $file_path = $articles_dir . $file_to_edit;
    
    if (file_exists($file_path)) {
        $edit_mode = true;
        $html_content = file_get_contents($file_path);
        
        // Extraire les informations de l'article
        preg_match('/<h1 class="article-title">(.*?)<\/h1>/', $html_content, $title_matches);
        $title = isset($title_matches[1]) ? $title_matches[1] : '';
        
        preg_match('/<div class="article-featured-image">.*?<img src="(.*?)"/', $html_content, $image_matches);
        $featured_image = isset($image_matches[1]) ? $image_matches[1] : '';
        
        // Extraire la méta description
        preg_match('/<meta name="description" content="(.*?)">/', $html_content, $meta_matches);
        $meta_description = isset($meta_matches[1]) ? $meta_matches[1] : '';
        
        // Extraire les tags si présents
        preg_match('/<div class="article-tags">(.*?)<a href="\.\.\/index\.html#posts" class="tag">Retour aux articles<\/a>/', $html_content, $tags_matches);
        if (isset($tags_matches[1])) {
            // Extraire tous les tags
            preg_match_all('/<a href="#" class="tag">(.*?)<\/a>/', $tags_matches[1], $tag_items);
            if (isset($tag_items[1]) && !empty($tag_items[1])) {
                $tags = implode(', ', $tag_items[1]);
            }
        }
        
        // Extraire le contenu principal de l'article
        preg_match('/<div class="article-content">(.*?)<\/div>\s*<div class="article-tags">/', $html_content, $content_matches, PREG_OFFSET_CAPTURE);
        if (isset($content_matches[1])) {
            $content = $content_matches[1][0];
        } else {
            // Fallback si la regex ne trouve pas le contenu
            preg_match('/<div class="article-content">(.*?)<\/div>/s', $html_content, $content_matches);
            $content = isset($content_matches[1]) ? $content_matches[1] : '';
        }
        
        // Extraire le slug du nom de fichier
        $file_info = pathinfo($file_to_edit);
        $filename_without_ext = $file_info['filename'];
        // Supprimer le préfixe de date si présent (format YYYYMMDD-)
        $slug = preg_replace('/^\d{8}-/', '', $filename_without_ext);
    } else {
        $error = "L'article demandé n'existe pas.";
    }
}

// Traitement du formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $featured_image = $_POST['featured_image'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $custom_slug = trim($_POST['slug'] ?? '');
    $tags = $_POST['tags'] ?? '';
    
    if (empty($title)) {
        $error = "Le titre est obligatoire.";
    } else {
        // Créer un slug à partir du titre pour le nom de fichier ou utiliser le slug personnalisé
        $slug = !empty($custom_slug) ? $custom_slug : generateSlug($title);
        $date_prefix = date('Ymd');
        $file_name = $edit_mode ? $file_to_edit : $date_prefix . '-' . $slug . '.html';
    }
}  
// Traitement de la sauvegarde automatique
if (isset($_POST['auto_save']) && $_POST['auto_save'] === '1') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $featured_image = $_POST['featured_image'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $custom_slug = trim($_POST['slug'] ?? '');
    $tags = $_POST['tags'] ?? '';
    
    if (!empty($title)) {
        // Générer un nom de session unique pour cet article
        $session_key = 'auto_save_' . md5($title);
        
        // Sauvegarder les données dans la session
        $_SESSION[$session_key] = [
            'title' => $title,
            'content' => $content,
            'featured_image' => $featured_image,
            'meta_description' => $meta_description,
            'slug' => $custom_slug,
            'tags' => $tags,
            'timestamp' => time()
        ];
        
        echo "Article auto-sauvegardé";
        exit; // Arrêter l'exécution pour les requêtes AJAX
    }
}
// Gestion de l'upload d'image
$image_path = $featured_image;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Validation de l'image
    $validation_result = validateImage($_FILES['image']);
    if ($validation_result !== true) {
        $error = $validation_result;
    } else {
        $target_dir = $config['images_dir'];
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['image']['name']);
        $file_ext = strtolower($file_info['extension']);
        
        if (in_array($file_ext, $config['allowed_image_types'])) {
            $new_file_name = $slug . '-' . time() . '.' . $file_ext;
            $target_file = $target_dir . $new_file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Chemin d'image standardisé - stocker sans le préfixe "../" pour cohérence
                $image_path = 'images/articles/' . $new_file_name;
                
                // Optimiser l'image si c'est un format que nous pouvons optimiser
                $mime_type = mime_content_type($target_file);
                if (in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    optimizeImage($target_file, $target_file);
                }
            } else {
                $error = "Erreur lors de l'upload de l'image.";
            }
        } else {
            $error = "Format d'image non pris en charge.";
        }
    }
}
function optimizeImage($source_path, $destination_path, $quality = 85) {
    // Obtenir les informations de l'image
    $info = getimagesize($source_path);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source_path);
            imagejpeg($image, $destination_path, $quality);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            // Préserver la transparence
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $destination_path, floor($quality / 10));
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source_path);
            imagegif($image, $destination_path);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source_path);
            imagewebp($image, $destination_path, $quality);
            break;
        default:
            return false;
    }
    
    imagedestroy($image);
    return true;
}
        
        if (empty($error)) {
            // Préparation des tags HTML
            $tags_html = '';
            if (!empty($tags)) {
                $tag_array = array_map('trim', explode(',', $tags));
                foreach ($tag_array as $tag) {
                    if (!empty($tag)) {
                        $tags_html .= '<a href="#" class="tag">' . htmlspecialchars($tag) . '</a> ';
                    }
                }
            }
            
            // Si la méta description est vide, générer à partir du contenu
            if (empty($meta_description)) {
                $meta_description = substr(strip_tags($content), 0, 160);
            }
            
            // Préparation du contenu HTML de l'article
            $article_html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' - Loomble</title>
    <link rel="stylesheet" href="../style.css">
    <meta name="description" content="' . htmlspecialchars($meta_description) . '">
</head>
<body>
    <!-- Navigation -->
    <header>
        <a href="../index.html" class="logo">Loomble</a>
        <div class="menuToggle"></div>
    </header>
    
    <ul class="navigation">
        <li><a href="../index.html#home">Accueil</a></li>
        <li><a href="../index.html#about">Présentation</a></li>
        <li><a href="../index.html#posts">Le livre</a></li>
        <li><a href="../index.html#services">Les 3 projets</a></li>
        <li><a href="../index.html#contact">Contact</a></li>
    </ul>
    
    <div class="article-container" style="margin-top: 120px;">
        <article class="article-header">
            <h1 class="article-title">' . htmlspecialchars($title) . '</h1>
            <div class="article-meta">
                <span class="article-date">Publié le ' . date('d/m/Y') . '</span>
            </div>
            
            // Dans la partie où l'image est intégrée à l'article
' . (!empty($image_path) ? '<div class="article-featured-image">
<img src="/' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($title) . '">
</div>' : '') . '
            
            <div class="article-content">
                ' . $content . '
            </div>
            
            <div class="article-tags">
                ' . $tags_html . '
                <a href="../index.html#posts" class="tag">Retour aux articles</a>
            </div>
        </article>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footerContent">
            <div class="footerSection">
                <h3>Loomble</h3>
                <p>Loomble est la fabrique des liens. Elle tisse les liens dans l\'unique objectif de la quête de la vérité et de vivre en paix.</p>
            </div>
            <div class="footerSection">
                <h3>Liens rapides</h3>
                <ul class="footerLinks">
                    <li><a href="../index.html#home">Accueil</a></li>
                    <li><a href="../index.html#about">Présentation</a></li>
                    <li><a href="../index.html#posts">Livre</a></li>
                    <li><a href="../index.html#services">Les 3 projets</a></li>
                    <li><a href="../index.html#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footerSection">
                <h3>Contact</h3>
                <ul class="contactInfo">
                    <li>
                        <span><img src="../images/email.png" alt="Email"></span>
                        <span>lien@loomble.net</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; ' . date('Y') . ' Loomble. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script>
        const menuToggle = document.querySelector(".menuToggle");
        const navigation = document.querySelector(".navigation");
        
        menuToggle.addEventListener("click", () => {
            menuToggle.classList.toggle("active");
            navigation.classList.toggle("open");
        });
        
        window.addEventListener("scroll", function() {
            const header = document.querySelector("header");
            header.classList.toggle("sticky", window.scrollY > 0);
        });
    </script>
</body>
</html>';
            
            // Enregistrer le fichier HTML
            $articles_dir = $config['articles_dir'];
            if (!is_dir($articles_dir)) {
                mkdir($articles_dir, 0755, true);
            }
            
            $file_path = $articles_dir . $file_name;
            if (file_put_contents($file_path, $article_html)) {
                $message = $edit_mode ? "L'article a été mis à jour avec succès." : "L'article a été publié avec succès.";
                
                // Vérifier si on doit rediriger vers le tableau de bord
                if (isset($_POST['save_and_exit'])) {
                    header('Location: dashboard.php?message=' . urlencode($message));
                    exit;
                }
                
                // En mode édition, mettre à jour les variables
                if (!$edit_mode) {
                    $edit_mode = true;
                    $file_to_edit = $file_name;
                }
            } else {
                $error = "Erreur lors de l'enregistrement de l'article.";
            }
        }
    }
}

// Fonction pour ouvrir le gestionnaire d'images dans une popup
function openImageManager() {
    echo '<script>
        window.open("image-manager.php?popup=1", "imageManager", "width=800,height=600");
    </script>';
}

// Si on demande d'ouvrir le gestionnaire d'images
if (isset($_GET['open_image_manager'])) {
    openImageManager();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loomble Admin - <?php echo $edit_mode ? 'Modifier' : 'Nouvel'; ?> article</title>
    <link rel="stylesheet" href="../style.css">

    <script src="https://cdn.tiny.cloud/1/gmko9xvnoatxurakhdow9otq5wzrpqdokob5a87xquo0ue1p/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '#content',
            apiKey: 'gmko9xvnoatxurakhdow9otq5wzrpqdokob5a87xquo0ue1p',
            height: 500,
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount',
                'codesample emoticons hr nonbreaking pagebreak quickbars'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic underline strikethrough | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'link image media table | removeformat | codesample blockquote | ' +
                'forecolor backcolor | emoticons hr | pagebreak | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 16px; -webkit-font-smoothing: antialiased; }',
            file_picker_types: 'image',
            images_upload_handler: function (blobInfo, success, failure) {
                alert("Pour ajouter des images, veuillez utiliser l'option d'image mise en avant ou utiliser le gestionnaire d'images.");
                failure('L\'upload direct n\'est pas pris en charge dans l\'éditeur.');
            },
            codesample_languages: [
                { text: 'HTML/XML', value: 'markup' },
                { text: 'CSS', value: 'css' },
                { text: 'JavaScript', value: 'javascript' },
                { text: 'PHP', value: 'php' },
                { text: 'Python', value: 'python' },
                { text: 'Ruby', value: 'ruby' },
                { text: 'Java', value: 'java' },
                { text: 'C', value: 'c' },
                { text: 'C#', value: 'csharp' },
                { text: 'C++', value: 'cpp' }
            ],
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            paste_data_images: false,
            relative_urls: false,
            convert_urls: false
        });
    </script>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 40px;
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
        .editor-form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .thumbnail-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            display: none;
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
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
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
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-primary:hover {
            background-color: #003d1a;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
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
        .image-manager-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-left: 10px;
        }
        .image-manager-btn:hover {
            background-color: #e0e0e0;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-col {
            flex: 1;
        }
        .help-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        .tab-container {
            margin-bottom: 20px;
        }
        .tab-nav {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        .tab-btn.active {
            border-bottom: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 500;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><?php echo $edit_mode ? 'Modifier l\'article' : 'Nouvel article'; ?></h1>
            <div>
                <a href="dashboard.php" class="back-link">← Retour au tableau de bord</a>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form class="editor-form" method="post" action="" enctype="multipart/form-data">
            <div class="tab-container">
                <div class="tab-nav">
                    <button type="button" class="tab-btn active" data-tab="content-tab">Contenu</button>
                    <button type="button" class="tab-btn" data-tab="settings-tab">Paramètres</button>
                </div>
                
                <div id="content-tab" class="tab-content active">
                    <div class="form-group">
                        <label for="title">Titre de l'article</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="featured_image">Image mise en avant</label>
                        <div style="display: flex; align-items: center;">
                            <input type="text" id="featured_image" name="featured_image" class="form-control" value="<?php echo htmlspecialchars($featured_image); ?>" placeholder="URL de l'image">
                            <a href="?open_image_manager=1" class="image-manager-btn">Gestionnaire d'images</a>
                        </div>
                        
                        <?php if ($featured_image): ?>
                            <img src="../<?php echo htmlspecialchars($featured_image); ?>" alt="Image actuelle" class="thumbnail-preview" style="display: block;">
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Télécharger une nouvelle image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <img id="image-preview" class="thumbnail-preview" alt="Aperçu de l'image">
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Contenu de l'article</label>
                        <textarea id="content" name="content"><?php echo $content; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags">Tags (séparés par des virgules)</label>
                        <input type="text" id="tags" name="tags" class="form-control" value="<?php echo htmlspecialchars($tags); ?>" placeholder="exemple, tutoriel, actualité">
                    </div>
                </div>
                
                <div id="settings-tab" class="tab-content">
                    <div class="form-group">
                        <label for="slug">Slug (URL personnalisée)</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="<?php echo htmlspecialchars($slug); ?>" placeholder="mon-article">
                        <p class="help-text">Laissez vide pour générer automatiquement à partir du titre. Utilisez uniquement des lettres minuscules, chiffres et tirets.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="form-control" rows="3" placeholder="Brève description de l'article pour les moteurs de recherche (max 160 caractères)"><?php echo htmlspecialchars($meta_description); ?></textarea>
                        <p class="help-text">Laissez vide pour générer automatiquement à partir du contenu. Limité à 160 caractères pour un meilleur référencement.</p>
                    </div>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
                <button type="submit" name="save_and_exit" class="btn btn-secondary">Enregistrer et quitter</button>
            </div>
        </form>
    </div>

    <script>
      // Remplacer ce script JavaScript dans editor.php (environ ligne 600)

// Prévisualisation de l'image téléchargée
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
});

// Prévisualisation de l'image par URL
document.getElementById('featured_image').addEventListener('change', function() {
    const preview = document.querySelector('.thumbnail-preview');
    const url = this.value;
    
    if (url) {
        // Construire correctement le chemin de prévisualisation
        let previewUrl = url;
        
        // Si le chemin ne commence pas par http (donc un chemin relatif)
        if (!url.startsWith('http')) {
            // Si le chemin ne commence pas déjà par "../"
            if (!url.startsWith('../')) {
                // Ajouter "../" pour la prévisualisation depuis l'admin
                previewUrl = '../' + url;
            }
        }
        
        preview.src = previewUrl;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
        
        // Système d'onglets
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Désactiver tous les onglets
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Activer l'onglet cliqué
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Générer automatiquement le slug à partir du titre
        document.getElementById('title').addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            // Ne générer le slug que si le champ est vide
            if (slugField.value === '') {
                // Fonction simplifiée de génération de slug (à compléter avec la gestion des accents)
                const title = this.value;
                const slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Supprimer les caractères spéciaux
                    .replace(/\s+/g, '-')     // Remplacer les espaces par des tirets
                    .replace(/-+/g, '-')      // Éviter les tirets multiples
                    .trim();                   // Supprimer les espaces en début et fin
                
                slugField.value = slug;
            }
        });

        // Sauvegarde automatique
let autoSaveTimer;
const autoSaveInterval = 60000; // 1 minute

function autoSave() {
    const title = document.getElementById('title').value;
    const content = tinymce.get('content').getContent();
    
    if (title && content) {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('featured_image', document.getElementById('featured_image').value);
        formData.append('meta_description', document.getElementById('meta_description').value);
        formData.append('slug', document.getElementById('slug').value);
        formData.append('tags', document.getElementById('tags').value);
        formData.append('auto_save', '1');
        
        fetch('editor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            const autoSaveStatus = document.getElementById('autoSaveStatus');
            if (autoSaveStatus) {
                autoSaveStatus.textContent = 'Sauvegarde automatique effectuée à ' + new Date().toLocaleTimeString();
                autoSaveStatus.style.opacity = '1';
                setTimeout(() => {
                    autoSaveStatus.style.opacity = '0';
                }, 3000);
            }
        })
        .catch(error => console.error('Erreur de sauvegarde automatique:', error));
    }
}

// Initialiser la sauvegarde automatique
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un indicateur de sauvegarde
    const btnGroup = document.querySelector('.btn-group');
    const statusElement = document.createElement('div');
    statusElement.id = 'autoSaveStatus';
    statusElement.style.cssText = 'margin-left: 20px; color: #666; font-size: 0.8em; opacity: 0; transition: opacity 0.5s;';
    btnGroup.appendChild(statusElement);
    
    // Démarrer la sauvegarde automatique
    autoSaveTimer = setInterval(autoSave, autoSaveInterval);
    
    // Arrêter la sauvegarde automatique si l'utilisateur quitte la page
    window.addEventListener('beforeunload', function() {
        clearInterval(autoSaveTimer);
    });
});
    </script>
</body>
</html>
