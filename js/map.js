function initMap() {
    map = L.map('map').setView([14.7167, -17.4677], 13); // Dakar par défaut
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
}

function showMap(stations, userLat, userLng) {
    const mapContainer = document.getElementById('map');
    mapContainer.style.display = 'block';
    
    if (!map) {
        initMap();
    }
    
    // Clear existing markers
    stationMarkers.forEach(marker => map.removeLayer(marker));
    stationMarkers = [];
    if (userMarker) {
        map.removeLayer(userMarker);
    }
    
    // Add user marker
    userMarker = L.marker([userLat, userLng], {
        icon: L.icon({
            iconUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJDOC4xMzQwMSAyIDUgNS4xMzQwMSA1IDlDNSAxNC4yNSAxMiAyMiAxMiAyMkMxMiAyMiAxOSAxNC4yNSAxOSA5QzE5IDUuMTM0MDEgMTUuODY2IDIgMTIgMloiIGZpbGw9IiNEQzI2MjYiIHN0cm9rZT0iI0ZGRkZGRiIgc3Ryb2tlLXdpZHRoPSIyIi8+CjxjaXJjbGUgY3g9IjEyIiBjeT0iOSIgcj0iMyIgZmlsbD0iI0ZGRkZGRiIvPgo8L3N2Zz4K',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        })
    }).addTo(map);
    userMarker.bindPopup('Votre position').openPopup();
    
    // Add station markers
    stations.forEach(station => {
        const marker = L.marker([station.latitude, station.longitude], {
            icon: L.icon({
                iconUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJDOC4xMzQwMSAyIDUgNS4xMzQwMSA1IDlDNSAxNC4yNSAxMiAyMiAxMiAyMkMxMiAyMiAxOSAxNC4yNSAxOSA5QzE5IDUuMTM0MDEgMTUuODY2IDIgMTIgMloiIGZpbGw9IiMyNTYzRUIiIHN0cm9rZT0iI0ZGRkZGRiIgc3Ryb2tlLXdpZHRoPSIyIi8+CjxjaXJjbGUgY3g9IjEyIiBjeT0iOSIgcj0iMyIgZmlsbD0iI0ZGRkZGRiIvPgo8L3N2Zz4K',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            })
        }).addTo(map);
        
        marker.bindPopup(`
            <div>
                <strong>${station.nom}</strong><br>
                Stock: ${station.stock_gaz} bouteilles<br>
                Prix: ${station.prix_unite}€<br>
                Distance: ${station.distance_km.toFixed(1)} km
            </div>
        `);
        
        stationMarkers.push(marker);
    });
    
    // Fit map to show all markers
    const group = new L.featureGroup([userMarker, ...stationMarkers]);
    map.fitBounds(group.getBounds().pad(0.1));
}