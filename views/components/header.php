<header class="header">
    <div class="container">
        <div class="header-brand">
            <a href="/" class="logo">
                🔥 <span>GazCameroun</span>
            </a>
        </div>
        
        <nav class="header-nav">
            <?php if (isset($_SESSION['user_type'])): ?>
                <div class="user-menu">
                    <span class="user-greeting">
                        Bonjour, <strong><?php echo htmlspecialchars($_SESSION['nom']); ?></strong>
                    </span>
                    
                    <?php if ($_SESSION['user_type'] === 'utilisateur'): ?>
                        <a href="/dashboard" class="nav-link">Mon Espace</a>
                        <a href="/commander" class="nav-link">Commander</a>
                        <a href="/mes-commandes" class="nav-link">Mes Commandes</a>
                    <?php else: ?>
                        <a href="/station-dashboard" class="nav-link">Tableau de Bord</a>
                        <a href="/gestion-stock" class="nav-link">Stock</a>
                        <a href="/commandes-recues" class="nav-link">Commandes</a>
                    <?php endif; ?>
                    
                    <a href="/logout" class="btn btn-outline">Déconnexion</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/login" class="btn btn-outline">Connexion</a>
                    <a href="/register" class="btn btn-primary">Inscription</a>
                </div>
            <?php endif; ?>
        </nav>
        
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>