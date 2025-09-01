// Utilitaires généraux

// Formatage des prix en FCFA
function formatPrice(amount) {
    return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
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
    // Formats acceptés: 6XXXXXXXX (mobile) ou 2XXXXXXXX (fixe)
    return /^(6[5-9]|2[2-3])[0-9]{7}$/.test(phone);
}

// Calcul de distance (formule de Haversine)
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Rayon de la Terre en km
    const dLat = deg2rad(lat2 - lat1);
    const dLng = deg2rad(lng2 - lng1);
    
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}

// Calcul des frais de livraison
function calculateDeliveryFee(distance) {
    if (distance <= 5) return 500;
    if (distance <= 10) return 1000;
    if (distance <= 20) return 1500;
    return 2000;
}

// Échapper HTML pour éviter XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Debounce pour les recherches
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Validation d'email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Gestion des erreurs réseau
function handleNetworkError(error) {
    console.error('Erreur réseau:', error);
    
    if (!navigator.onLine) {
        showAlert('Pas de connexion internet. Vérifiez votre connexion.', 'error');
    } else {
        showAlert('Erreur de communication avec le serveur', 'error');
    }
}

// Stockage local pour mode hors ligne
const LocalStorage = {
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (error) {
            console.error('Erreur stockage local:', error);
        }
    },
    
    get(key) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : null;
        } catch (error) {
            console.error('Erreur lecture stockage local:', error);
            return null;
        }
    },
    
    remove(key) {
        try {
            localStorage.removeItem(key);
        } catch (error) {
            console.error('Erreur suppression stockage local:', error);
        }
    }
};

// Gestion des notifications push
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                showAlert('Notifications activées!', 'success');
            }
        });
    }
}

function showNotification(title, body, icon = '/icons/icon-192x192.png') {
    if ('serviceWorker' in navigator && 'Notification' in window && Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(registration => {
            registration.showNotification(title, {
                body: body,
                icon: icon,
                badge: '/icons/icon-192x192.png',
                vibrate: [100, 50, 100],
                data: {
                    dateOfArrival: Date.now(),
                    primaryKey: '1'
                },
                actions: [
                    {
                        action: 'view',
                        title: 'Voir',
                        icon: '/icons/icon-192x192.png'
                    }
                ]
            });
        });
    }
}

// Détection du mode hors ligne
window.addEventListener('online', function() {
    showAlert('Connexion rétablie', 'success');
});

window.addEventListener('offline', function() {
    showAlert('Mode hors ligne activé', 'warning');
});

// Utilitaires pour les villes camerounaises
const CAMEROON_CITIES = [
    { name: 'Yaoundé', lat: 3.8480, lng: 11.5021 },
    { name: 'Douala', lat: 4.0511, lng: 9.7679 },
    { name: 'Bafoussam', lat: 5.4781, lng: 10.4167 },
    { name: 'Bamenda', lat: 5.9597, lng: 10.1453 },
    { name: 'Garoua', lat: 9.3265, lng: 13.3958 },
    { name: 'Maroua', lat: 10.5913, lng: 14.3153 },
    { name: 'Ngaoundéré', lat: 7.3167, lng: 13.5833 }
];

function getCityCoordinates(cityName) {
    const city = CAMEROON_CITIES.find(c => 
        c.name.toLowerCase().includes(cityName.toLowerCase())
    );
    return city || { name: 'Yaoundé', lat: 3.8480, lng: 11.5021 };
}

// Conversion des devises (si nécessaire)
function convertToFCFA(amount, fromCurrency = 'EUR') {
    const rates = {
        'EUR': 656, // 1 EUR = 656 FCFA (approximatif)
        'USD': 580  // 1 USD = 580 FCFA (approximatif)
    };
    
    return Math.round(amount * (rates[fromCurrency] || 1));
}

// Validation des données camerounaises
function validateCameroonData(data) {
    const errors = [];
    
    // Validation nom (pas de caractères spéciaux)
    if (data.nom && !/^[a-zA-ZÀ-ÿ\s'-]+$/.test(data.nom)) {
        errors.push('Le nom contient des caractères invalides');
    }
    
    // Validation téléphone
    if (data.telephone && !validateCameroonPhone(data.telephone)) {
        errors.push('Numéro de téléphone camerounais invalide');
    }
    
    // Validation adresse (doit contenir une ville camerounaise)
    if (data.adresse) {
        const hasValidCity = CAMEROON_CITIES.some(city => 
            data.adresse.toLowerCase().includes(city.name.toLowerCase())
        );
        if (!hasValidCity) {
            errors.push('L\'adresse doit contenir une ville camerounaise valide');
        }
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Gestion des erreurs spécifiques au contexte
function handleCameroonSpecificErrors(error) {
    const errorMessages = {
        'network_error': 'Problème de connexion. Vérifiez votre réseau mobile ou WiFi.',
        'location_error': 'Impossible de vous localiser. Saisissez votre adresse manuellement.',
        'payment_error': 'Erreur de paiement. Contactez votre opérateur mobile.',
        'delivery_error': 'Zone de livraison non couverte. Service disponible uniquement dans les grandes villes.'
    };
    
    return errorMessages[error] || 'Une erreur est survenue';
}