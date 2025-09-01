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

$order_id = $_POST['order_id'] ?? '';
$status = $_POST['status'] ?? '';

$allowed_statuses = ['confirmee', 'en_livraison', 'livree', 'annulee'];

if (empty($order_id) || !in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Vérifier que la commande appartient à cette station
    $stmt = $pdo->prepare("SELECT id, statut, quantite FROM commandes WHERE id = ? AND station_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
        exit;
    }
    
    // Si annulée, remettre le stock
    if ($status === 'annulee' && $order['statut'] !== 'annulee') {
        $stmt = $pdo->prepare("UPDATE stations SET stock_gaz = stock_gaz + ? WHERE id = ?");
        $stmt->execute([$order['quantite'], $_SESSION['user_id']]);
    }
    
    // Mettre à jour le statut
    $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
}
?>