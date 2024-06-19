<?php
$servername = "localhost";
$username = "root";
$password = "///";
$dbname = "esp32_data";
$device_id = "esp32_1";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Protéger contre les injections SQL
$device_id = $conn->real_escape_string($device_id);

// Obtenir les données les plus récentes
$sql = "SELECT temperature, humidity, light, timestamp FROM readings WHERE device_id='$device_id' ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastReading = $row['timestamp'];
    $temperature = $row['temperature'];
    $humidity = $row['humidity'];
    $light = $row['light'];
    $lightk = $light * 10; // Multiplier par 10

    // Formater la valeur de lightk
    if ($lightk >= 1000) {
        $lightk = round($lightk / 1000, 1) . 'k';
    }
} else {
    $temperature = 0;
    $humidity = 0;
    $light = 0;
    $lightk = 0;
    $lastReading = "No data";
}

// Obtenir les statistiques pour la journée actuelle
$date = date('Y-m-d');

$sql = "SELECT MIN(temperature) as minTemp, MAX(temperature) as maxTemp, AVG(temperature) as avgTemp,
               MIN(humidity) as minHumidity, MAX(humidity) as maxHumidity, AVG(humidity) as avgHumidity,
               (SELECT timestamp FROM readings WHERE device_id='$device_id' AND temperature = (SELECT MIN(temperature) FROM readings WHERE device_id='$device_id' AND DATE(timestamp) = '$date') AND DATE(timestamp) = '$date' LIMIT 1) as minTempTime,
               (SELECT timestamp FROM readings WHERE device_id='$device_id' AND temperature = (SELECT MAX(temperature) FROM readings WHERE device_id='$device_id' AND DATE(timestamp) = '$date') AND DATE(timestamp) = '$date' LIMIT 1) as maxTempTime,
               (SELECT timestamp FROM readings WHERE device_id='$device_id' AND humidity = (SELECT MIN(humidity) FROM readings WHERE device_id='$device_id' AND DATE(timestamp) = '$date') AND DATE(timestamp) = '$date' LIMIT 1) as minHumidityTime,
               (SELECT timestamp FROM readings WHERE device_id='$device_id' AND humidity = (SELECT MAX(humidity) FROM readings WHERE device_id='$device_id' AND DATE(timestamp) = '$date') AND DATE(timestamp) = '$date' LIMIT 1) as maxHumidityTime
        FROM readings
        WHERE device_id='$device_id' AND DATE(timestamp) = '$date'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $minTemp = $row['minTemp'];
    $maxTemp = $row['maxTemp'];
    $avgTemp = $row['avgTemp'];
    $minHumidity = $row['minHumidity'];
    $maxHumidity = $row['maxHumidity'];
    $avgHumidity = $row['avgHumidity'];
    $minTempTime = $row['minTempTime'];
    $maxTempTime = $row['maxTempTime'];
    $minHumidityTime = $row['minHumidityTime'];
    $maxHumidityTime = $row['maxHumidityTime'];
} else {
    $minTemp = $maxTemp = $avgTemp = 0;
    $minHumidity = $maxHumidity = $avgHumidity = 0;
    $minTempTime = $maxTempTime = $minHumidityTime = $maxHumidityTime = "No data";
}

// Créer un tableau associatif avec les données
$data = array(
    'temperature' => $temperature,
    'minTemp' => $minTemp,
    'maxTemp' => $maxTemp,
    'avgTemp' => $avgTemp,
    'minTempTime' => $minTempTime,
    'maxTempTime' => $maxTempTime,
    'humidity' => $humidity,
    'minHumidity' => $minHumidity,
    'maxHumidity' => $maxHumidity,
    'avgHumidity' => $avgHumidity,
    'minHumidityTime' => $minHumidityTime,
    'maxHumidityTime' => $maxHumidityTime,
    'light' => $light,
    'lightk' => $lightk,
    'lastReading' => $lastReading
);

// Envoyer les données sous forme de JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
