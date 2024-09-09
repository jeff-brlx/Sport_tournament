# Plateforme de Gestion de Tournois de Sport  
![PHP](https://img.shields.io/badge/PHP-8.0-blue)
![Symfony](https://img.shields.io/badge/Symfony-7.0-black)
![MySQL](https://img.shields.io/badge/MySQL-5.7-orange)

Ce projet consiste en une plateforme permettant aux utilisateurs de **créer, organiser et participer à des tournois de sport**. Elle offre des fonctionnalités complètes pour la gestion des tournois, des joueurs, des inscriptions et des parties.

## Fonctionnalités Principales

- **Gestion des Tournois** : Création et organisation de tournois, définition des dates, lieux et règles.
- **Gestion des Joueurs** : Inscription des joueurs, création de profils, participation aux tournois.
- **Inscriptions aux Tournois** : Inscription à des tournois, gestion des inscriptions et suivi du statut des participants.
- **Gestion des Parties** : Organisation des matchs entre participants, saisie des scores et détermination des gagnants.
- **Administration** : Gestion centralisée des tournois, participants et parties via Postman.

## Technologies Utilisées

- **Symfony 7**
- **PHP 8**
- **PhpStorm**
- **phpMyAdmin**
- **Postman** (pour tester les routes)

## Installation

1. **Télécharger le dépôt et extraire le contenu**.
2. **Installer les dépendances** :
   ```bash
   composer install
3. **Configurer la base de données** :
   - Modifier le fichier `.env` pour y spécifier les paramètres de votre base de données.
   - Créer une table locale nommée `sport_tournaments` puis effectuer les migrations nécessaires :
     ```bash
     php bin/console doctrine:migrations:migrate
     ```

4. **Lancer le serveur** :
   ```bash
   symfony serve

5. **Tester les routes via Postman** : Une invitation vers l'espace de travail Postman est disponible via ce lien : https://app.getpostman.com/join-team?invite_code=85b722829589cf578897b765ec4f3e27&target_code=caa136d009aa0393e8cb69e8eb9ece35 .

