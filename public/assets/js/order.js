// Gestion des commandes utilisateur
let currentStations = [];
let userCoordinates = null;

document.addEventListener('DOMContentLoaded', function() {
    const orderSearchForm = document.getElementById('orderSearchForm');
    if (orderSearchForm) {
        orderSearchForm.addEventListener('submit', handleOrderSearch);
    }
});

async function handleOrderSearch(e) {
    e.preventDefault();
    
    const adresse = document.getElementById('adresse_livraison').value;
    const quantite = document.getElementById('quantite').value;
    
    if (!adresse.trim()) {
        showAlert('Veuillez saisir une adresse de livraison', 'warning');
        return;
    }
    
    showLoading(true);
    
    try {
        // Géocoder l'adresse
        const geoResponse = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse + ', Cameroun')}&accept-language=fr&limit=1`);
        const geoData = await geoResponse.json();
        
        if (geoData.length === 0) {
            showAlert('Adresse non trouvée. Veuillez vérifier l\'orthographe.', 'error');
            showLoading(false);
            return;
        }
        
        userCoordinates = {
            lat: parseFloat(geoData[0].lat),
            lng: parseFloat(geoData[0].lon)
        };
        
        // Rechercher les stations
        const formData = new FormData();
        formData.append('lat', userCoordinates.lat);
        formData.append('lng', userCoordinates.lng);
        formData.append('quantite', quantite);
        
        const response = await fetch('/search-stations', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success && result.stations.length > 0) {
            currentStations = result.stations;
            displayStations(result.stations);
            showMap(result.stations, userCoordinates.lat, userCoordinates.lng);
            showAlert(`${result.stations.length} station(s) trouvée(s) près de chez vous`, 'success');
        } else {
            showAlert('Aucune station disponible dans votre région (rayon de 25km)', 'warning');
        }
    } catch (error) {
        console.error('Erreur recherche:', error);
        showAlert('Erreur lors de la recherche des stations', 'error');
    }
    
    showLoading(false);
}

function displayStations(stations) {
    const container = document.getElementById('stations-list');
    const stationsContainer = document.getElementById('stations-container');
    
    if (!container || !stationsContainer) return;
    
    stationsContainer.style.display = 'block';
    
    const quantite = parseInt(document.getElementById('quantite').value);
    
    container.innerHTML = stations.map(station => {
        const prixBouteilles = station.prix_unite * quantite;
        const fraisLivraison = calculateDeliveryFee(station.distance_km);
        const prixTotal = prixBouteilles + fraisLivraison;
        
        return `
            <div class="station-card" data-station-id="${station.id}">
                <div class="station-header">
                    <div class="station-info">
                        <h3>${escapeHtml(station.nom)}</h3>
                        <p>📍 ${escapeHtml(station.adresse)}</p>
                        <p>📞 ${station.telephone}</p>
                    </div>
                    <div class="station-badge">
                        <span class="distance">${station.distance_km.toFixed(1)} km</span>
                    </div>
                </div>
                
                <div class="station-details">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Stock disponible</span>
                            <span class="detail-value">${station.stock_gaz} bouteilles</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Prix unitaire</span>
                            <span class="detail-value">${formatPrice(station.prix_unite)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Prix bouteilles</span>
                            <span class="detail-value">${formatPrice(prixBouteilles)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Frais livraison</span>
                            <span class="detail-value">${formatPrice(fraisLivraison)}</span>
                        </div>
                    </div>
                    
                    <div class="total-price">
                        <span class="total-label">Total à payer:</span>
                        <span class="total-value">${formatPrice(prixTotal)}</span>
                    </div>
                </div>
                
                <div class="station-actions">
                    <button onclick="commander(${station.id})" class="btn btn-primary btn-full">
                        🔥 Commander ${quantite} bouteille(s)
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

async function commander(stationId) {
    if (!userCoordinates) {
        showAlert('Erreur: coordonnées de livraison manquantes', 'error');
        return;
    }
    
    const quantite = document.getElementById('quantite').value;
    const adresse = document.getElementById('adresse_livraison').value;
    
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '⏳ Commande en cours...';
    button.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('station_id', stationId);
        formData.append('quantite', quantite);
        formData.append('adresse_livraison', adresse);
        formData.append('latitude_livraison', userCoordinates.lat);
        formData.append('longitude_livraison', userCoordinates.lng);
        
        const response = await fetch('/create-order', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('🎉 Commande passée avec succès! Vous serez contacté par la station.', 'success');
            
            // Réinitialiser le formulaire
            document.getElementById('adresse_livraison').value = '';
            document.getElementById('quantite').value = '1';
            document.getElementById('stations-container').style.display = 'none';
            document.getElementById('map-container').style.display = 'none';
            
            // Rediriger vers les commandes après 3 secondes
            setTimeout(() => {
                window.location.href = '/mes-commandes';
            }, 3000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erreur commande:', error);
        showAlert('Erreur lors de la commande', 'error');
    }
    
    button.textContent = originalText;
    button.disabled = false;
}

function calculateDeliveryFee(distance) {
    // Frais de livraison basés sur la distance (contexte camerounais)
    if (distance <= 5) return 500;   // 500 FCFA pour moins de 5km
    if (distance <= 10) return 1000; // 1000 FCFA pour 5-10km
    if (distance <= 20) return 1500; // 1500 FCFA pour 10-20km
    return 2000; // 2000 FCFA pour plus de 20km
}

function showLoading(show) {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = show ? 'block' : 'none';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Géolocalisation pour station
async function getLocationForStation() {
    const adresseInput = document.querySelector('#registerStationForm input[name="adresse"]');
    const adresse = adresseInput.value;
    
    if (!adresse.trim()) {
        showAlert('Veuillez saisir une adresse', 'warning');
        return;
    }
    
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '📍 Localisation...';
    button.disabled = true;
    
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse + ', Cameroun')}&accept-language=fr&limit=1`);
        const data = await response.json();
        
        if (data.length > 0) {
            const location = data[0];
            // Stocker les coordonnées
            document.querySelector('#registerStationForm').dataset.latitude = location.lat;
            document.querySelector('#registerStationForm').dataset.longitude = location.lon;
            showAlert('📍 Localisation trouvée et enregistrée!', 'success');
        } else {
            showAlert('Adresse non trouvée. Vérifiez l\'orthographe.', 'error');
        }
    } catch (error) {
        console.error('Erreur géolocalisation:', error);
        showAlert('Erreur lors de la géolocalisation', 'error');
    }
    
    button.textContent = originalText;
    button.disabled = false;
}