<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GazLivraison - Commande de Gaz à Domicile</title>
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/png" href="icons/icon-192x192.png">
    <meta name="theme-color" content="#2563EB">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <div id="app">
        <header class="header">
            <div class="container">
                <h1 class="logo">🔥 GazLivraison</h1>
                <nav class="nav">
                    <?php if (isset($_SESSION['user_type'])): ?>
                        <span>Bienvenue, <?php echo $_SESSION['nom']; ?></span>
                        <a href="api/logout.php" class="btn btn-secondary">Déconnexion</a>
                    <?php else: ?>
                        <button onclick="showLogin()" class="btn btn-primary">Connexion</button>
                        <button onclick="showRegister()" class="btn btn-secondary">Inscription</button>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <main class="main">
            <?php if (!isset($_SESSION['user_type'])): ?>
                <!-- Page d'accueil -->
                <section class="hero">
                    <div class="container">
                        <h2>Commandez votre gaz de cuisine en ligne</h2>
                        <p>Service de livraison rapide et fiable dans votre région</p>
                        <div class="hero-actions">
                            <button onclick="showRegisterUser()" class="btn btn-primary btn-large">
                                👤 Je suis un particulier
                            </button>
                            <button onclick="showRegisterStation()" class="btn btn-secondary btn-large">
                                🏪 Je suis une station de service
                            </button>
                        </div>
                    </div>
                </section>
            <?php elseif ($_SESSION['user_type'] === 'utilisateur'): ?>
                <!-- Dashboard utilisateur -->
                <section class="dashboard">
                    <div class="container">
                        <h2>Commander du gaz</h2>
                        <div class="order-form">
                            <div class="form-group">
                                <label>Quantité de bouteilles</label>
                                <select id="quantite" class="form-control">
                                    <option value="1">1 bouteille</option>
                                    <option value="2">2 bouteilles</option>
                                    <option value="3">3 bouteilles</option>
                                    <option value="4">4 bouteilles</option>
                                    <option value="5">5 bouteilles</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Adresse de livraison</label>
                                <input type="text" id="adresse_livraison" class="form-control" placeholder="Saisissez votre adresse">
                                <button onclick="getCurrentLocation()" class="btn btn-small">📍 Ma position actuelle</button>
                            </div>
                            <button onclick="searchStations()" class="btn btn-primary">Rechercher des stations</button>
                        </div>
                        
                        <div id="map" style="height: 400px; margin: 20px 0; display: none;"></div>
                        <div id="stations-list"></div>
                        
                        <div id="mes-commandes">
                            <h3>Mes commandes</h3>
                            <div id="commandes-container"></div>
                        </div>
                    </div>
                </section>
            <?php else: ?>
                <!-- Dashboard station -->
                <section class="dashboard">
                    <div class="container">
                        <h2>Gestion de station</h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <h3>Stock actuel</h3>
                                <p id="stock-count">-</p>
                                <button onclick="showStockModal()" class="btn btn-small">Mettre à jour</button>
                            </div>
                            <div class="stat-card">
                                <h3>Commandes en attente</h3>
                                <p id="pending-orders">-</p>
                            </div>
                            <div class="stat-card">
                                <h3>Prix par bouteille</h3>
                                <p id="price-per-unit">-</p>
                                <button onclick="showPriceModal()" class="btn btn-small">Modifier</button>
                            </div>
                        </div>
                        
                        <div id="commandes-recues">
                            <h3>Commandes reçues</h3>
                            <div id="station-orders-container"></div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modals -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h2>Connexion</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Type de compte</label>
                    <select name="user_type" required class="form-control">
                        <option value="utilisateur">Particulier</option>
                        <option value="station">Station de service</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
        </div>
    </div>

    <div id="registerUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('registerUserModal')">&times;</span>
            <h2>Inscription Particulier</h2>
            <form id="registerUserForm">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="nom" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
        </div>
    </div>

    <div id="registerStationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('registerStationModal')">&times;</span>
            <h2>Inscription Station de Service</h2>
            <form id="registerStationForm">
                <div class="form-group">
                    <label>Nom de la station</label>
                    <input type="text" name="nom" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Adresse complète</label>
                    <input type="text" name="adresse" required class="form-control">
                    <button type="button" onclick="getLocationForStation()" class="btn btn-small">📍 Localiser sur la carte</button>
                </div>
                <div class="form-group">
                    <label>Stock initial</label>
                    <input type="number" name="stock_gaz" min="0" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Prix par bouteille (€)</label>
                    <input type="number" name="prix_unite" step="0.01" min="0" required class="form-control" value="15.00">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
        </div>
    </div>

    <div id="stockModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('stockModal')">&times;</span>
            <h2>Mettre à jour le stock</h2>
            <form id="stockForm">
                <div class="form-group">
                    <label>Nouveau stock</label>
                    <input type="number" name="nouveau_stock" min="0" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>

    <div id="priceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('priceModal')">&times;</span>
            <h2>Modifier le prix</h2>
            <form id="priceForm">
                <div class="form-group">
                    <label>Prix par bouteille (€)</label>
                    <input type="number" name="nouveau_prix" step="0.01" min="0" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script src="js/map.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service-worker.js');
        }
    </script>
</body>
</html>
</create_file>
