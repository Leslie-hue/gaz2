// Gestion des cartes avec Leaflet
let map;
let userMarker;
let stationMarkers = [];

function initMap(containerId = 'map', center = [3.8480, 11.5021], zoom = 13) {
    if (map) {
        map.remove();
    }
    
    map = L.map(containerId).setView(center, zoom);
    
    // Utiliser OpenStreetMap avec style adapté
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);
    
    return map;
}

function showMap(stations, userLat, userLng) {
    const mapContainer = document.getElementById('map-container');
    if (!mapContainer) return;
    
    mapContainer.style.display = 'block';
    
    // Initialiser la carte
    if (!map) {
        initMap('map', [userLat, userLng], 13);
    } else {
        map.setView([userLat, userLng], 13);
    }
    
    // Nettoyer les marqueurs existants
    clearMarkers();
    
    // Ajouter le marqueur utilisateur
    userMarker = L.marker([userLat, userLng], {
        icon: createUserIcon()
    }).addTo(map);
    
    userMarker.bindPopup(`
        <div class="popup-content">
            <h4>📍 Votre position</h4>
            <p>Adresse de livraison</p>
        </div>
    `).openPopup();
    
    // Ajouter les marqueurs des stations
    stations.forEach(station => {
        const marker = L.marker([station.latitude, station.longitude], {
            icon: createStationIcon(station.stock_gaz)
        }).addTo(map);
        
        const quantite = parseInt(document.getElementById('quantite').value);
        const prixBouteilles = station.prix_unite * quantite;
        const fraisLivraison = calculateDeliveryFee(station.distance_km);
        const prixTotal = prixBouteilles + fraisLivraison;
        
        marker.bindPopup(`
            <div class="popup-content">
                <h4>🏪 ${escapeHtml(station.nom)}</h4>
                <div class="popup-details">
                    <p><strong>Stock:</strong> ${station.stock_gaz} bouteilles</p>
                    <p><strong>Prix:</strong> ${formatPrice(station.prix_unite)}</p>
                    <p><strong>Distance:</strong> ${station.distance_km.toFixed(1)} km</p>
                    <p><strong>Total:</strong> ${formatPrice(prixTotal)}</p>
                </div>
                <button onclick="commander(${station.id})" class="btn btn-primary btn-small popup-btn">
                    Commander
                </button>
            </div>
        `);
        
        stationMarkers.push(marker);
    });
    
    // Ajuster la vue pour inclure tous les marqueurs
    const group = new L.featureGroup([userMarker, ...stationMarkers]);
    map.fitBounds(group.getBounds().pad(0.1));
}

function clearMarkers() {
    if (userMarker) {
        map.removeLayer(userMarker);
        userMarker = null;
    }
    
    stationMarkers.forEach(marker => map.removeLayer(marker));
    stationMarkers = [];
}

function createUserIcon() {
    return L.icon({
        iconUrl: 'data:image/svg+xml;base64,' + btoa(`
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="14" fill="#DC2626" stroke="#FFFFFF" stroke-width="2"/>
                <circle cx="16" cy="16" r="6" fill="#FFFFFF"/>
                <text x="16" y="20" text-anchor="middle" fill="#DC2626" font-size="8" font-weight="bold">📍</text>
            </svg>
        `),
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
}

function createStationIcon(stock) {
    const color = stock > 20 ? '#059669' : stock > 10 ? '#F59E0B' : '#DC2626';
    
    return L.icon({
        iconUrl: 'data:image/svg+xml;base64,' + btoa(`
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="14" fill="${color}" stroke="#FFFFFF" stroke-width="2"/>
                <text x="16" y="20" text-anchor="middle" fill="#FFFFFF" font-size="12" font-weight="bold">🏪</text>
            </svg>
        `),
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
}

// Géocodage spécifique au Cameroun
async function geocodeAddressCameroon(address) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ', Cameroun')}&accept-language=fr&limit=1`);
        const data = await response.json();
        
        if (data.length > 0) {
            return {
                lat: parseFloat(data[0].lat),
                lng: parseFloat(data[0].lon),
                display_name: data[0].display_name
            };
        }
        
        throw new Error('Adresse non trouvée');
    } catch (error) {
        console.error('Erreur géocodage:', error);
        throw error;
    }
}

// Géocodage inverse
async function reverseGeocodeCameroon(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr`);
        const data = await response.json();
        
        if (data.address) {
            const address = data.address;
            let formattedAddress = '';
            
            // Construire une adresse lisible pour le Cameroun
            if (address.road) formattedAddress += address.road + ', ';
            if (address.suburb || address.neighbourhood) {
                formattedAddress += (address.suburb || address.neighbourhood) + ', ';
            }
            if (address.city || address.town || address.village) {
                formattedAddress += (address.city || address.town || address.village);
            }
            
            return formattedAddress || data.display_name;
        }
        
        throw new Error('Adresse non trouvée');
    } catch (error) {
        console.error('Erreur géocodage inverse:', error);
        throw error;
    }
}

// Vérifier si une coordonnée est au Cameroun
function isInCameroon(lat, lng) {
    // Limites approximatives du Cameroun
    const bounds = {
        north: 13.1,
        south: 1.7,
        east: 16.2,
        west: 8.5
    };
    
    return lat >= bounds.south && lat <= bounds.north && 
           lng >= bounds.west && lng <= bounds.east;
}

// Obtenir la ville la plus proche
function getNearestCity(lat, lng) {
    let nearestCity = CAMEROON_CITIES[0];
    let minDistance = calculateDistance(lat, lng, nearestCity.lat, nearestCity.lng);
    
    CAMEROON_CITIES.forEach(city => {
        const distance = calculateDistance(lat, lng, city.lat, city.lng);
        if (distance < minDistance) {
            minDistance = distance;
            nearestCity = city;
        }
    });
    
    return nearestCity;
}

// Styles CSS pour les popups
const popupStyles = `
    .popup-content {
        min-width: 200px;
        text-align: center;
    }
    
    .popup-content h4 {
        margin-bottom: 8px;
        color: #111827;
    }
    
    .popup-details {
        margin: 12px 0;
        text-align: left;
    }
    
    .popup-details p {
        margin: 4px 0;
        font-size: 14px;
    }
    
    .popup-btn {
        margin-top: 8px;
        width: 100%;
    }
`;

// Injecter les styles
if (!document.getElementById('map-popup-styles')) {
    const style = document.createElement('style');
    style.id = 'map-popup-styles';
    style.textContent = popupStyles;
    document.head.appendChild(style);
}