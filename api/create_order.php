<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'utilisateur') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$station_id = $_POST['station_id'] ?? '';
$quantite = $_POST['quantite'] ?? '';
$adresse_livraison = $_POST['adresse_livraison'] ?? '';
$latitude_livraison = $_POST['latitude_livraison'] ?? '';
$longitude_livraison = $_POST['longitude_livraison'] ?? '';

if (empty($station_id) || empty($quantite) || empty($adresse_livraison)) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Commencer une transaction
    $pdo->beginTransaction();
    
    // Vérifier le stock disponible
    $stmt = $pdo->prepare("SELECT stock_gaz, prix_unite, latitude, longitude FROM stations WHERE id = ?");
    $stmt->execute([$station_id]);
    $station = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$station) {
        $pdo->rollback();
        echo json_encode(['success' => false, 'message' => 'Station non trouvée']);
        exit;
    }
    
    if ($station['stock_gaz'] < $quantite) {
        $pdo->rollback();
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        exit;
    }
    
    // Calculer la distance
    $station_lat = floatval($station['latitude']);
    $station_lng = floatval($station['longitude']);
    $user_lat = floatval($latitude_livraison);
    $user_lng = floatval($longitude_livraison);
    
    $distance = calculateDistance($station_lat, $station_lng, $user_lat, $user_lng);
    
    // Calculer le prix total
    $prix_total = $station['prix_unite'] * $quantite;
    
    // Créer la commande
    $stmt = $pdo->prepare("
        INSERT INTO commandes (utilisateur_id, station_id, quantite, prix_total, adresse_livraison, latitude_livraison, longitude_livraison, distance_km) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['user_id'], 
        $station_id, 
        $quantite, 
        $prix_total, 
        $adresse_livraison, 
        $latitude_livraison, 
        $longitude_livraison,
        $distance
    ]);
    
    // Mettre à jour le stock
    $stmt = $pdo->prepare("UPDATE stations SET stock_gaz = stock_gaz - ? WHERE id = ?");
    $stmt->execute([$quantite, $station_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Commande créée avec succès']);
    
} catch (Exception $e) {
    $pdo->rollback();
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la création de la commande: ' . $e->getMessage()]);
}

function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earth_radius * $c;
}
?>