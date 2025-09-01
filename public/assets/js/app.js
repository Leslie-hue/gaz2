// Configuration globale
const CONFIG = {
    currency: 'FCFA',
    deliveryRadius: 25,
    minOrderQuantity: 1,
    maxOrderQuantity: 10
};

// Gestion des onglets
function showTab(tabName) {
    // Masquer tous les onglets
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Désactiver tous les boutons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Activer l'onglet sélectionné
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// Gestion des modals
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showStockModal() {
    showModal('stockModal');
}

function showPriceModal() {
    showModal('priceModal');
}

// Fermer modal en cliquant à l'extérieur
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Menu mobile
function toggleMobileMenu() {
    const nav = document.querySelector('.header-nav');
    nav.classList.toggle('mobile-open');
}

// Géolocalisation
function getCurrentLocation() {
    if (!navigator.geolocation) {
        showAlert('La géolocalisation n\'est pas supportée par votre navigateur', 'error');
        return;
    }
    
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '📍 Localisation...';
    button.disabled = true;
    
    navigator.geolocation.getCurrentPosition(
        async function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr`);
                const data = await response.json();
                
                if (data.display_name) {
                    // Extraire les parties pertinentes de l'adresse
                    const address = data.address;
                    let formattedAddress = '';
                    
                    if (address.road) formattedAddress += address.road + ', ';
                    if (address.suburb || address.neighbourhood) formattedAddress += (address.suburb || address.neighbourhood) + ', ';
                    if (address.city || address.town) formattedAddress += (address.city || address.town);
                    
                    document.getElementById('adresse_livraison').value = formattedAddress || data.display_name;
                    showAlert('Position récupérée avec succès', 'success');
                } else {
                    throw new Error('Adresse non trouvée');
                }
            } catch (error) {
                showAlert('Impossible de récupérer l\'adresse', 'warning');
                console.error('Erreur géocodage inverse:', error);
            }
            
            button.textContent = originalText;
            button.disabled = false;
        },
        function(error) {
            let message = 'Erreur de géolocalisation';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Accès à la position refusé';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Position indisponible';
                    break;
                case error.TIMEOUT:
                    message = 'Délai d\'attente dépassé';
                    break;
            }
            showAlert(message, 'error');
            button.textContent = originalText;
            button.disabled = false;
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    );
}

// Système d'alertes
function showAlert(message, type = 'info') {
    // Supprimer les alertes existantes
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
    
    // Permettre la fermeture manuelle
    alert.addEventListener('click', () => alert.remove());
}

// Formatage des prix
function formatPrice(amount) {
    return new Intl.NumberFormat('fr-FR').format(amount) + ' ' + CONFIG.currency;
}

// Formatage des dates
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Validation téléphone camerounais
function validateCameroonPhone(phone) {
    return /^(6[5-9]|2[2-3])[0-9]{7}$/.test(phone);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des formulaires d'authentification
    initAuthForms();
    
    // Gestion des formulaires de station
    initStationForms();
    
    // Demander permission pour les notifications
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});

function initAuthForms() {
    // Formulaire de connexion
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Connexion...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Connexion réussie!', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect || '/';
                    }, 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur de connexion au serveur', 'error');
            }
            
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // Formulaires d'inscription
    const registerUserForm = document.getElementById('registerUserForm');
    if (registerUserForm) {
        registerUserForm.addEventListener('submit', handleUserRegistration);
    }
    
    const registerStationForm = document.getElementById('registerStationForm');
    if (registerStationForm) {
        registerStationForm.addEventListener('submit', handleStationRegistration);
    }
}

async function handleUserRegistration(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Validation téléphone
    const phone = formData.get('telephone');
    if (!validateCameroonPhone(phone)) {
        showAlert('Numéro de téléphone camerounais invalide (ex: 655123456)', 'error');
        return;
    }
    
    submitBtn.textContent = 'Inscription...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/register-user', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            this.reset();
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de l\'inscription', 'error');
    }
    
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
}

async function handleStationRegistration(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Validation téléphone
    const phone = formData.get('telephone');
    if (!validateCameroonPhone(phone)) {
        showAlert('Numéro de téléphone camerounais invalide', 'error');
        return;
    }
    
    submitBtn.textContent = 'Enregistrement...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/register-station', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            this.reset();
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de l\'enregistrement', 'error');
    }
    
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
}

function initStationForms() {
    // Formulaire de mise à jour du stock
    const stockForm = document.getElementById('stockForm');
    if (stockForm) {
        stockForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/update-stock', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('stockModal');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur lors de la mise à jour', 'error');
            }
        });
    }
    
    // Formulaire de mise à jour du prix
    const priceForm = document.getElementById('priceForm');
    if (priceForm) {
        priceForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('/update-price', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal('priceModal');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Erreur lors de la mise à jour', 'error');
            }
        });
    }
}