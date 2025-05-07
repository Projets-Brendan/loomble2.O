<?php

// admin/dashboard.php
require_once 'includes/auth_check.php';

// Récupérer les messages
$success_message = isset($_GET['message']) ? $_GET['message'] : '';
$error_message = isset($_GET['error']) ? $_GET['error'] : '';

// Récupérer la liste des articles existants
$articles_dir = '../articles/';
$articles = [];

if (is_dir($articles_dir)) {
    $files = scandir($articles_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            // Lire le fichier pour extraire le titre
            $content = file_get_contents($articles_dir . $file);
            preg_match('/<h1 class="article-title">(.*?)<\/h1>/', $content, $matches);
            $title = isset($matches[1]) ? $matches[1] : pathinfo($file, PATHINFO_FILENAME);
            
            // Obtenir la date de création du fichier
            $created = date('Y-m-d', filemtime($articles_dir . $file));
            
            $articles[] = [
                'file' => $file,
                'title' => $title,
                'created' => $created
            ];
        }
    }
    
    // Trier par date décroissante
    usort($articles, function($a, $b) {
        return strtotime($b['created']) - strtotime($a['created']);
    });
}

// Fonction pour extraire un extrait du contenu de l'article
function getArticleExcerpt($filePath, $maxLength = 150) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        preg_match('/<div class="article-content">(.*?)<\/div>/s', $content, $matches);
        
        if (isset($matches[1])) {
            $excerpt = strip_tags($matches[1]);
            $excerpt = preg_replace('/\s+/', ' ', $excerpt);
            return strlen($excerpt) > $maxLength ? substr($excerpt, 0, $maxLength) . '...' : $excerpt;
        }
    }
    return 'Aucun contenu disponible';
}

// Récupérer l'image mise en avant d'un article
function getArticleImage($filePath) {
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        preg_match('/<div class="article-featured-image">.*?<img src="(.*?)"/', $content, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loomble Admin - Tableau de bord</title>
    <link rel="stylesheet" href="../style.css">
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
        .admin-actions {
            margin-bottom: 30px;
        }
        .admin-btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            transition: 0.3s;
        }
        .admin-btn:hover {
            background: #003d1a;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-table th,
        .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .admin-table th {
            background-color: #f8f8f8;
            font-weight: 500;
        }
        .admin-logout {
            display: inline-block;
            margin-left: 10px;
            color: #666;
            text-decoration: none;
        }
        .admin-logout:hover {
            text-decoration: underline;
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
        .article-excerpt {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .article-actions {
            display: flex;
            gap: 10px;
        }
        .article-actions a {
            color: var(--primary);
            text-decoration: none;
        }
        .article-actions a:hover {
            text-decoration: underline;
        }
        .article-actions .delete {
            color: #e74c3c;
        }
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-card h3 {
            margin-bottom: 10px;
            color: var(--primary);
        }
        .stat-card p {
            font-size: 1.8em;
            font-weight: 500;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Tableau de bord Loomble</h1>
            <div>
                <span>Connecté en tant que <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="admin-logout">Déconnexion</a>
            </div>
        </div>
        
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Articles publiés</h3>
                <p><?php echo count($articles); ?></p>
            </div>
            <div class="stat-card">
                <h3>Dernier article</h3>
                <p><?php echo !empty($articles) ? date('d/m/Y', strtotime($articles[0]['created'])) : 'N/A'; ?></p>
            </div>
        </div>
        
        <div class="admin-actions">
            <a href="editor.php" class="admin-btn">Nouvel article</a>
        </div>
        
        <h2>Articles publiés</h2>
        
        <?php if (empty($articles)): ?>
            <p>Aucun article n'a encore été publié.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <div class="article-title"><?php echo htmlspecialchars($article['title']); ?></div>
                            <div class="article-excerpt">
                                <?php echo htmlspecialchars(getArticleExcerpt($articles_dir . $article['file'])); ?>
                            </div>
                        </td>
                        <td><?php echo $article['created']; ?></td>
                        <td class="article-actions">
                            <a href="editor.php?edit=<?php echo urlencode($article['file']); ?>">Modifier</a>
                            <a href="../articles/<?php echo urlencode($article['file']); ?>" target="_blank">Voir</a>
                            <a href="delete.php?file=<?php echo urlencode($article['file']); ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
