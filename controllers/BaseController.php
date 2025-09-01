<?php
require_once 'config/app.php';

abstract class BaseController {
    protected function view($viewName, $data = []) {
        extract($data);
        
        // Démarrer la capture de sortie
        ob_start();
        
        // Inclure la vue
        $viewPath = "views/pages/{$viewName}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("Vue non trouvée: {$viewName}");
        }
        
        // Récupérer le contenu
        $content = ob_get_clean();
        
        // Inclure le layout
        include 'views/layouts/main.php';
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    protected function requireAuth($userType = null) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        if ($userType && $_SESSION['user_type'] !== $userType) {
            $this->redirect('/');
        }
    }
    
    protected function formatPrice($amount) {
        return number_format($amount, 0, ',', ' ') . ' ' . CURRENCY;
    }
    
    protected function formatDate($date) {
        return date('d/m/Y à H:i', strtotime($date));
    }
}
?>