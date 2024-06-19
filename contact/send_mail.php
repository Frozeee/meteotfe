<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Inclure le fichier de configuration
$config = require 'config.php';

// Désactiver l'affichage des erreurs pour éviter de divulguer des informations sensibles
ini_set('display_errors', 0);
error_reporting(0);

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['smtp_port'];

    // Paramètres de l'email
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['from_email']);

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = 'Nouveau message de ' . htmlspecialchars($_POST['name']);
    $mail->Body    = "Nom: " . htmlspecialchars($_POST['name']) . "<br>Email: " . htmlspecialchars($_POST['email']) . "<br><br>Message:<br>" . nl2br(htmlspecialchars($_POST['message']));

    // Envoi de l'email
    $mail->send();
    header('Content-Type: text/plain');
    echo 'Merci! Votre message a été envoyé.';
} catch (Exception $e) {
    header('Content-Type: text/plain');
    echo "Oups! Un problème est survenu et votre message n'a pas pu être envoyé.";
    error_log('Erreur d\'envoi de l\'email : ' . $mail->ErrorInfo);
}
?>
