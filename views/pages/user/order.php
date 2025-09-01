<section class="order-section">
    <div class="container">
        <div class="order-header">
            <h1>Commander du Gaz Domestique</h1>
            <p>Trouvez les meilleures offres près de chez vous</p>
        </div>
        
        <div class="order-form-container">
            <div class="order-form-card">
                <h2>Détails de votre commande</h2>
                
                <form id="orderSearchForm" class="order-form">
                    <div class="form-group">
                        <label for="quantite">Nombre de bouteilles de gaz</label>
                        <select id="quantite" name="quantite" class="form-control">
                            <option value="1">1 bouteille (12.5 kg)</option>
                            <option value="2">2 bouteilles (25 kg)</option>
                            <option value="3">3 bouteilles (37.5 kg)</option>
                            <option value="4">4 bouteilles (50 kg)</option>
                            <option value="5">5 bouteilles (62.5 kg)</option>
                        </select>
                        <small>Bouteilles de gaz domestique standard 12.5kg</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse_livraison">Adresse de livraison</label>
                        <input type="text" id="adresse_livraison" name="adresse_livraison" 
                               class="form-control" placeholder="Quartier, Ville" required>
                        <div class="location-actions">
                            <button type="button" onclick="getCurrentLocation()" class="btn btn-outline btn-small">
                                📍 Ma position actuelle
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">
                        🔍 Rechercher les stations
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Carte -->
        <div id="map-container" class="map-container" style="display: none;">
            <div id="map" class="map"></div>
        </div>
        
        <!-- Liste des stations -->
        <div id="stations-container" class="stations-container" style="display: none;">
            <h2>Stations disponibles</h2>
            <div id="stations-list" class="stations-list"></div>
        </div>
        
        <!-- Loading -->
        <div id="loading" class="loading-container" style="display: none;">
            <div class="loading-spinner"></div>
            <p>Recherche des stations...</p>
        </div>
    </div>
</section>