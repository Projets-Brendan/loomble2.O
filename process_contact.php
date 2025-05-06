<?php
session_start();

// Configuration
$to = "lien@loomble.net";
$submission_limit = 3;
$submission_window = 60;

// Vérification de la méthode de requête
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    echo "Méthode non autorisée.";
    exit;
}

// Fonction pour nettoyer et valider les données
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validation et nettoyage des champs
$name = sanitize_input($_POST["name"] ?? "");
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$subject = sanitize_input($_POST["subject"] ?? "");
$message = sanitize_input($_POST["message"] ?? "");
$fax_honeypot = sanitize_input($_POST["fax"] ?? "");

// Vérification du Honeypot
if (!empty($fax_honeypot)) {
    http_response_code(400);
    echo "Votre soumission semble être celle d'un robot.";
    exit;
}

// Validation des champs obligatoires
if (empty($name)) {
    http_response_code(400);
    echo "Veuillez entrer votre nom.";
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Veuillez entrer une adresse email valide.";
    exit;
}

if (empty($subject)) {
    http_response_code(400);
    echo "Veuillez entrer le sujet de votre message.";
    exit;
}

if (empty($message)) {
    http_response_code(400);
    echo "Veuillez entrer votre message.";
    exit;
}

// Protection contre le spam et les abus (limitation du taux)
if (isset($_SESSION['last_submission']) && (time() - $_SESSION['last_submission']) < $submission_window) {
    if (isset($_SESSION['submission_count']) && $_SESSION['submission_count'] >= $submission_limit) {
        http_response_code(429); // Too Many Requests
        echo "Vous avez envoyé trop de messages récemment. Veuillez réessayer plus tard.";
        exit;
    }
    $_SESSION['submission_count']++;
} else {
    $_SESSION['last_submission'] = time();
    $_SESSION['submission_count'] = 1;
}

// Construction de l'email
$email_subject = "Nouveau message de votre site web : " . $subject;
$email_body = "Nom: " . $name . "\n";
$email_body .= "Email: " . $email . "\n\n";
$email_body .= "Message:\n" . $message . "\n";
$headers = "From: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\n";
$headers .= "X-Mailer: PHP/" . phpversion();
$headers = str_replace(array("\r", "\n", "%0a", "%0d"), '', $headers);

// Envoi de l'email
if (mail($to, $email_subject, $email_body, $headers)) {
    http_response_code(200);
    echo "Merci ! Votre message a été envoyé.";
    $_SESSION['last_submission'] = time();
    $_SESSION['submission_count'] = 1;
} else {
    http_response_code(500);
    echo "Oops! Une erreur interne du serveur s'est produite lors de l'envoi de l'e-mail.";
}

?>
