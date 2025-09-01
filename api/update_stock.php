<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'station') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$nouveau_stock = $_POST['nouveau_stock'] ?? '';

if (empty($nouveau_stock) || !is_numeric($nouveau_stock)) {
    echo json_encode(['success' => false, 'message' => 'Stock invalide']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("UPDATE stations SET stock_gaz = ? WHERE id = ?");
    $stmt->execute([$nouveau_stock, $_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Stock mis à jour']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?>