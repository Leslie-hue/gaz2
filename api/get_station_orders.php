<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'station') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.nom as client_nom, u.telephone as client_telephone 
        FROM commandes c 
        JOIN utilisateurs u ON c.utilisateur_id = u.id 
        WHERE c.station_id = ? 
        ORDER BY c.created_at DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir les types
    foreach ($orders as &$order) {
        $order['distance_km'] = floatval($order['distance_km']);
        $order['prix_total'] = floatval($order['prix_total']);
        $order['quantite'] = intval($order['quantite']);
    }
    
    echo json_encode(['success' => true, 'orders' => $orders]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement: ' . $e->getMessage()]);
}
?>