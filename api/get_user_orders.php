<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'utilisateur') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("
        SELECT c.*, s.nom as station_nom 
        FROM commandes c 
        JOIN stations s ON c.station_id = s.id 
        WHERE c.utilisateur_id = ? 
        ORDER BY c.created_at DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'orders' => $orders]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du chargement: ' . $e->getMessage()]);
}
?>