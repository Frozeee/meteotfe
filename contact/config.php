// config.php
<?php
return [
    'smtp_host' => 'pro3.mail.ovh.net',
    'smtp_username' => 'contact@meteotfe.be',
    'smtp_password' => '///',
    'smtp_secure' => PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
    'smtp_port' => 587,
    'from_email' => 'contact@meteotfe.be',
    'from_name' => 'MeteoTFE'
];
?>
