# BlueWave Messenger (PHP + MySQL)

Application de messagerie complète pour apprendre le backend PHP, les sessions, MySQL, et un frontend moderne.

## Fonctionnalités

- Authentification par sessions PHP (inscription/connexion/déconnexion)
- Messagerie privée entre utilisateurs
- Groupes de discussion
- Création de groupes avec plusieurs membres
- Page d'accueil avec la liste de tous les utilisateurs et des groupes
- Interface bleue responsive (desktop + mobile)
- Protection CSRF sur tous les formulaires

## Architecture

- `app/`: logique backend (config, auth, db, repositories, helpers)
- `public/`: pages accessibles + assets CSS/JS
- `database/schema.sql`: script SQL complet

## Installation

1. Créer la base de données:
```bash
mysql -u root -p < database/schema.sql
```

2. Configurer la connexion MySQL dans:
- `app/config.php`

3. Lancer le serveur PHP local depuis la racine du projet:
```bash
php -S localhost:8000 -t public
```

4. Ouvrir:
- `http://localhost:8000/register.php`

## Notes techniques

- Les mots de passe sont hashés avec `password_hash()`.
- Les accès BD utilisent PDO + requêtes préparées.
- Le chat charge les messages automatiquement toutes les 3 secondes (`fetch_private.php`, `fetch_group.php`).

## Idées d'amélioration

- WebSocket pour temps réel instantané
- Upload de fichiers/images
- Statut en ligne/hors ligne
- Notifications de nouveaux messages
