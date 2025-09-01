<?php
class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $dbPath = __DIR__ . '/../database/gas_delivery.db';
            $this->pdo = new PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    private function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS stations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            adresse TEXT NOT NULL,
            latitude REAL NOT NULL,
            longitude REAL NOT NULL,
            stock_gaz INTEGER DEFAULT 0,
            prix_unite DECIMAL(10,2) DEFAULT 15.00,
            active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            adresse TEXT NOT NULL,
            latitude REAL,
            longitude REAL,
            active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS commandes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            station_id INTEGER NOT NULL,
            quantite INTEGER NOT NULL,
            prix_bouteilles DECIMAL(10,2) NOT NULL,
            frais_livraison DECIMAL(10,2) DEFAULT 0,
            prix_total DECIMAL(10,2) NOT NULL,
            statut VARCHAR(50) DEFAULT 'en_attente',
            adresse_livraison TEXT NOT NULL,
            latitude_livraison REAL NOT NULL,
            longitude_livraison REAL NOT NULL,
            distance_km REAL NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
            FOREIGN KEY (station_id) REFERENCES stations(id)
        );
        ";
        
        $this->pdo->exec($sql);
    }
}
?>