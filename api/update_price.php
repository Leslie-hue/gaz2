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

$nouveau_prix = $_POST['nouveau_prix'] ?? '';

if (empty($nouveau_prix) || !is_numeric($nouveau_prix)) {
    echo json_encode(['success' => false, 'message' => 'Prix invalide']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->prepare("UPDATE stations SET prix_unite = ? WHERE id = ?");
    $stmt->execute([$nouveau_prix, $_SESSION['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Prix mis à jour']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?>