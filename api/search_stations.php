<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$lat = $_GET['lat'] ?? '';
$lng = $_GET['lng'] ?? '';
$quantite = $_GET['quantite'] ?? 1;

if (empty($lat) || empty($lng)) {
    echo json_encode(['success' => false, 'message' => 'Coordonnées manquantes']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Rechercher les stations avec stock suffisant dans un rayon de 50km
    $stmt = $pdo->prepare("
        SELECT *, 
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance_km
        FROM stations 
        WHERE stock_gaz >= ?
        HAVING distance_km <= 50
        ORDER BY distance_km ASC
        LIMIT 10
    ");
    
    $stmt->execute([$lat, $lng, $lat, $quantite]);
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir les distances en float
    foreach ($stations as &$station) {
        $station['distance_km'] = floatval($station['distance_km']);
        $station['latitude'] = floatval($station['latitude']);
        $station['longitude'] = floatval($station['longitude']);
        $station['prix_unite'] = floatval($station['prix_unite']);
        $station['stock_gaz'] = intval($station['stock_gaz']);
    }
    
    echo json_encode(['success' => true, 'stations' => $stations]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la recherche: ' . $e->getMessage()]);
}
?>