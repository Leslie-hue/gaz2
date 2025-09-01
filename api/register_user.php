<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($nom) || empty($email) || empty($telephone) || empty($adresse) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        exit;
    }
    
    // Géocoder l'adresse avec cURL et User-Agent
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($adresse));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "GasDeliveryApp/1.0 (contact@example.com)");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $geoData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        echo json_encode(['success' => false, 'message' => 'Erreur de géocodage: HTTP ' . $httpCode]);
        exit;
    }
    
    $geoResult = json_decode($geoData, true);
    
    $latitude = null;
    $longitude = null;
    if (!empty($geoResult)) {
        $latitude = floatval($geoResult[0]['lat']);
        $longitude = floatval($geoResult[0]['lon']);
    }
    
    // Hash du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insérer l'utilisateur
    $stmt = $pdo->prepare("
        INSERT INTO utilisateurs (nom, email, telephone, adresse, latitude, longitude, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$nom, $email, $telephone, $adresse, $latitude, $longitude, $hashedPassword]);
    
    echo json_encode(['success' => true, 'message' => 'Inscription réussie']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()]);
}
?>