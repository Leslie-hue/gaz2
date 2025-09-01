<?php
class Router {
    private $routes = [];
    
    public function get($path, $controller, $method) {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function post($path, $controller, $method) {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';
        
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            $controllerName = $route['controller'];
            $methodName = $route['method'];
            
            require_once "controllers/{$controllerName}.php";
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            // Route par défaut
            require_once 'controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
        }
    }
}

// Définition des routes
$router = new Router();

// Routes publiques
$router->get('/', 'HomeController', 'index');
$router->get('/login', 'AuthController', 'showLogin');
$router->post('/login', 'AuthController', 'login');
$router->get('/register', 'AuthController', 'showRegister');
$router->post('/register-user', 'AuthController', 'registerUser');
$router->post('/register-station', 'AuthController', 'registerStation');
$router->get('/logout', 'AuthController', 'logout');

// Routes utilisateurs
$router->get('/dashboard', 'UserController', 'dashboard');
$router->get('/commander', 'UserController', 'showOrder');
$router->post('/search-stations', 'UserController', 'searchStations');
$router->post('/create-order', 'UserController', 'createOrder');
$router->get('/mes-commandes', 'UserController', 'myOrders');

// Routes stations
$router->get('/station-dashboard', 'StationController', 'dashboard');
$router->get('/gestion-stock', 'StationController', 'stockManagement');
$router->post('/update-stock', 'StationController', 'updateStock');
$router->post('/update-price', 'StationController', 'updatePrice');
$router->get('/commandes-recues', 'StationController', 'receivedOrders');
$router->post('/update-order-status', 'StationController', 'updateOrderStatus');

return $router;
?>