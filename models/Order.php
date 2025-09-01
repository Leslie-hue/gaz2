<?php
require_once 'BaseModel.php';

class Order extends BaseModel {
    protected $table = 'commandes';
    
    public function createOrder($data) {
        // Validation des données
        if (!$this->validateOrderData($data)) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Vérifier le stock disponible
            $stmt = $this->db->prepare("SELECT stock_gaz, prix_unite, latitude, longitude, nom FROM stations WHERE id = ? AND active = 1");
            $stmt->execute([$data['station_id']]);
            $station = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$station) {
                throw new Exception('Station non trouvée ou inactive');
            }
            
            if ($station['stock_gaz'] < $data['quantite']) {
                throw new Exception('Stock insuffisant à la station');
            }
            
            // Calculer la distance
            $distance = $this->calculateDistance(
                $station['latitude'], 
                $station['longitude'],
                $data['latitude_livraison'], 
                $data['longitude_livraison']
            );
            
            if ($distance > DELIVERY_RADIUS_KM) {
                throw new Exception('Adresse de livraison trop éloignée (max ' . DELIVERY_RADIUS_KM . ' km)');
            }
            
            // Calculer le prix total avec frais de livraison
            $prix_bouteilles = $station['prix_unite'] * $data['quantite'];
            $frais_livraison = $this->calculateDeliveryFee($distance);
            $prix_total = $prix_bouteilles + $frais_livraison;
            
            // Créer la commande
            $orderData = [
                'utilisateur_id' => $data['utilisateur_id'],
                'station_id' => $data['station_id'],
                'quantite' => $data['quantite'],
                'prix_bouteilles' => $prix_bouteilles,
                'frais_livraison' => $frais_livraison,
                'prix_total' => $prix_total,
                'adresse_livraison' => $data['adresse_livraison'],
                'latitude_livraison' => $data['latitude_livraison'],
                'longitude_livraison' => $data['longitude_livraison'],
                'distance_km' => $distance,
                'statut' => 'en_attente',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->create($orderData);
            
            // Mettre à jour le stock
            $stmt = $this->db->prepare("UPDATE stations SET stock_gaz = stock_gaz - ? WHERE id = ?");
            $stmt->execute([$data['quantite'], $data['station_id']]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function updateStatus($orderId, $newStatus, $stationId) {
        $allowedStatuses = ['confirmee', 'en_livraison', 'livree', 'annulee'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new Exception('Statut invalide');
        }
        
        // Vérifier que la commande appartient à cette station
        $stmt = $this->db->prepare("SELECT * FROM commandes WHERE id = ? AND station_id = ?");
        $stmt->execute([$orderId, $stationId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            throw new Exception('Commande non trouvée');
        }
        
        // Si annulée, remettre le stock
        if ($newStatus === 'annulee' && $order['statut'] !== 'annulee') {
            $stmt = $this->db->prepare("UPDATE stations SET stock_gaz = stock_gaz + ? WHERE id = ?");
            $stmt->execute([$order['quantite'], $stationId]);
        }
        
        // Mettre à jour le statut
        return $this->update($orderId, ['statut' => $newStatus]);
    }
    
    private function validateOrderData($data) {
        $required = ['utilisateur_id', 'station_id', 'quantite', 'adresse_livraison', 'latitude_livraison', 'longitude_livraison'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Données de commande incomplètes: {$field}");
            }
        }
        
        if ($data['quantite'] < MIN_ORDER_QUANTITY || $data['quantite'] > MAX_ORDER_QUANTITY) {
            throw new Exception('Quantité invalide (entre ' . MIN_ORDER_QUANTITY . ' et ' . MAX_ORDER_QUANTITY . ' bouteilles)');
        }
        
        return true;
    }
    
    private function calculateDistance($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earth_radius * $c;
    }
    
    private function calculateDeliveryFee($distance) {
        // Frais de livraison basés sur la distance
        if ($distance <= 5) return 500; // 500 FCFA pour moins de 5km
        if ($distance <= 10) return 1000; // 1000 FCFA pour 5-10km
        if ($distance <= 20) return 1500; // 1500 FCFA pour 10-20km
        return 2000; // 2000 FCFA pour plus de 20km
    }
}
?>