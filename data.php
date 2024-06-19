<?php
$servername = "localhost";
$username = "root";
$password = "///";
$dbname = "esp32_data";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['temperature']) && isset($_POST['humidity']) && isset($_POST['device_id'])) {
    $temperature = htmlspecialchars($_POST['temperature']);
    $humidity = htmlspecialchars($_POST['humidity']);
    $device_id = htmlspecialchars($_POST['device_id']);
    $vocIndex = isset($_POST['vocIndex']) && $_POST['vocIndex'] !== 'NULL' ? htmlspecialchars($_POST['vocIndex']) : null;
    $light = isset($_POST['light']) && $_POST['light'] !== 'NULL' ? htmlspecialchars($_POST['light']) : null;

    // Debugging output
    error_log("Received data - Temperature: $temperature, Humidity: $humidity, VOC Index: $vocIndex, Device ID: $device_id, Light: $light");

    // Valider les données
    if (is_numeric($temperature) && is_numeric($humidity) && !empty($device_id)) {
        // Préparer et exécuter la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO readings (temperature, humidity, vocIndex, light, device_id) VALUES (?, ?, ?, ?, ?)");
        if ($vocIndex === null && $light === null) {
            $stmt->bind_param("ddsss", $temperature, $humidity, $vocIndex, $light, $device_id);
        } else if ($vocIndex === null) {
            $stmt->bind_param("ddsss", $temperature, $humidity, $vocIndex, $light, $device_id);
        } else if ($light === null) {
            $stmt->bind_param("dddis", $temperature, $humidity, $vocIndex, $light, $device_id);
        } else {
            $stmt->bind_param("dddis", $temperature, $humidity, $vocIndex, $device_id);
        }

        if ($stmt->execute()) {
            echo "Données insérées avec succès";
        } else {
            echo "Erreur: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Données invalides";
    }
} else {
    echo "Données non reçues";
}

$conn->close();
?>
