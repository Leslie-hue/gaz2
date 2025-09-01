<?php
require_once 'BaseController.php';
require_once 'models/Station.php';
require_once 'models/Order.php';

class StationController extends BaseController {
    
    public function dashboard() {
        $this->requireAuth('station');
        
        $stationModel = new Station();
        $stats = $stationModel->getStats($_SESSION['user_id']);
        $recentOrders = $stationModel->getOrders($_SESSION['user_id']);
        
        // Limiter aux 5 dernières commandes pour le dashboard
        $recentOrders = array_slice($recentOrders, 0, 5);
        
        $data = [
            'title' => 'Tableau de Bord - ' . APP_NAME,
            'page' => 'station-dashboard',
            'stats' => $stats,
            'recent_orders' => $recentOrders,
            'station' => $_SESSION
        ];
        
        $this->view('station/dashboard', $data);
    }
    
    public function stockManagement() {
        $this->requireAuth('station');
        
        $stationModel = new Station();
        $station = $stationModel->find($_SESSION['user_id']);
        
        $data = [
            'title' => 'Gestion du Stock - ' . APP_NAME,
            'page' => 'stock-management',
            'station_data' => $station
        ];
        
        $this->view('station/stock', $data);
    }
    
    public function updateStock() {
        $this->requireAuth('station');
        
        $nouveauStock = $_POST['nouveau_stock'] ?? '';
        
        if (!is_numeric($nouveauStock) || $nouveauStock < 0) {
            $this->json(['success' => false, 'message' => 'Stock invalide'], 400);
        }
        
        try {
            $stationModel = new Station();
            $result = $stationModel->update($_SESSION['user_id'], ['stock_gaz' => $nouveauStock]);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Stock mis à jour avec succès']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updatePrice() {
        $this->requireAuth('station');
        
        $nouveauPrix = $_POST['nouveau_prix'] ?? '';
        
        if (!is_numeric($nouveauPrix) || $nouveauPrix < 500) {
            $this->json(['success' => false, 'message' => 'Prix invalide (minimum 500 FCFA)'], 400);
        }
        
        try {
            $stationModel = new Station();
            $result = $stationModel->update($_SESSION['user_id'], ['prix_unite' => $nouveauPrix]);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Prix mis à jour avec succès']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function receivedOrders() {
        $this->requireAuth('station');
        
        $stationModel = new Station();
        $orders = $stationModel->getOrders($_SESSION['user_id']);
        
        $data = [
            'title' => 'Commandes Reçues - ' . APP_NAME,
            'page' => 'received-orders',
            'orders' => $orders,
            'station' => $_SESSION
        ];
        
        $this->view('station/orders', $data);
    }
    
    public function updateOrderStatus() {
        $this->requireAuth('station');
        
        $orderId = $_POST['order_id'] ?? '';
        $status = $_POST['status'] ?? '';
        
        try {
            $orderModel = new Order();
            $result = $orderModel->updateStatus($orderId, $status, $_SESSION['user_id']);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Statut mis à jour avec succès']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
?>