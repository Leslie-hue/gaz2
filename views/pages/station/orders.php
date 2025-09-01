<section class="station-orders">
    <div class="container">
        <div class="orders-header">
            <h1>Commandes Reçues</h1>
            <p>Gérez les commandes de vos clients</p>
        </div>
        
        <!-- Filtres -->
        <div class="orders-filters">
            <button class="filter-btn active" onclick="filterOrders('all')">Toutes</button>
            <button class="filter-btn" onclick="filterOrders('en_attente')">En attente</button>
            <button class="filter-btn" onclick="filterOrders('confirmee')">Confirmées</button>
            <button class="filter-btn" onclick="filterOrders('en_livraison')">En livraison</button>
            <button class="filter-btn" onclick="filterOrders('livree')">Livrées</button>
        </div>
        
        <?php if (!empty($orders)): ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card" data-status="<?php echo $order['statut']; ?>">
                        <div class="order-card-header">
                            <div class="order-info">
                                <h3>Commande #<?php echo $order['id']; ?></h3>
                                <span class="order-date"><?php echo $this->formatDate($order['created_at']); ?></span>
                            </div>
                            <span class="order-status status-<?php echo $order['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                            </span>
                        </div>
                        
                        <div class="order-content">
                            <div class="client-info">
                                <h4>👤 Informations client</h4>
                                <div class="client-details">
                                    <p><strong>Nom:</strong> <?php echo htmlspecialchars($order['client_nom']); ?></p>
                                    <p><strong>Téléphone:</strong> 
                                        <a href="tel:<?php echo $order['client_telephone']; ?>" class="phone-link">
                                            <?php echo $order['client_telephone']; ?>
                                        </a>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="order-info-grid">
                                <div class="info-item">
                                    <span class="info-label">Quantité</span>
                                    <span class="info-value"><?php echo $order['quantite']; ?> bouteille(s)</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Prix total</span>
                                    <span class="info-value"><?php echo $this->formatPrice($order['prix_total']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Distance</span>
                                    <span class="info-value"><?php echo number_format($order['distance_km'], 1); ?> km</span>
                                </div>
                            </div>
                            
                            <div class="delivery-info">
                                <h4>📍 Adresse de livraison</h4>
                                <p><?php echo htmlspecialchars($order['adresse_livraison']); ?></p>
                                <button onclick="showOnMap(<?php echo $order['latitude_livraison']; ?>, <?php echo $order['longitude_livraison']; ?>)" 
                                        class="btn btn-outline btn-small">
                                    🗺️ Voir sur la carte
                                </button>
                            </div>
                        </div>
                        
                        <div class="order-actions">
                            <?php if ($order['statut'] === 'en_attente'): ?>
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmee')" 
                                        class="btn btn-success">
                                    ✅ Confirmer
                                </button>
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'annulee')" 
                                        class="btn btn-error">
                                    ❌ Refuser
                                </button>
                            <?php elseif ($order['statut'] === 'confirmee'): ?>
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'en_livraison')" 
                                        class="btn btn-primary">
                                    🚚 Marquer en livraison
                                </button>
                            <?php elseif ($order['statut'] === 'en_livraison'): ?>
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'livree')" 
                                        class="btn btn-success">
                                    ✅ Marquer comme livré
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2>Aucune commande</h2>
                <p>Vous n'avez pas encore reçu de commande.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal carte -->
<div id="mapModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Localisation de livraison</h2>
            <button class="modal-close" onclick="closeModal('mapModal')">&times;</button>
        </div>
        <div id="delivery-map" class="delivery-map"></div>
    </div>
</div>