<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Inscription</h1>
                <p>Rejoignez la communauté GazCameroun</p>
            </div>
            
            <div class="register-tabs">
                <button class="tab-btn active" onclick="showTab('user')">
                    👤 Particulier
                </button>
                <button class="tab-btn" onclick="showTab('station')">
                    🏪 Station-service
                </button>
            </div>
            
            <!-- Formulaire Particulier -->
            <div id="user-tab" class="tab-content active">
                <form id="registerUserForm" class="auth-form">
                    <div class="form-group">
                        <label for="user_nom">Nom complet</label>
                        <input type="text" id="user_nom" name="nom" required class="form-control" 
                               placeholder="Jean Dupont">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_email">Adresse email</label>
                        <input type="email" id="user_email" name="email" required class="form-control" 
                               placeholder="jean@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_telephone">Téléphone</label>
                        <input type="tel" id="user_telephone" name="telephone" required class="form-control" 
                               placeholder="655123456" pattern="[6][5-9][0-9]{7}|[2][2-3][0-9]{7}">
                        <small>Format: 655123456 ou 222123456</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_adresse">Adresse complète</label>
                        <input type="text" id="user_adresse" name="adresse" required class="form-control" 
                               placeholder="Quartier, Ville, Région">
                    </div>
                    
                    <div class="form-group">
                        <label for="user_password">Mot de passe</label>
                        <input type="password" id="user_password" name="password" required class="form-control" 
                               minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">
                        S'inscrire comme particulier
                    </button>
                </form>
            </div>
            
            <!-- Formulaire Station -->
            <div id="station-tab" class="tab-content">
                <form id="registerStationForm" class="auth-form">
                    <div class="form-group">
                        <label for="station_nom">Nom de la station</label>
                        <input type="text" id="station_nom" name="nom" required class="form-control" 
                               placeholder="Station Total Mvan">
                    </div>
                    
                    <div class="form-group">
                        <label for="station_email">Email professionnel</label>
                        <input type="email" id="station_email" name="email" required class="form-control" 
                               placeholder="station@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="station_telephone">Téléphone</label>
                        <input type="tel" id="station_telephone" name="telephone" required class="form-control" 
                               placeholder="655123456">
                    </div>
                    
                    <div class="form-group">
                        <label for="station_adresse">Adresse de la station</label>
                        <input type="text" id="station_adresse" name="adresse" required class="form-control" 
                               placeholder="Adresse complète avec quartier et ville">
                        <button type="button" onclick="getLocationForStation()" class="btn btn-small btn-outline">
                            📍 Localiser sur la carte
                        </button>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock_gaz">Stock initial</label>
                            <input type="number" id="stock_gaz" name="stock_gaz" min="0" required class="form-control" 
                                   placeholder="50">
                        </div>
                        
                        <div class="form-group">
                            <label for="prix_unite">Prix par bouteille (FCFA)</label>
                            <input type="number" id="prix_unite" name="prix_unite" min="500" required class="form-control" 
                                   placeholder="3500" value="3500">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="station_password">Mot de passe</label>
                        <input type="password" id="station_password" name="password" required class="form-control" 
                               minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">
                        Enregistrer ma station
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>Déjà inscrit ? <a href="/login">Connectez-vous</a></p>
            </div>
        </div>
    </div>
</section>