<!-- Correction du HTML -->
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
      <li><a href="#home">Acceuil</a></li>
      <li><a href="#about">Présentation</a></li>
      <li><a href="#posts">Le livre</a></li>
      <li><a href="#services">Les 3 projets</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>

    <!-- Titre et acceuil du site -->

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
            vous disais qu’elle avait déjà lieu ? <br /><br />
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
        <?php
        // Chemin vers le répertoire des articles
        $articles_dir = 'articles/'; // Assurez-vous que ce chemin est correct par rapport à index.php

        $articles = [];

        // Vérifier si le répertoire existe
        if (is_dir($articles_dir)) {
            // Lire tous les fichiers du répertoire
            $files = scandir($articles_dir);

            // Parcourir les fichiers
            foreach ($files as $file) {
                // S'assurer que c'est un fichier .html et non les entrées '.' ou '..'
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                    $file_path = $articles_dir . $file;

                    // Lire le contenu du fichier HTML de l'article
                    $content = file_get_contents($file_path);

                    // Extraire le titre (similaire à dashboard.php)
                    preg_match('/<h1 class="article-title">(.*?)<\/h1>/', $content, $title_matches);
                    $title = isset($title_matches[1]) ? htmlspecialchars($title_matches[1]) : pathinfo($file, PATHINFO_FILENAME);

                     // Extraire l'image mise en avant
                    preg_match('/<div class="article-featured-image">.*?<img src="(.*?)"/', $content, $image_matches);
                    $featured_image = isset($image_matches[1]) ? htmlspecialchars($image_matches[1]) : ''; // Utilisez une image par défaut si aucune n'est trouvée

                    // Extraire un extrait du contenu (similaire à dashboard.php)
                    preg_match('/<div class="article-content">(.*?)<\/div>/s', $content, $content_matches);
                    $excerpt = isset($content_matches[1]) ? strip_tags($content_matches[1]) : 'Aucun contenu disponible';
                    $excerpt = preg_replace('/\s+/', ' ', $excerpt); // Remplacer les espaces multiples par un simple espace
                    $excerpt = htmlspecialchars(substr($excerpt, 0, 150)) . (strlen($excerpt) > 150 ? '...' : ''); // Limiter à 150 caractères

                    // Ajouter l'article à notre liste avec le nom de fichier
                    $articles[] = [
                        'file' => $file,
                        'title' => $title,
                        'featured_image' => $featured_image,
                        'excerpt' => $excerpt,
                         // Vous pouvez ajouter d'autres informations si nécessaire (date, tags, etc.)
                         'created' => filemtime($file_path) // Récupérer le timestamp pour le tri
                    ];
                }
            }

            // Trier les articles par date de modification décroissante (les plus récents en premier)
             usort($articles, function($a, $b) {
                return $b['created'] - $a['created'];
            });
        }

        // Afficher les articles
        if (empty($articles)) {
            echo "<p>Aucun article n'a encore été publié.</p>";
        } else {
            foreach ($articles as $article) {
                // Déterminer le chemin de l'image, en s'assurant qu'il est correct par rapport à index.php
                // Si l'image est stockée dans un sous-dossier comme 'images/', le chemin doit en tenir compte.
                // Si featured_image est déjà un chemin complet depuis la racine du site (ex: /images/monimage.jpg)
                // utilisez simplement $article['featured_image'].
                // Si featured_image est un chemin relatif depuis le dossier articles (ex: ../images/monimage.jpg),
                // il faut ajuster le chemin pour qu'il soit correct depuis index.php.
                // Dans votre cas, les images semblent être dans ../images/articles/ ou similaires par rapport à l'éditeur.
                // Si featured_image dans le fichier article est 'images/nom_image.jpg' (relatif à la racine du site)
                $image_src = !empty($article['featured_image']) ? $article['featured_image'] : 'placeholder_image.jpg'; // Image par défaut si pas d'image

                // Assurez-vous que le chemin de l'article est correct par rapport à index.php
                $article_url = $articles_dir . urlencode($article['file']);


                echo '
                <div class="postCard">
                    <div class="imgBx">';
                // Afficher l'image si elle existe, sinon une image par défaut ou rien
                 if (!empty($article['featured_image'])) {
                     // Supposons que l'image_path dans le fichier article est 'images/uploads/mon-image.jpg'
                     // Si votre répertoire d'images est 'images/' à la racine du site, le chemin est déjà bon.
                     echo '<img src="' . $article['featured_image'] . '" alt="' . $article['title'] . '" />';
                 } else {
                     // Optionnel : afficher une image par défaut si aucune image n'est définie
                     // echo '<img src="images/default_article_image.jpg" alt="Image par défaut" />';
                 }
                echo '</div>
                    <div class="contentBx">
                        <h3>' . $article['title'] . '</h3>
                        <p>' . $article['excerpt'] . '</p>
                        <a href="' . $article_url . '" class="btn">Lire la suite</a>
                    </div>
                </div>';
            }
        }
        ?>
      </div>


      <div class="loadMore" style="display: none;">
         </div>
    </section>

    <!-- Les projets -->

    <section class="services" id="services">
      <div class="title">
        <h2>Les trois projets pricipaux</h2>
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
            <input type="submit" value="Send Message" />
          </div>
        </form>
      </div>
    </section>

    <!-- Informations complétaire -->

    <footer>
      <div class="footerContent">
        <div class="footerSection">
          <h3>Loomble</h3>
          <p>
            Loomble est la fabrique des liens. - Loom voulant dire en anglais
            machine à tisser - Elle tisse les liens dans l'unique objectif de la
            quête de la vérité et de vivre en paix.
          </p>
          <!-- <ul class="socialIcons">
            <li>
              <a href="#"><img src="images/facebook.png" alt="Facebook" /></a>
            </li>
            <li>
              <a href="#"><img src="images/twitter.png" alt="Twitter" /></a>
            </li>
            <li>
              <a href="#"><img src="images/instagram.png" alt="Instagram" /></a>
            </li>
          </ul>-->
        </div>
        <div class="footerSection">
          <h3>Quick Links</h3>
          <ul class="footerLinks">
            <li><a href="#home">Acceuil</a></li>
            <li><a href="#about">Présentation</a></li>
            <li><a href="#posts">Livre</a></li>
            <li><a href="#services">Les 3 projets</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
        <div class="footerSection">
          <h3>Contact Info</h3>
          <ul class="contactInfo">
            <!-- <li>
              <span><img src="images/location.png" alt="Location" /></span>
              <span>Rennes</span>
            </li>-->
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

      // Simuler le chargement de plus de posts
      const loadMoreBtn = document.querySelector(".loadMoreBtn");
      if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", function () {
          // Simulation de chargement
          this.textContent = "Loading...";

          setTimeout(() => {
            // Créer de nouveaux posts
            const postGrid = document.querySelector(".postGrid");

            // Images à utiliser en rotation
            const images = ["window.jpg", "girl.jpg", "architecture.jpg"];

            // Ajouter 3 nouveaux posts
            for (let i = 0; i < 3; i++) {
              const postCard = document.createElement("div");
              postCard.className = "postCard";

              const imgIndex = i % images.length;

              postCard.innerHTML = `
              <div class="imgBx">
                <img src="images/${images[imgIndex]}" alt="Blog image">
              </div>
              <div class="contentBx">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p>
                <a href="#" class="btn">Read More</a>
              </div>
            `;

              // Ajouter effet d'apparition
              postCard.style.opacity = "0";
              postGrid.appendChild(postCard);

              // Animation d'apparition
              setTimeout(() => {
                postCard.style.transition = "opacity 0.5s ease";
                postCard.style.opacity = "1";
              }, 100 * i);
            }

            // Restaurer le bouton
            this.textContent = "Load More";
          }, 1000);
        });
      }
    </script>
  </body>
</html>
