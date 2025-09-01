// Gestion spécifique aux stations
document.addEventListener('DOMContentLoaded', function() {
    initStationDashboard();
    initOrderManagement();
});

function initStationDashboard() {
    // Formulaires de gestion stock/prix
    const addStockForm = document.getElementById('addStockForm');
    if (addStockForm) {
        addStockForm.addEventListener('submit', handleAddStock);
    }
    
    const setStockForm = document.getElementById('setStockForm');
    if (setStockForm) {
        setStockForm.addEventListener('submit', handleSetStock);
    }
    
    const updatePriceForm = document.getElementById('updatePriceForm');
    if (updatePriceForm) {
        updatePriceForm.addEventListener('submit', handleUpdatePrice);
    }
}

async function handleAddStock(e) {
    e.preventDefault();
    
    const quantity = document.getElementById('add_quantity').value;
    const currentStock = parseInt(document.querySelector('.stock-number').textContent);
    const newStock = currentStock + parseInt(quantity);
    
    await updateStock(newStock, 'Stock ajouté avec succès!');
    document.getElementById('add_quantity').value = '';
}

async function handleSetStock(e) {
    e.preventDefault();
    
    const newStock = document.getElementById('set_quantity').value;
    await updateStock(newStock, 'Stock défini avec succès!');
}

async function handleUpdatePrice(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Mise à jour...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/update-price', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de la mise à jour du prix', 'error');
    }
    
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
}

async function updateStock(newStock, successMessage) {
    try {
        const formData = new FormData();
        formData.append('nouveau_stock', newStock);
        
        const response = await fetch('/update-stock', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(successMessage, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Erreur lors de la mise à jour du stock', 'error');
    }
}

function initOrderManagement() {
    // Filtres de commandes
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function filterOrders(status) {
    const orders = document.querySelectorAll('.order-card[data-status]');
    
    orders.forEach(order => {
        if (status === 'all' || order.dataset.status === status) {
            order.style.display = 'block';
        } else {
            order.style.display = 'none';
        }
    });
}

async function updateOrderStatus(orderId, newStatus) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Messages de confirmation
    const confirmMessages = {
        'confirmee': 'Confirmer cette commande ?',
        'annulee': 'Êtes-vous sûr de vouloir refuser cette commande ?',
        'en_livraison': 'Marquer cette commande comme en cours de livraison ?',
        'livree': 'Confirmer que cette commande a été livrée ?'
    };
    
    if (!confirm(confirmMessages[newStatus])) {
        return;
    }
    
    button.textContent = '⏳ Mise à jour...';
    button.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);
        
        const response = await fetch('/update-order-status', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Statut mis à jour avec succès!', 'success');
            
            // Notification au client
            if (newStatus === 'confirmee') {
                showAlert('Le client sera notifié de la confirmation', 'info');
            } else if (newStatus === 'en_livraison') {
                showAlert('Le client sera notifié du départ en livraison', 'info');
            }
            
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Erreur mise à jour statut:', error);
        showAlert('Erreur lors de la mise à jour', 'error');
    }
    
    button.textContent = originalText;
    button.disabled = false;
}

function showOnMap(lat, lng) {
    showModal('mapModal');
    
    setTimeout(() => {
        if (window.deliveryMap) {
            window.deliveryMap.remove();
        }
        
        window.deliveryMap = L.map('delivery-map').setView([lat, lng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.deliveryMap);
        
        // Marqueur de livraison
        L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJDOC4xMzQwMSAyIDUgNS4xMzQwMSA1IDlDNSAxNC4yNSAxMiAyMiAxMiAyMkMxMiAyMiAxOSAxNC4yNSAxOSA5QzE5IDUuMTM0MDEgMTUuODY2IDIgMTIgMloiIGZpbGw9IiNEQzI2MjYiIHN0cm9rZT0iI0ZGRkZGRiIgc3Ryb2tlLXdpZHRoPSIyIi8+CjxjaXJjbGUgY3g9IjEyIiBjeT0iOSIgcj0iMyIgZmlsbD0iI0ZGRkZGRiIvPgo8L3N2Zz4K',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            })
        }).addTo(window.deliveryMap).bindPopup('📍 Adresse de livraison').openPopup();
    }, 100);
}

// Statistiques en temps réel
function updateDashboardStats() {
    // Cette fonction peut être appelée périodiquement pour mettre à jour les stats
    fetch('/api/get_station_data.php')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const stockElement = document.getElementById('stock-count');
                const pendingElement = document.getElementById('pending-orders');
                
                if (stockElement) {
                    stockElement.textContent = result.data.stock_gaz + ' bouteilles';
                }
                
                if (pendingElement) {
                    pendingElement.textContent = result.data.pending_orders;
                }
            }
        })
        .catch(error => console.error('Erreur mise à jour stats:', error));
}

// Mise à jour automatique toutes les 30 secondes
if (document.querySelector('.station-dashboard')) {
    setInterval(updateDashboardStats, 30000);
}