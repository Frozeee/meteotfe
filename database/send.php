<?php

$servername = "localhost";
$username = "root";
$password = "///";
$dbname = "esp32_data";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$items_per_page = 10; // Nombre d'éléments par chargement
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Gestion des filtres
$filterDay = isset($_GET['filterDay']) ? $_GET['filterDay'] : 'all';
$filterSource = isset($_GET['filterSource']) ? $_GET['filterSource'] : 'all';

$dateFilter = "";
if ($filterDay == 'today') {
    $dateFilter = "AND DATE(timestamp) = CURDATE()";
} elseif ($filterDay == 'yesterday') {
    $dateFilter = "AND DATE(timestamp) = CURDATE() - INTERVAL 1 DAY";
} elseif ($filterDay == 'before_yesterday') {
    $dateFilter = "AND DATE(timestamp) = CURDATE() - INTERVAL 2 DAY";
}

$sourceFilter = "";
if ($filterSource == 'exterieur') {
    $sourceFilter = "AND device_id = 'esp32_1'";
} elseif ($filterSource == 'interieur') {
    $sourceFilter = "AND device_id != 'esp32_1'";
}

$sql = "SELECT temperature, humidity, light, vocIndex, timestamp, device_id 
        FROM readings 
        WHERE 1=1 $dateFilter $sourceFilter 
        ORDER BY timestamp DESC 
        LIMIT $offset, $items_per_page";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dateTime = new DateTime($row['timestamp']);
        $date = $dateTime->format('d/m/Y');
        $time = $dateTime->format('H:i');

        $isInterior = $row["device_id"] !== "esp32_1";  // Assumons que 'esp32_1' est extérieur
        $lightValue = $row["light"] * 10;

        if ($lightValue >= 1000) {
            $lightValue = round($lightValue / 1000, 1) . 'k';
        }

        $lightDisplay = $isInterior ? "/" : $lightValue . " lux"; // Afficher "/" pour intérieur, "0 lux" ou autre pour extérieur
        $vocIndexDisplay = $isInterior ? $row["vocIndex"] . " cov" : "/"; // Afficher "/" pour extérieur

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["temperature"]) . " °C</td>";
        echo "<td>" . htmlspecialchars($row["humidity"]) . " %</td>";
        echo "<td>" . htmlspecialchars($lightDisplay) . "</td>";
        echo "<td>" . htmlspecialchars($vocIndexDisplay) . "</td>"; // Afficher la qualité de l'air avec l'unité (cov) ou "/"
        echo "<td>" . htmlspecialchars($date) . "</td>";
        echo "<td>" . htmlspecialchars($time) . "</td>";
        echo "<td>" . htmlspecialchars($isInterior ? "Intérieur" : "Extérieur") . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Aucune donnée disponible</td></tr>";
}

$conn->close();
?>
