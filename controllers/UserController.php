<?php
require_once 'BaseController.php';
require_once 'models/User.php';
require_once 'models/Station.php';
require_once 'models/Order.php';

class UserController extends BaseController {
    
    public function dashboard() {
        $this->requireAuth('utilisateur');
        
        $userModel = new User();
        $orders = $userModel->getOrders($_SESSION['user_id']);
        
        $data = [
            'title' => 'Mon Espace - ' . APP_NAME,
            'page' => 'user-dashboard',
            'orders' => $orders,
            'user' => $_SESSION
        ];
        
        $this->view('user/dashboard', $data);
    }
    
    public function showOrder() {
        $this->requireAuth('utilisateur');
        
        $data = [
            'title' => 'Commander du Gaz - ' . APP_NAME,
            'page' => 'order',
            'user' => $_SESSION
        ];
        
        $this->view('user/order', $data);
    }
    
    public function searchStations() {
        $this->requireAuth('utilisateur');
        
        $lat = $_POST['lat'] ?? '';
        $lng = $_POST['lng'] ?? '';
        $quantite = $_POST['quantite'] ?? 1;
        
        if (empty($lat) || empty($lng)) {
            $this->json(['success' => false, 'message' => 'Coordonnées manquantes'], 400);
        }
        
        try {
            $stationModel = new Station();
            $stations = $stationModel->searchNearby($lat, $lng, $quantite);
            
            $this->json(['success' => true, 'stations' => $stations]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function createOrder() {
        $this->requireAuth('utilisateur');
        
        try {
            $orderData = [
                'utilisateur_id' => $_SESSION['user_id'],
                'station_id' => $_POST['station_id'],
                'quantite' => $_POST['quantite'],
                'adresse_livraison' => $_POST['adresse_livraison'],
                'latitude_livraison' => $_POST['latitude_livraison'],
                'longitude_livraison' => $_POST['longitude_livraison']
            ];
            
            $orderModel = new Order();
            $result = $orderModel->createOrder($orderData);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Commande passée avec succès!']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de la création de la commande'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function myOrders() {
        $this->requireAuth('utilisateur');
        
        $userModel = new User();
        $orders = $userModel->getOrders($_SESSION['user_id']);
        
        $data = [
            'title' => 'Mes Commandes - ' . APP_NAME,
            'page' => 'my-orders',
            'orders' => $orders,
            'user' => $_SESSION
        ];
        
        $this->view('user/orders', $data);
    }
}
?>