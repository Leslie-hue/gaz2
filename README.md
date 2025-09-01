# GazCameroun - Application de Livraison de Gaz Domestique

Application PWA complète pour la commande et livraison de gaz domestique au Cameroun.

## Fonctionnalités

### Pour les Particuliers
- Inscription et connexion sécurisée
- Recherche automatique des stations les plus proches (rayon 25km)
- Commande de gaz domestique avec géolocalisation
- Suivi des commandes en temps réel
- Calcul automatique des frais de livraison
- Interface responsive et PWA
- Validation spécifique aux numéros camerounais

### Pour les Stations de Service
- Enregistrement avec localisation automatique
- Gestion du stock de gaz domestique
- Réception et traitement des commandes
- Mise à jour des prix en FCFA
- Tableau de bord complet
- Gestion des zones de livraison

## Technologies Utilisées

- **Architecture**: MVC (Model-View-Controller)
- **Backend**: PHP 8.2 avec SQLite, architecture modulaire
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla) avec design moderne
- **Cartes**: OpenStreetMap avec Leaflet.js
- **PWA**: Service Worker, Web App Manifest
- **Conteneurisation**: Docker + Docker Compose
- **Contexte**: Adapté au marché camerounais (FCFA, villes, téléphones)

## Installation avec Docker

1. Cloner le projet
2. Construire et lancer l'application:
```bash
docker-compose up --build
```

3. Accéder à l'application sur http://localhost:8080

## Architecture MVC

```
├── config/           # Configuration et routes
├── controllers/      # Logique de contrôle
├── models/          # Logique métier et accès données
├── views/           # Templates et interfaces
│   ├── layouts/     # Layouts principaux
│   ├── pages/       # Pages spécifiques
│   └── components/  # Composants réutilisables
└── public/assets/   # Ressources statiques
```

## Structure de la Base de Données

- **stations**: Stations-service avec géolocalisation et gestion stock
- **utilisateurs**: Comptes particuliers avec validation camerounaise
- **commandes**: Commandes avec calcul distance et frais de livraison

## Fonctionnalités PWA

- Installation sur mobile/desktop
- Fonctionnement hors ligne partiel
- Notifications push
- Design responsive adaptatif
- Optimisé pour les connexions mobiles africaines

## Routes Principales

- `/` - Page d'accueil
- `/login` - Connexion
- `/register` - Inscription (particulier/station)
- `/dashboard` - Espace utilisateur
- `/commander` - Commande de gaz
- `/station-dashboard` - Tableau de bord station
- `/gestion-stock` - Gestion du stock
- `/commandes-recues` - Gestion des commandes

## Spécificités Camerounaises

- **Devise**: Franc CFA (FCFA)
- **Téléphones**: Validation des numéros camerounais (6XXXXXXXX, 2XXXXXXXX)
- **Géolocalisation**: Optimisée pour les villes camerounaises
- **Frais de livraison**: Adaptés aux distances locales
- **Prix**: Bouteilles de gaz 12.5kg standard
- **Villes**: Yaoundé, Douala, Bafoussam, Bamenda, etc.