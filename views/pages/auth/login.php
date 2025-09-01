<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Connexion</h1>
                <p>Accédez à votre espace GazCameroun</p>
            </div>
            
            <form id="loginForm" class="auth-form">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required class="form-control" 
                           placeholder="votre@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="user_type">Type de compte</label>
                    <select id="user_type" name="user_type" required class="form-control">
                        <option value="utilisateur">Particulier</option>
                        <option value="station">Station-service</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    Se connecter
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Pas encore de compte ? <a href="/register">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
</section>