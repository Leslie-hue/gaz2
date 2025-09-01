<section class="stock-management">
    <div class="container">
        <div class="stock-header">
            <h1>Gestion du Stock</h1>
            <p>Gérez votre inventaire de gaz domestique</p>
        </div>
        
        <div class="stock-overview">
            <div class="stock-card">
                <div class="stock-visual">
                    <div class="gas-bottles">
                        <?php 
                        $stock = $station_data['stock_gaz'];
                        $maxDisplay = 20;
                        $bottlesToShow = min($stock, $maxDisplay);
                        
                        for ($i = 0; $i < $bottlesToShow; $i++): ?>
                            <div class="gas-bottle"></div>
                        <?php endfor; ?>
                        
                        <?php if ($stock > $maxDisplay): ?>
                            <div class="stock-overflow">+<?php echo $stock - $maxDisplay; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="stock-info">
                    <h2>Stock Actuel</h2>
                    <p class="stock-number"><?php echo $stock; ?> bouteilles</p>
                    <p class="stock-weight"><?php echo $stock * 12.5; ?> kg de gaz total</p>
                    
                    <div class="stock-status">
                        <?php if ($stock < 10): ?>
                            <span class="status-badge status-low">⚠️ Stock faible</span>
                        <?php elseif ($stock < 30): ?>
                            <span class="status-badge status-medium">📊 Stock modéré</span>
                        <?php else: ?>
                            <span class="status-badge status-good">✅ Stock suffisant</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stock-actions">
            <div class="action-card">
                <h3>📦 Réapprovisionner</h3>
                <p>Ajoutez des bouteilles à votre stock</p>
                <form id="addStockForm" class="stock-form">
                    <div class="form-row">
                        <input type="number" id="add_quantity" min="1" placeholder="Quantité à ajouter" 
                               class="form-control" required>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
            
            <div class="action-card">
                <h3>🔄 Définir le stock</h3>
                <p>Définissez le stock total actuel</p>
                <form id="setStockForm" class="stock-form">
                    <div class="form-row">
                        <input type="number" id="set_quantity" min="0" placeholder="Stock total" 
                               class="form-control" value="<?php echo $stock; ?>" required>
                        <button type="submit" class="btn btn-primary">Définir</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="price-management">
            <div class="price-card">
                <h3>💰 Gestion des Prix</h3>
                <div class="current-price">
                    <span class="price-label">Prix actuel par bouteille:</span>
                    <span class="price-value"><?php echo $this->formatPrice($station_data['prix_unite']); ?></span>
                </div>
                
                <form id="updatePriceForm" class="price-form">
                    <div class="form-group">
                        <label for="nouveau_prix">Nouveau prix (FCFA)</label>
                        <input type="number" id="nouveau_prix" name="nouveau_prix" min="500" 
                               class="form-control" value="<?php echo $station_data['prix_unite']; ?>" required>
                        <small>Prix minimum: 500 FCFA</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le prix</button>
                </form>
            </div>
        </div>
        
        <!-- Historique des mouvements de stock -->
        <div class="stock-history">
            <h3>📊 Activité récente</h3>
            <div class="history-list">
                <div class="history-item">
                    <span class="history-time">Aujourd'hui 14:30</span>
                    <span class="history-action">Vente de 2 bouteilles</span>
                    <span class="history-change">-2</span>
                </div>
                <div class="history-item">
                    <span class="history-time">Hier 09:15</span>
                    <span class="history-action">Réapprovisionnement</span>
                    <span class="history-change">+20</span>
                </div>
            </div>
        </div>
    </div>
</section>