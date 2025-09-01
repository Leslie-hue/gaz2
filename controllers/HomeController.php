<?php
require_once 'BaseController.php';

class HomeController extends BaseController {
    public function index() {
        // Si l'utilisateur est connecté, rediriger vers son dashboard
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['user_type'] === 'station') {
                $this->redirect('/station-dashboard');
            } else {
                $this->redirect('/dashboard');
            }
        }
        
        $data = [
            'title' => 'Accueil - ' . APP_NAME,
            'page' => 'home'
        ];
        
        $this->view('home', $data);
    }
}
?>