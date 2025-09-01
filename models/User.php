<?php
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'utilisateurs';
    
    public function authenticate($email, $password) {
        $user = $this->findBy('email', $email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function register($data) {
        // Validation des données
        if (!$this->validateUserData($data)) {
            return false;
        }
        
        // Vérifier si l'email existe déjà
        if ($this->findBy('email', $data['email'])) {
            throw new Exception('Cet email est déjà utilisé');
        }
        
        // Géocoder l'adresse
        $coordinates = $this->geocodeAddress($data['adresse']);
        
        // Préparer les données
        $userData = [
            'nom' => $data['nom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'],
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($userData);
    }
    
    public function getOrders($userId) {
        $stmt = $this->db->prepare("
            SELECT c.*, s.nom as station_nom, s.telephone as station_telephone
            FROM commandes c 
            JOIN stations s ON c.station_id = s.id 
            WHERE c.utilisateur_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function validateUserData($data) {
        $required = ['nom', 'email', 'telephone', 'adresse', 'password'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Le champ {$field} est requis");
            }
        }
        
        // Validation email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalide');
        }
        
        // Validation téléphone camerounais
        if (!preg_match('/^(6[5-9]|2[2-3])[0-9]{7}$/', $data['telephone'])) {
            throw new Exception('Numéro de téléphone camerounais invalide (ex: 655123456)');
        }
        
        return true;
    }
    
    private function geocodeAddress($address) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address . ", Cameroun"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "GazCameroun/1.0 (contact@gazcameroun.cm)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $geoData = curl_exec($ch);
        curl_close($ch);
        
        $geoResult = json_decode($geoData, true);
        
        if (!empty($geoResult)) {
            return [
                'lat' => floatval($geoResult[0]['lat']),
                'lng' => floatval($geoResult[0]['lon'])
            ];
        }
        
        // Coordonnées par défaut (Yaoundé)
        return ['lat' => DEFAULT_LAT, 'lng' => DEFAULT_LNG];
    }
}
?>