<?php
// admin/preview.php
require_once 'includes/auth_check.php';

// Vérifier si des données ont été envoyées
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['title']) || !isset($_POST['content'])) {
    header('Location: editor.php');
    exit;
}

$title = trim($_POST['title']);
$content = $_POST['content'];
$featured_image = $_POST['featured_image'] ?? '';

// Extraction d'un extrait pour la méta description
$excerpt = strip_tags($content);
$excerpt = preg_replace('/\s+/', ' ', $excerpt);
$excerpt = substr($excerpt, 0, 160);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo $config['site_name']; ?> (Prévisualisation)</title>
    <link rel="stylesheet" href="../style.css">
    <meta name="description" content="<?php echo htmlspecialchars($excerpt); ?>">
    <style>
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #f39c12;
            color: white;
            text-align: center;
            padding: 10px;
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preview-banner-content {
            flex: 1;
            text-align: center;
            font-weight: bold;
        }
        .preview-actions {
            display: flex;
            gap: 10px;
        }
        .preview-btn {
            padding: 5px 15px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            cursor: pointer;
        }
        .preview-back {
            background-color: #3498db;
        }
        .preview-publish {
            background-color: #2ecc71;
        }
        body {
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <div class="preview-banner">
        <div class="preview-banner-content">
            MODE PRÉVISUALISATION - Cet article n'est pas encore publié
        </div>
        <div class="preview-actions">
            <a href="javascript:history.back()" class="preview-btn preview-back">Retour à l'éditeur</a>
            <form id="publish-form" action="editor.php" method="post" style="display:inline;">
                <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                <input type="hidden" name="content" value="<?php echo htmlspecialchars($content); ?>">
                <input type="hidden" name="featured_image" value="<?php echo htmlspecialchars($featured_image); ?>">
                <input type="hidden" name="publish" value="1">
                <button type="submit" class="preview-btn preview-publish">Publier</button>
            </form>
        </div>
    </div>
    
    <!-- Navigation -->
    <header>
        <a href="../index.html" class="logo"><?php echo $config['site_name']; ?></a>
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
            <h1 class="article-title"><?php echo htmlspecialchars($title); ?></h1>
            <div class="article-meta">
                <span class="article-date">Prévisualisation du <?php echo date('d/m/Y'); ?></span>
            </div>
            
            <?php if ($featured_image): ?>
            <div class="article-featured-image">
                <img src="<?php echo htmlspecialchars($featured_image); ?>" alt="<?php echo htmlspecialchars($title); ?>">
            </div>
            <?php endif; ?>
            
            <div class="article-content">
                <?php echo $content; ?>
            </div>
            
            <div class="article-tags">
                <a href="../index.html#posts" class="tag">Retour aux articles</a>
            </div>
        </article>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footerContent">
            <div class="footerSection">
                <h3><?php echo $config['site_name']; ?></h3>
                <p>Loomble est la fabrique des liens. Elle tisse les liens dans l'unique objectif de la quête de la vérité et de vivre en paix.</p>
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
            <p>&copy; <?php echo date('Y'); ?> <?php echo $config['site_name']; ?>. All Rights Reserved.</p>
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
</html>
