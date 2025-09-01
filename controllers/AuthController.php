<?php
require_once 'BaseController.php';
require_once 'models/User.php';
require_once 'models/Station.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $data = [
            'title' => 'Connexion - ' . APP_NAME,
            'page' => 'login'
        ];
        
        $this->view('auth/login', $data);
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $userType = $_POST['user_type'] ?? '';
        
        if (empty($email) || empty($password) || empty($userType)) {
            $this->json(['success' => false, 'message' => 'Tous les champs sont requis'], 400);
        }
        
        try {
            if ($userType === 'station') {
                $model = new Station();
                $user = $model->authenticate($email, $password);
            } else {
                $model = new User();
                $user = $model->authenticate($email, $password);
            }
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $userType;
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['login_time'] = time();
                
                $redirectUrl = ($userType === 'station') ? '/station-dashboard' : '/dashboard';
                $this->json(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                $this->json(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function showRegister() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        $data = [
            'title' => 'Inscription - ' . APP_NAME,
            'page' => 'register'
        ];
        
        $this->view('auth/register', $data);
    }
    
    public function registerUser() {
        try {
            $model = new User();
            $result = $model->register($_POST);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Inscription réussie! Vous pouvez maintenant vous connecter.']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de l\'inscription'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function registerStation() {
        try {
            $model = new Station();
            $result = $model->register($_POST);
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Station enregistrée avec succès!']);
            } else {
                $this->json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/');
    }
}
?>