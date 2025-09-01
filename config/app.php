<?php
// Configuration principale de l'application
define('APP_NAME', 'GazCameroun');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:8080');
define('CURRENCY', 'FCFA');
define('DEFAULT_CITY', 'Yaoundé');
define('DEFAULT_LAT', 3.8480);
define('DEFAULT_LNG', 11.5021);

// Configuration des prix
define('DELIVERY_RADIUS_KM', 25); // Rayon de livraison en km
define('MIN_ORDER_QUANTITY', 1);
define('MAX_ORDER_QUANTITY', 10);

// Configuration de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('BCRYPT_COST', 12);

// Fuseau horaire
date_default_timezone_set('Africa/Douala');

// Démarrage de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>