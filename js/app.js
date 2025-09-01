let map;
let userMarker;
let stationMarkers = [];

// Modals
function showLogin() {
    document.getElementById('loginModal').style.display = 'block';
}

function showRegister() {
    showRegisterUser();
}

function showRegisterUser() {
    document.getElementById('registerUserModal').style.display = 'block';
}

function showRegisterStation() {
    document.getElementById('registerStationModal').style.display = 'block';
}

function showStockModal() {
    document.getElementById('stockModal').style.display = 'block';
}

function showPriceModal() {
    document.getElementById('priceModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Forms handling
document.addEventListener('DOMContentLoaded', function() {
    // Login form
    document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur de connexion', 'error');
        }
    });

    // Register user form
    document.getElementById('registerUserForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/register_user.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Inscription réussie! Vous pouvez maintenant vous connecter.', 'success');
                closeModal('registerUserModal');
                this.reset();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur lors de l\'inscription', 'error');
        }
    });

    // Register station form
    document.getElementById('registerStationForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/register_station.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Station enregistrée avec succès!', 'success');
                closeModal('registerStationModal');
                this.reset();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur lors de l\'enregistrement', 'error');
        }
    });

    // Stock form
    document.getElementById('stockForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/update_stock.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Stock mis à jour!', 'success');
                closeModal('stockModal');
                loadStationData();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur lors de la mise à jour', 'error');
        }
    });

    // Price form
    document.getElementById('priceForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/update_price.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('Prix mis à jour!', 'success');
                closeModal('priceModal');
                loadStationData();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            showAlert('Erreur lors de la mise à jour', 'error');
        }
    });

    // Load initial data
    const stockCount = document.getElementById('stock-count');
    const stationOrdersContainer = document.getElementById('station-orders-container');
    const commandesContainer = document.getElementById('commandes-container');
    
    if (stockCount && stationOrdersContainer) {
        // This is a station dashboard
        loadStationData();
        loadStationOrders();
    } else if (commandesContainer) {
        // This is a user dashboard
        loadUserOrders();
    }
});

// Geolocation
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Reverse geocoding simple
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('adresse_livraison').value = data.display_name;
                })
                .catch(error => {
                    showAlert('Impossible de récupérer l\'adresse', 'warning');
                });
        }, function(error) {
            showAlert('Impossible d\'accéder à votre position', 'error');
        });
    } else {
        showAlert('La géolocalisation n\'est pas supportée', 'error');
    }
}

function getLocationForStation() {
    const adresse = document.querySelector('#registerStationForm input[name="adresse"]').value;
    
    if (!adresse) {
        showAlert('Veuillez saisir une adresse', 'warning');
        return;
    }
    
    // Geocoding avec OpenStreetMap
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const location = data[0];
                // Stocker les coordonnées pour l'envoi du formulaire
                document.querySelector('#registerStationForm').dataset.latitude = location.lat;
                document.querySelector('#registerStationForm').dataset.longitude = location.lon;
                showAlert('Localisation trouvée!', 'success');
            } else {
                showAlert('Adresse non trouvée', 'error');
            }
        })
        .catch(error => {
            showAlert('Erreur lors de la géolocalisation', 'error');
        });
}

// Search stations
async function searchStations() {
    const adresse = document.getElementById('adresse_livraison').value;
    const quantite = document.getElementById('quantite').value;
    
    if (!adresse) {
        showAlert('Veuillez saisir une adresse de livraison', 'warning');
        return;
    }
    
    try {
        // Geocoding de l'adresse
        const geoResponse = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}`);
        const geoData = await geoResponse.json();
        
        if (geoData.length === 0) {
            showAlert('Adresse non trouvée', 'error');
            return;
        }
        
        const userLat = parseFloat(geoData[0].lat);
        const userLng = parseFloat(geoData[0].lon);
        
        // Rechercher les stations proches
        const response = await fetch(`api/search_stations.php?lat=${userLat}&lng=${userLng}&quantite=${quantite}`);
        const result = await response.json();
        
        if (result.success && result.stations.length > 0) {
            displayStations(result.stations, userLat, userLng);
            showMap(result.stations, userLat, userLng);
        } else {
            showAlert('Aucune station disponible dans votre région', 'warning');
        }
    } catch (error) {
        showAlert('Erreur lors de la recherche', 'error');
    }
}

function displayStations(stations, userLat, userLng) {
    const container = document.getElementById('stations-list');
    container.innerHTML = '<h3>Stations disponibles</h3>';
    
    stations.forEach(station => {
        const stationEl = document.createElement('div');
        stationEl.className = 'station-card';
        stationEl.innerHTML = `
            <div class="station-header">
                <div class="station-info">
                    <h3>${station.nom}</h3>
                    <p>${station.adresse}</p>
                </div>
            </div>
            <div class="station-details">
                <div class="detail-item">
                    <strong>${station.distance_km.toFixed(1)} km</strong>
                    <span>Distance</span>
                </div>
                <div class="detail-item">
                    <strong>${station.stock_gaz}</strong>
                    <span>Stock disponible</span>
                </div>
                <div class="detail-item">
                    <strong>${station.prix_unite}€</strong>
                    <span>Prix unitaire</span>
                </div>
                <div class="detail-item">
                    <strong>${(station.prix_unite * document.getElementById('quantite').value).toFixed(2)}€</strong>
                    <span>Total</span>
                </div>
            </div>
            <button onclick="commander(${station.id})" class="btn btn-success" style="width: 100%;">
                Commander ${document.getElementById('quantite').value} bouteille(s)
            </button>
        `;
        container.appendChild(stationEl);
    });
}

async function commander(stationId) {
    const quantite = document.getElementById('quantite').value;
    const adresse = document.getElementById('adresse_livraison').value;
    
    try {
        const geoResponse = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}`);
        const geoData = await geoResponse.json();
        const userLat = parseFloat(geoData[0].lat);
        const userLng = parseFloat(geoData[0].lon);
        
        const formData = new FormData();
        formData.append('station_id', stationId);
        formData.append('quantite', quantite);
        formData.append('adresse_livraison', adresse);
        formData.append('latitude_livraison', userLat);
        formData.append('longitude_livraison', userLng);
        
        const response = await fetch('api/create_order.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Commande passée avec succès!', 'success');
            loadUserOrders();
            // Clear form
            document.getElementById('adresse_livraison').value = '';
            document.getElementById('stations-list').innerHTML = '';
            document.getElementById('map').style.display = 'none';
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de la commande', 'error');
    }
}

// Load user orders
async function loadUserOrders() {
    try {
        const response = await fetch('api/get_user_orders.php');
        const result = await response.json();
        
        const container = document.getElementById('commandes-container');
        if (result.success && result.orders.length > 0) {
            container.innerHTML = result.orders.map(order => `
                <div class="order-card">
                    <div class="order-header">
                        <span>Commande #${order.id}</span>
                        <span class="order-status status-${order.statut}">${order.statut.replace('_', ' ')}</span>
                    </div>
                    <p><strong>Station:</strong> ${order.station_nom}</p>
                    <p><strong>Quantité:</strong> ${order.quantite} bouteille(s)</p>
                    <p><strong>Prix total:</strong> ${order.prix_total}€</p>
                    <p><strong>Adresse:</strong> ${order.adresse_livraison}</p>
                    <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString('fr-FR')}</p>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p>Aucune commande pour le moment.</p>';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
    }
}

// Load station data
async function loadStationData() {
    try {
        const response = await fetch('api/get_station_data.php');
        const result = await response.json();
        
        if (result.success) {
            const stockCount = document.getElementById('stock-count');
            const pendingOrders = document.getElementById('pending-orders');
            const pricePerUnit = document.getElementById('price-per-unit');
            
            if (stockCount) stockCount.textContent = result.data.stock_gaz + ' bouteilles';
            if (pendingOrders) pendingOrders.textContent = result.data.pending_orders;
            if (pricePerUnit) pricePerUnit.textContent = result.data.prix_unite + '€';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
    }
}

// Load station orders
async function loadStationOrders() {
    try {
        const response = await fetch('api/get_station_orders.php');
        const result = await response.json();
        
        const container = document.getElementById('station-orders-container');
        if (!container) {
            console.log('station-orders-container not found on this page');
            return;
        }
        
        if (result.success && result.orders.length > 0) {
            container.innerHTML = result.orders.map(order => `
                <div class="order-card">
                    <div class="order-header">
                        <span>Commande #${order.id}</span>
                        <span class="order-status status-${order.statut}">${order.statut.replace('_', ' ')}</span>
                    </div>
                    <p><strong>Client:</strong> ${order.client_nom}</p>
                    <p><strong>Téléphone:</strong> ${order.client_telephone}</p>
                    <p><strong>Quantité:</strong> ${order.quantite} bouteille(s)</p>
                    <p><strong>Prix total:</strong> ${order.prix_total}€</p>
                    <p><strong>Adresse:</strong> ${order.adresse_livraison}</p>
                    <p><strong>Distance:</strong> ${order.distance_km.toFixed(1)} km</p>
                    <div style="margin-top: 16px; display: flex; gap: 8px;">
                        ${order.statut === 'en_attente' ? `
                            <button onclick="updateOrderStatus(${order.id}, 'confirmee')" class="btn btn-success btn-small">Confirmer</button>
                            <button onclick="updateOrderStatus(${order.id}, 'annulee')" class="btn btn-secondary btn-small">Refuser</button>
                        ` : ''}
                        ${order.statut === 'confirmee' ? `
                            <button onclick="updateOrderStatus(${order.id}, 'en_livraison')" class="btn btn-primary btn-small">En livraison</button>
                        ` : ''}
                        ${order.statut === 'en_livraison' ? `
                            <button onclick="updateOrderStatus(${order.id}, 'livree')" class="btn btn-success btn-small">Livré</button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p>Aucune commande reçue.</p>';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
    }
}

async function updateOrderStatus(orderId, newStatus) {
    try {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);
        
        const response = await fetch('api/update_order_status.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Statut mis à jour!', 'success');
            loadStationOrders();
            loadStationData();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de la mise à jour', 'error');
    }
}

// Alert system
function showAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    document.body.insertBefore(alert, document.body.firstChild);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// PWA installation
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    deferredPrompt = e;
    // Show install button or prompt
});

function installApp() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            deferredPrompt = null;
        });
    }
}

// Service Worker notifications
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission();
    }
}

function showNotification(title, body) {
    if ('serviceWorker' in navigator && 'Notification' in window && Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(registration => {
            registration.showNotification(title, {
                body: body,
                icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUE3NkY4RDI3OEM1MTFFNjk0NTFFNkQ1OUEzOUJEMzciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUE3NkY4RDM3OEM1MTFFNjk0NTFFNkQ1OUEzOUJEMzciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFQTc2RjhEMDc4QzUxMUU2OTQ1MUU2RDU5QTM5QkQzNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFQTc2RjhEMTc4QzUxMUU2OTQ1MUU2RDU5QTM5QkQzNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pv7/AAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEH',
                badge: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUE3NkY4RDI3OEM1MTFFNjk0NTFFNkQ1OUEzOUJEMzciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUE3NkY4RDM3OEM1MTFFNjk0NTFFNkQ1OUEzOUJEMzciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFQTc2RjhEMDc4QzUxMUU2OTQ1MUU2RDU5QTM5QkQzNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFQTc2RjhEMTc4QzUxMUU2OTQ1MUU2RDU5QTM5QkQzNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pv7/AAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEHAAABWKLEH'
            });
        });
    }
}