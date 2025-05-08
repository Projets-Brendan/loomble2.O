<?php
// Chemin vers le répertoire des articles
$articles_dir = 'articles/';

// Récupérer la liste des articles
$articles = [];

if (is_dir($articles_dir)) {
    $files = scandir($articles_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            // Lire le fichier pour extraire le titre et le contenu
            $content = file_get_contents($articles_dir . $file);
            
            // Extraire le titre
            preg_match('/<h1 class="article-title">(.*?)<\/h1>/', $content, $title_matches);
            $title = isset($title_matches[1]) ? $title_matches[1] : pathinfo($file, PATHINFO_FILENAME);
            
            // Extraire l'image mise en avant
            preg_match('/<div class="article-featured-image">.*?<img src="(.*?)"/', $content, $image_matches);
            $featured_image = isset($image_matches[1]) ? $image_matches[1] : '';
            
            // Extraire un extrait du contenu
            preg_match('/<div class="article-content">(.*?)<\/div>\s*<div class="article-tags">/', $content, $content_matches, PREG_OFFSET_CAPTURE);
            if (isset($content_matches[1])) {
                $article_content = $content_matches[1][0];
            } else {
                preg_match('/<div class="article-content">(.*?)<\/div>/s', $content, $content_matches);
                $article_content = isset($content_matches[1]) ? $content_matches[1] : '';
            }
            
            // Créer un extrait court
            $excerpt = strip_tags($article_content);
            $excerpt = preg_replace('/\s+/', ' ', $excerpt);
            $excerpt = substr($excerpt, 0, 150) . '...';
            
            // Obtenir la date de création du fichier
            $created = date('Y-m-d', filemtime($articles_dir . $file));
            
            $articles[] = [
                'file' => $file,
                'title' => $title,
                'excerpt' => $excerpt,
                'featured_image' => $featured_image,
                'created' => $created
            ];
        }
    }
    
    // Trier par date décroissante
    usort($articles, function($a, $b) {
        return strtotime($b['created']) - strtotime($a['created']);
    });
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Loomble est la fabrique des liens. - Loom voulant dire en anglais machine à tisser - Elle tisse les liens dans l'unique objectif de la quête de la vérité et de vivre en paix. Inspirée par les écrits de Carl Jung."
    />
    <title>Loomble</title>
    <link rel="stylesheet" href="style.css" />

    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="images/favicon_io/favicon-32x32.png"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="images/favicon_io/favicon-16x16.png"
    />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="images/favicon_io/apple-touch-icon.png"
    />
    <link
      rel="android-chrome-192x192"
      sizes="192x192"
      href="images/favicon_io/android-chrome-192x192.png"
    />
    <link
      rel="android-chrome-512x512"
      sizes="512x512"
      href="images/favicon_io/android-chrome-512x512.png"
    />
  </head>
  <body>
    <!-- Navigation -->
    <header>
      <a href="#" class="logo">Loomble</a>
      <div class="menuToggle"></div>
    </header>

    <ul class="navigation">
      <li><a href="#home">Accueil</a></li>
      <li><a href="#about">Présentation</a></li>
      <li><a href="#posts">Le livre</a></li>
      <li><a href="#services">Les 3 projets</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>

    <!-- Titre et accueil du site -->
    <section class="banner" id="home">
      <img src="images/banniere.jpg" class="cover" />
      <div class="contentBx">
        <h2>J'ai voulu être un arbre</h2>
        <h4>
          Synthétisation de 35 ans de vie<br />
          Révélation par Carl Jung
        </h4>
        <a href="#about" class="btn">Présentation</a>
      </div>
    </section>

    <section class="about" id="about">
      <div class="title">
        <h2>Présentation</h2>
      </div>
      <div class="contentBx">
        <div class="content">
          <p>
            Bienvenue dans ma demeure virtuelle !
            <br /><br />
          </p>

          <p>
            Loomble est la fabrique des liens. - Loom voulant dire en anglais
            machine à tisser - Elle tisse les liens dans l'unique objectif de la
            quête de la vérité. Il est vital de créer ces liens afin d'avoir une
            cohérence entre les éléments, les êtres ! Je vous propose de créer
            un groupe de paix, non pas de manière utopique mais de façon
            concrète et réelle. Rassemblons-nous. Un collectif suffisamment
            puissant pour influer sur notre monde, par un respect, un bonheur,
            une fermeté, un amour, une fidélité, une autonomie, l'empathie, une
            sensibilité, une joie commune, et comprendre la profonde nature de
            l'être, humain, mais pas seulement, vous l'avez compris. Si vous
            sentez une connexion avec ces mots mais aussi avec l'histoire que je
            vous partage, contactez-moi si vous êtes intéressés par ce début
            d'histoire concrète qu'on écrira et qu'on créera ensemble. Et si je
            vous disais qu'elle avait déjà lieu ? <br /><br />
            En attendant, pour établir un premier contact et lien avec vous,
            j'ai pris la décision de me livrer entièrement. Étant un ancien
            malade et pair-aidant, j'ai conscience que le savoir expérientiel
            peut grandement apporter dans les deux sens. C'est pour cette raison
            que j'écris ce livre, je le partage et le construis petit à petit
            avec vous. Chaque jour, j'écris dans l'élan d'une meilleure
            individuation et une meilleure compréhension de mon environnement,
            sur la base des expériences et des savoirs de Carl Jung, un
            psychologue et psychiatre suisse mais surtout guide de l'âme.
            J'aimerais devenir dans un futur proche la relève autoproclamée de
            Carl Jung, afin de continuer son travail et son œuvre qui est
            précieux et dont je suis convaincu qu'il peut guider vers une
            certaine vérité justement.
          </p>
        </div>

        <div class="content">
          <div class="imgBx">
            <img src="images/tisser.jpg" class="cover" />
          </div>
        </div>
      </div>
    </section>

    <!-- Les articles dédiés au livre -->
    <section class="posts" id="posts">
      <div class="title">
        <h2>J'ai voulu être un arbre</h2>
        <p>
          Voici les derniers articles de mon livre. Je vous invite à me
          contacter si mes écrits stimulent vos énergies. Pour que l'énergie
          puisse circuler entre nous, il est important de créer le lien.
        </p>
      </div>

      <div class="postGrid">
        <?php if (empty($articles)): ?>
          <p>Aucun article n'a encore été publié.</p>
        <?php else: ?>
          <?php foreach ($articles as $article): ?>
            <?php
              // Pour être sûr d'avoir une image, utiliser une image par défaut si nécessaire
              $image_src = 'images/banniere.jpg'; // Image par défaut
              
              if (!empty($article['featured_image'])) {
                  // Traiter le chemin d'image
                  $featured_image = $article['featured_image'];
                  
                  // Si le chemin commence par ../
                  if (strpos($featured_image, '../') === 0) {
                      $featured_image = substr($featured_image, 3);
                      $image_src = $featured_image;
                  }
                  // Si le chemin commence par /
                  else if (strpos($featured_image, '/') === 0) {
                      $featured_image = substr($featured_image, 1);
                      $image_src = $featured_image;
                  }
                  // Sinon, on l'utilise tel quel
                  else {
                      $image_src = $featured_image;
                  }
              }
              
              // Pour débogage
              // echo "<!-- Image originale: " . htmlspecialchars($article['featured_image']) . " -->\n";
              // echo "<!-- Image traitée: " . htmlspecialchars($image_src) . " -->\n";
              
              // Lien vers l'article
              $article_url = $articles_dir . urlencode($article['file']);
            ?>
            <div class="postCard">
              <div class="imgBx">
                <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" />
              </div>
              <div class="contentBx">
                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                <p><?php echo htmlspecialchars($article['excerpt']); ?></p>
                <a href="<?php echo htmlspecialchars($article_url); ?>" class="btn">Lire la suite</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- Les projets -->
    <section class="services" id="services">
      <div class="title">
        <h2>Les trois projets principaux</h2>
        <p>
          Comment me mettre concrètement à l'action pour créer du lien ? Je
          travaille sur trois objectifs qui permettront à la fois d'avancer dans
          le processus d'individuation, de créer un lieu commun virtuel et, à
          long terme, physique. Le dernier objectif concerne la musique.
        </p>
      </div>
      <div class="contentBx">
        <div class="serviceBox">
          <img
            src="images/individuation.jpeg"
            alt="Service Icon"
            class="serviceIcon"
          />
          <h3>Individuation</h3>
          <p>
            Le Moi, l'inconscient individuel, l'inconscient collectif et le Soi
            seront les thèmes principaux de ce projet. Je vais vous partager mes
            réflexions et mes expériences sur le sujet et vous proposer des
            exercices pratiques pour vous aider à avancer dans votre propre
            processus d'individuation.
          </p>
        </div>
        <div class="serviceBox">
          <img src="images/source.jpg" alt="Service Icon" class="serviceIcon" />
          <h3>Lieu commun "Source"</h3>
          <p>
            Projet central ! <br />
            Ensemble, nous allons créer un lieu commun virtuel et physique, la
            "source", où des pratiques d'éveil seront proposées et où une
            attention particulière sera portée à la quête de la vérité.
          </p>
        </div>
        <div class="serviceBox">
          <img
            src="images/musique.jpeg"
            alt="Service Icon"
            class="serviceIcon"
          />
          <h3>La musique</h3>
          <p>
            La musique est un moyen puissant de créer des liens entre les
            personnes. Elle est pour moi une technique sensible afin de stimuler
            le "flair" pendant des pratiques de création de musique
            électronique. - Le sujet du "flair" est traité dans mon livre.
            Globalement, il s'agit de sentir l'énergie et de pouvoir la diffuser
            avec les autres qui peuvent l'écouter.
          </p>
        </div>
      </div>
    </section>

    <!-- Le formulaire -->
    <section class="contact" id="contact">
      <div class="title">
        <h2>Me contacter</h2>
        <p>
          Si vous souhaitez me contacter, n'hésitez pas à remplir le formulaire
          ci-dessous. Je vous répondrai dès que possible.
        </p>
      </div>
      <div class="contactForm">
        <form action="process_contact.php" method="post">
          <div class="formRow">
            <div class="inputBox">
              <input type="text" name="name" required />
              <span>Prénom et nom</span>
            </div>
            <div class="inputBox">
              <input type="email" name="email" required />
              <span>Email</span>
            </div>
          </div>
          <div class="inputBox">
            <input type="text" name="subject" required />
            <span>Sujet</span>
          </div>
          <div class="inputBox">
            <textarea name="message" required></textarea>
            <span>Votre message</span>
          </div>
          <div class="inputBox" style="display: none">
            <input type="text" name="fax" value="" />
            <span>Ne remplissez pas ce champ</span>
          </div>
          <div class="inputBox">
            <input type="submit" value="Envoyer le message" />
          </div>
        </form>
      </div>
    </section>

    <!-- Informations complémentaires -->
    <footer>
      <div class="footerContent">
        <div class="footerSection">
          <h3>Loomble</h3>
          <p>
            Loomble est la fabrique des liens. - Loom voulant dire en anglais
            machine à tisser - Elle tisse les liens dans l'unique objectif de la
            quête de la vérité et de vivre en paix.
          </p>
        </div>
        <div class="footerSection">
          <h3>Liens rapides</h3>
          <ul class="footerLinks">
            <li><a href="#home">Accueil</a></li>
            <li><a href="#about">Présentation</a></li>
            <li><a href="#posts">Livre</a></li>
            <li><a href="#services">Les 3 projets</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
        <div class="footerSection">
          <h3>Contact</h3>
          <ul class="contactInfo">
            <li>
              <span><img src="images/email.png" alt="Email" /></span>
              <span>lien@loomble.net</span>
            </li>
            <li>
              <span><img src="images/site.png" alt="Email" /></span>
              <span>Lien essentiel : https://la-piste.fr</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2025 Loomble. All Rights Reserved. <a href="admin/index.php" class="admin-link">Admin</a></p>
      </div>
    </footer>

    <script>
      const menuToggle = document.querySelector(".menuToggle");
      const navigation = document.querySelector(".navigation");

      menuToggle.addEventListener("click", () => {
        menuToggle.classList.toggle("active");
        navigation.classList.toggle("open");
      });

      window.addEventListener("scroll", function () {
        const header = document.querySelector("header");
        header.classList.toggle("sticky", window.scrollY > 0);
      });

      // Smooth scroll pour les liens de navigation
      document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
          e.preventDefault();

          const targetId = this.getAttribute("href");
          const targetElement = document.querySelector(targetId);

          if (targetElement) {
            // Fermer le menu mobile si ouvert
            if (navigation.classList.contains("open")) {
              menuToggle.classList.remove("active");
              navigation.classList.remove("open");
            }

            window.scrollTo({
              top: targetElement.offsetTop - 80, // Ajuster pour le header
              behavior: "smooth",
            });
          }
        });
      });
    </script>
  </body>
</html>
