<?php
// Configuration des paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "///";
$dbname = "esp32_data";

// Création d'une nouvelle connexion à la base de données MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie si la connexion a échoué
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupère les paramètres de la requête GET, avec des valeurs par défaut si non définis
$type = isset($_GET['type']) ? $_GET['type'] : 'temperature';
$period = isset($_GET['period']) ? $_GET['period'] : 'hour';
$device_id = isset($_GET['device_id']) ? $_GET['device_id'] : 'esp32_1';

// Détermine l'intervalle de temps en fonction de la période spécifiée
switch($period) {
    case 'hour':
        $interval = '1 HOUR';
        break;
    case 'day':
        $interval = '1 DAY';
        break;
    case 'week':
        $interval = '1 WEEK';
        break;
    default:
        $interval = '1 HOUR';
}

// Prépare la requête SQL pour sélectionner les données du capteur
$sql = "SELECT $type, DATE_FORMAT(timestamp, '%H:%i %d/%m/%Y') as formatted_timestamp 
        FROM readings 
        WHERE device_id='$device_id' 
        AND timestamp > NOW() - INTERVAL $interval 
        ORDER BY timestamp ASC";

// Exécute la requête SQL
$result = $conn->query($sql);

// Initialise les tableaux pour les étiquettes et les valeurs
$labels = [];
$values = [];

// Si des résultats sont trouvés, les ajoute aux tableaux
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $labels[] = $row['formatted_timestamp'];
        $values[] = $row[$type];
    }
}

// Prépare les données en format JSON
$data = array(
    'labels' => $labels,
    'values' => $values
);

// Définit le type de contenu de la réponse HTTP comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Encode les données en JSON et les renvoie
echo json_encode($data);

// Ferme la connexion à la base de données
$conn->close();
?>
