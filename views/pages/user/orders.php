<section class="orders-section">
    <div class="container">
        <div class="orders-header">
            <h1>Mes Commandes</h1>
            <p>Suivez l'état de vos commandes de gaz</p>
        </div>
        
        <?php if (!empty($orders)): ?>
            <div class="orders-grid">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-card-header">
                            <div class="order-number">
                                <h3>Commande #<?php echo $order['id']; ?></h3>
                                <span class="order-date"><?php echo $this->formatDate($order['created_at']); ?></span>
                            </div>
                            <span class="order-status status-<?php echo $order['statut']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                            </span>
                        </div>
                        
                        <div class="order-details">
                            <div class="detail-row">
                                <span class="detail-label">Station:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['station_nom']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Téléphone station:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['station_telephone']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Quantité:</span>
                                <span class="detail-value"><?php echo $order['quantite']; ?> bouteille(s)</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Prix bouteilles:</span>
                                <span class="detail-value"><?php echo $this->formatPrice($order['prix_bouteilles'] ?? $order['prix_total']); ?></span>
                            </div>
                            <?php if (isset($order['frais_livraison'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Frais de livraison:</span>
                                <span class="detail-value"><?php echo $this->formatPrice($order['frais_livraison']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row total">
                                <span class="detail-label">Total:</span>
                                <span class="detail-value"><?php echo $this->formatPrice($order['prix_total']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Adresse de livraison:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($order['adresse_livraison']); ?></span>
                            </div>
                            <?php if (isset($order['distance_km'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Distance:</span>
                                <span class="detail-value"><?php echo number_format($order['distance_km'], 1); ?> km</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($order['statut'] === 'en_attente'): ?>
                            <div class="order-actions">
                                <p class="order-note">⏳ Votre commande est en attente de confirmation par la station</p>
                            </div>
                        <?php elseif ($order['statut'] === 'confirmee'): ?>
                            <div class="order-actions">
                                <p class="order-note">✅ Commande confirmée - Préparation en cours</p>
                            </div>
                        <?php elseif ($order['statut'] === 'en_livraison'): ?>
                            <div class="order-actions">
                                <p class="order-note">🚚 Votre gaz est en cours de livraison</p>
                            </div>
                        <?php elseif ($order['statut'] === 'livree'): ?>
                            <div class="order-actions">
                                <p class="order-note">✅ Commande livrée avec succès</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2>Aucune commande</h2>
                <p>Vous n'avez pas encore passé de commande de gaz.</p>
                <a href="/commander" class="btn btn-primary">Passer ma première commande</a>
            </div>
        <?php endif; ?>
    </div>
</section>