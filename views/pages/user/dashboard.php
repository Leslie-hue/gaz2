<section class="dashboard-section">
    <div class="container">
        <div class="dashboard-header">
            <h1>Mon Espace</h1>
            <p>Gérez vos commandes de gaz domestique</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- Actions rapides -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>🔥 Commander du gaz</h2>
                </div>
                <div class="card-content">
                    <p>Trouvez les stations les plus proches et commandez votre gaz domestique.</p>
                    <a href="/commander" class="btn btn-primary">
                        Nouvelle commande
                    </a>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>📊 Mes statistiques</h2>
                </div>
                <div class="card-content">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($orders); ?></span>
                            <span class="stat-label">Commandes totales</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">
                                <?php 
                                $pending = array_filter($orders, fn($o) => $o['statut'] === 'en_attente');
                                echo count($pending);
                                ?>
                            </span>
                            <span class="stat-label">En attente</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Commandes récentes -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>📦 Commandes récentes</h2>
                <a href="/mes-commandes" class="btn btn-outline btn-small">Voir tout</a>
            </div>
            <div class="card-content">
                <?php if (!empty($orders)): ?>
                    <div class="orders-list">
                        <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                            <div class="order-item">
                                <div class="order-info">
                                    <h4>Commande #<?php echo $order['id']; ?></h4>
                                    <p><?php echo $order['station_nom']; ?></p>
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
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h3>Aucune commande</h3>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="/commander" class="btn btn-primary">Première commande</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>