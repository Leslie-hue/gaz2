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
    
    // Données de la station
    $stmt = $pdo->prepare("SELECT stock_gaz, prix_unite FROM stations WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $station = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Commandes en attente
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM commandes WHERE station_id = ? AND statut = 'en_attente'");
    $stmt->execute([$_SESSION['user_id']]);
    $pending = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $data = [
        'stock_gaz' => $station['stock_gaz'],
        'prix_unite' => $station['prix_unite'],
        'pending_orders' => $pending['count']
    ];
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement: ' . $e->getMessage()]);
}
?>