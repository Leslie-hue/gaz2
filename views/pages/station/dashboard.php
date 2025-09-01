<section class="station-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Tableau de Bord Station</h1>
            <p>Gérez votre activité de vente de gaz</p>
        </div>
        
        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <h3>Stock Actuel</h3>
                    <p class="stat-number"><?php echo $stats['stock_gaz']; ?></p>
                    <span class="stat-label">bouteilles disponibles</span>
                </div>
                <a href="/gestion-stock" class="stat-action">Gérer</a>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <h3>Commandes en Attente</h3>
                    <p class="stat-number"><?php echo $stats['commandes_attente']; ?></p>
                    <span class="stat-label">à traiter</span>
                </div>
                <a href="/commandes-recues" class="stat-action">Voir</a>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3>Prix Unitaire</h3>
                    <p class="stat-number"><?php echo number_format($stats['prix_unite'], 0, ',', ' '); ?></p>
                    <span class="stat-label">FCFA par bouteille</span>
                </div>
                <button onclick="showPriceModal()" class="stat-action">Modifier</button>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <h3>Revenus Aujourd'hui</h3>
                    <p class="stat-number"><?php echo number_format($stats['revenus_jour'] ?? 0, 0, ',', ' '); ?></p>
                    <span class="stat-label">FCFA</span>
                </div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="quick-actions">
            <h2>Actions rapides</h2>
            <div class="actions-grid">
                <button onclick="showStockModal()" class="action-btn">
                    <div class="action-icon">📦</div>
                    <span>Mettre à jour le stock</span>
                </button>
                <button onclick="showPriceModal()" class="action-btn">
                    <div class="action-icon">💰</div>
                    <span>Modifier les prix</span>
                </button>
                <a href="/commandes-recues" class="action-btn">
                    <div class="action-icon">📋</div>
                    <span>Gérer les commandes</span>
                </a>
            </div>
        </div>
        
        <!-- Commandes récentes -->
        <?php if (!empty($recent_orders)): ?>
        <div class="dashboard-card">
            <div class="card-header">
                <h2>📦 Commandes récentes</h2>
                <a href="/commandes-recues" class="btn btn-outline btn-small">Voir toutes</a>
            </div>
            <div class="card-content">
                <div class="orders-list">
                    <?php foreach ($recent_orders as $order): ?>
                        <div class="order-item">
                            <div class="order-info">
                                <h4>Commande #<?php echo $order['id']; ?></h4>
                                <p><?php echo htmlspecialchars($order['client_nom']); ?></p>
                                <span class="order-date"><?php echo $this->formatDate($order['created_at']); ?></span>
                            </div>
                            <div class="order-details">
                                <span class="order-quantity"><?php echo $order['quantite']; ?> bouteille(s)</span>
                                <span class="order-price"><?php echo $this->formatPrice($order['prix_total']); ?></span>
                                <span class="order-status status-<?php echo $order['statut']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modals -->
<div id="stockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Mettre à jour le stock</h2>
            <button class="modal-close" onclick="closeModal('stockModal')">&times;</button>
        </div>
        <form id="stockForm" class="modal-form">
            <div class="form-group">
                <label for="nouveau_stock">Nouveau stock (bouteilles)</label>
                <input type="number" id="nouveau_stock" name="nouveau_stock" min="0" 
                       class="form-control" value="<?php echo $stats['stock_gaz']; ?>">
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('stockModal')" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<div id="priceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Modifier le prix</h2>
            <button class="modal-close" onclick="closeModal('priceModal')">&times;</button>
        </div>
        <form id="priceForm" class="modal-form">
            <div class="form-group">
                <label for="nouveau_prix">Prix par bouteille (FCFA)</label>
                <input type="number" id="nouveau_prix" name="nouveau_prix" min="500" 
                       class="form-control" value="<?php echo $stats['prix_unite']; ?>">
                <small>Prix minimum: 500 FCFA</small>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeModal('priceModal')" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>