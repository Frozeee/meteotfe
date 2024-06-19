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

// Obtenir les données les plus récentes pour l'ESP32_1
$sql1 = "SELECT temperature, humidity, light, timestamp FROM readings WHERE device_id='esp32_1' ORDER BY timestamp DESC LIMIT 1";
$result1 = $conn->query($sql1);

$data1 = array();

if ($result1->num_rows > 0) {
    $row = $result1->fetch_assoc();
    $data1 = array(
        'tempExt' => (float)$row['temperature'],
        'humExt' => (float)$row['humidity'],
        'light' => (float)$row['light']
    );
} else {
    $data1 = array(
        'tempExt' => 0,
        'humExt' => 0,
        'light' => 0
    );
}

// Obtenir les données les plus récentes pour l'ESP32_2
$sql2 = "SELECT temperature, humidity, vocIndex, timestamp FROM readings WHERE device_id='esp32_2' ORDER BY timestamp DESC LIMIT 1";
$result2 = $conn->query($sql2);

$data2 = array();

if ($result2->num_rows > 0) {
    $row = $result2->fetch_assoc();
    $data2 = array(
        'tempInt' => (float)$row['temperature'],
        'humInt' => (float)$row['humidity'],
        'vocIndex' => (float)$row['vocIndex']
    );
} else {
    $data2 = array(
        'tempInt' => 0,
        'humInt' => 0,
        'vocIndex' => 0
    );
}

$data = array_merge($data1, $data2);

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
