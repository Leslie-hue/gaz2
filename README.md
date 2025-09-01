# GazLivraison - Application de Commande de Gaz à Domicile

Application PWA complète pour la commande et livraison de gaz de cuisine à domicile.

## Fonctionnalités

### Pour les Particuliers
- Inscription et connexion sécurisée
- Recherche automatique des stations les plus proches
- Commande de gaz avec géolocalisation
- Suivi des commandes en temps réel
- Interface responsive et PWA

### Pour les Stations de Service
- Enregistrement avec localisation automatique
- Gestion du stock de gaz
- Réception et traitement des commandes
- Mise à jour des prix
- Tableau de bord complet

## Technologies Utilisées

- **Backend**: PHP 8.2 avec SQLite
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Cartes**: OpenStreetMap avec Leaflet.js
- **PWA**: Service Worker, Web App Manifest
- **Conteneurisation**: Docker + Docker Compose

## Installation avec Docker

1. Cloner le projet
2. Construire et lancer l'application:
```bash
docker-compose up --build
```

3. Accéder à l'application sur http://localhost:8080

## Structure de la Base de Données

- **stations**: Informations des stations de service avec géolocalisation
- **utilisateurs**: Comptes des particuliers
- **commandes**: Commandes avec calcul automatique de distance

## Fonctionnalités PWA

- Installation sur mobile/desktop
- Fonctionnement hors ligne partiel
- Notifications push
- Design responsive adaptatif

## API Endpoints

- `/api/login.php` - Connexion
- `/api/register_user.php` - Inscription particulier
- `/api/register_station.php` - Inscription station
- `/api/search_stations.php` - Recherche stations proches
- `/api/create_order.php` - Création de commande
- `/api/update_stock.php` - Mise à jour stock
- `/api/update_order_status.php` - Gestion statut commandes

## Calcul de Distance

L'application utilise la formule de Haversine pour calculer précisément les distances entre l'utilisateur et les stations de service, permettant de proposer automatiquement les stations les plus proches.