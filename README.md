# 🏠 API de Gestion Immobilière

Une API RESTful robuste construite avec Laravel pour gérer des biens immobiliers avec un contrôle d'accès basé sur les rôles (Admin, Agent, Visiteur). Ce projet implémente une architecture propre en trois couches (Contrôleur → Service → Repository) suivant les meilleures pratiques de l'industrie.

---

## 📋 Table des matières
- [Fonctionnalités](#✨-fonctionnalités)
- [Stack Technique](#🛠-stack-technique)
- [Architecture](#🏗-architecture)
- [Installation](#🚀-installation)
- [Variables d'environnement](#🔧-variables-denvironnement)
- [Rôles et permissions](#👥-rôles-et-permissions)
- [Documentation API](#📚-documentation-api)
- [Exemples de requêtes](#🔍-exemples-de-requêtes)
- [Structure du projet](#📁-structure-du-projet)
- [Tests](#🧪-tests)
- [Fonctionnalités bonus](#🎯-fonctionnalités-bonus)

---

## ✨ Fonctionnalités

### Fonctionnalités principales
- ✅ Authentification via Laravel Sanctum (token-based)
- ✅ Contrôle d'accès basé sur les rôles (Admin, Agent, Visiteur)
- ✅ Opérations CRUD complètes pour les biens immobiliers
- ✅ Filtres avancés (ville, type, prix min/max, statut)
- ✅ Recherche full-text sur le titre et la description
- ✅ Upload d'images avec validation (taille, type)
- ✅ Génération automatique des titres basée sur les détails du bien

### Architecture
- ✅ Architecture en trois couches : Contrôleur → Service → Repository
- ✅ DTOs pour le transfert de données entre les couches
- ✅ Form Requests pour la validation
- ✅ API Resources pour le formatage des réponses
- ✅ Policies pour l'autorisation
- ✅ Soft deletes pour les biens (Bonus)

---

## 🛠 Stack Technique

- **Laravel** 
- **PHP** 
- **MySQL** 
- **Laravel Sanctum** (Authentification API)
- **Laravel Storage** (Gestion des fichiers)

---

## 🏗 Architecture
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Requête │────▶│ Contrôleur │────▶│ Service │────▶│ Repository │────▶ Base de données
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
│ │ │
Form Request DTO │
│ │ │
Validation Logique métier Requêtes DB



**Responsabilités des couches :**
- **Contrôleur** : Gère les requêtes/réponses HTTP, autorisation
- **Service** : Contient la logique métier, orchestre les repositories
- **Repository** : Interactions avec la base de données, requêtes, filtres
- **DTO** : Transfert de données entre les couches
- **Form Request** : Règles de validation
- **Resource** : Formatage des réponses JSON

---

## 🚀 Installation

### Prérequis
- PHP >= 
- Composer
- MySQL >= 
- Node.js & NPM (optionnel, pour le frontend)

### Installation pas à pas

```bash
# 1. Cloner le dépôt
git clone https://github.com/ikrame-zerrouki/property-api-laravel.git
cd property-api-laravel

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier d'environnement
cp .env.example .env

# 4. Générer la clé de l'application
php artisan key:generate

# 5. Configurer la base de données dans le fichier .env
# Modifier DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Exécuter les migrations et les seeders
php artisan migrate 

# 7. Créer le lien de stockage
php artisan storage:link

# 8. Démarrer le serveur de développement
php artisan serve
🔧 Variables d'environnement
Principales variables utilisées dans le projet :


# Configuration de l'application
APP_NAME="API Immobilière"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Configuration de la base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=real_estate_db
DB_USERNAME=root
DB_PASSWORD=

# Sanctum & Session
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost

# Pagination
PROPERTIES_PER_PAGE=15

# Système de fichiers
FILESYSTEM_DISK=public
👥 Rôles et permissions
Rôle	Créer	Lire	Modifier	Supprimer	Publier	Upload images
Admin	✅ Tous	✅ Tous	✅ Tous	✅ Tous	✅ Tous	✅ Tous
Agent	✅	✅ Tous	✅ Ses biens	✅ Ses biens	✅ Ses biens	✅ Ses biens
Visiteur	❌	✅ Publiés seulement	❌	❌	❌	❌
Utilisateurs par défaut (via seeder)
Admin : admin@system.com / password


📚 Documentation API
Endpoints d'authentification
Méthode	Endpoint	Description	Accès
POST	/api/login	Connexion utilisateur	Public
POST	/api/register	Inscription utilisateur	Public
POST	/api/logout	Déconnexion	Auth
GET	/api/user	Infos utilisateur connecté	Auth
Endpoints des biens
Méthode	Endpoint	Description	Accès
GET	/api/properties	Liste tous les biens	Public
GET	/api/properties/{id}	Détails d'un bien	Public
POST	/api/properties	Créer un bien	Admin/Agent
PUT	/api/properties/{id}	Modifier un bien	Admin/Agent (propriétaire)
DELETE	/api/properties/{id}	Supprimer un bien	Admin/Agent (propriétaire)
PATCH	/api/properties/{id}/toggle-publish	Publier/Dépublier	Admin/Agent (propriétaire)
Endpoints des images
Méthode	Endpoint	Description	Accès
POST	/api/properties/{id}/images	Uploader des images	Admin/Agent (propriétaire)
GET	/api/properties/{id}/images	Voir les images d'un bien	Public
DELETE	/api/images/{id}	Supprimer une image	Admin/Agent (propriétaire)
POST	/api/images/bulk-delete	Supprimer plusieurs images	Admin/Agent (propriétaire)
PATCH	/api/images/{id}/set-primary	Définir image principale	Admin/Agent (propriétaire)
🔍 Exemples de requêtes
1. Connexion utilisateur
bash
POST /api/login
Content-Type: application/json

{
    "email": "admin@system.com",
    "password": "password"
}
Réponse :

json
{
    "success": true,
    "message": "Connexion réussie",
    "token": "1|laravel_sanctum_token_here",
    "user": {
        "id": 2,
        "name": "Agent User",
        "email": "admin@system.com",
        "role": "admin"
    }
}
2. Créer un bien (avec images)
bash
POST /api/properties
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "type": "appartement",
    "pieces": 4,
    "surface": 120.5,
    "prix": 2500000,
    "ville": "Alger",
    "description": "Bel appartement en plein centre",
    "statut": "disponible",
    "is_published": true,
    "images[]": @file1.jpg,
    "images[]": @file2.jpg
}
Réponse :

json
{
    "success": true,
    "message": "Bien créé avec succès",
    "data": {
        "id": 1,
        "title": "Appartement 4 pièces à Alger",
        "type": "appartement",
        "pieces": 4,
        "surface": 120.5,
        "prix": 2500000,
        "ville": "Alger",
        "images": [...]
    }
}
3. Filtrer les biens
bash
GET /api/properties?ville=Alger&type=appartement&prix_min=1000000&prix_max=3000000&statut=disponible&search=beau&per_page=10
Réponse :

json
{
    "success": true,
    "data": [...],
    "pagination": {
        "total": 25,
        "per_page": 10,
        "current_page": 1,
        "last_page": 3
    }
}
4. Uploader des images
bash
POST /api/properties/1/images
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "images[]": @image1.jpg,
    "images[]": @image2.jpg
}
Réponse :

json
{
    "success": true,
    "message": "2 image(s) uploadée(s) avec succès",
    "data": [
        {
            "id": 1,
            "path": "/storage/properties/1/image1.jpg",
            "url": "http://localhost/storage/properties/1/image1.jpg"
        },
        {
            "id": 2,
            "path": "/storage/properties/1/image2.jpg",
            "url": "http://localhost/storage/properties/1/image2.jpg"
        }
    ]
}
📁 Structure du projet
text
📦 project-root
├── 📂 app
│   ├── 📂 Http
│   │   ├── 📂 Controllers
│   │   │   ├── Api
│   │   │   │   └── AuthController.php
│   │   │   ├── PropertyController.php
│   │   │   └── ImageController.php
│   │   ├── 📂 Requests
│   │   │   ├── StorePropertyRequest.php
│   │   │   └── UploadImageRequest.php
│   │   └── 📂 Resources
│   │       ├── PropertyResource.php
│   │       └── ImageResource.php
│   ├── 📂 Models
│   │   ├── User.php
│   │   ├── Property.php
│   │   └── Image.php
│   ├── 📂 Repositories
│   │   ├── PropertyRepository.php
│   │   └── ImageRepository.php
│   ├── 📂 Services
│   │   ├── PropertyService.php
│   │   └── ImageService.php
│   ├── 📂 DTOs
│   │   ├── CreatePropertyDTO.php
│   │   ├── UpdatePropertyDTO.php
│   │   └── PropertyFilterDTO.php
│   ├── 📂 Policies
│   │   └── PropertyPolicy.php
│   └── 📂 Providers
│       └── AuthServiceProvider.php
├── 📂 database
│   ├── 📂 migrations
│   │   ├── create_properties_table.php
│   │   ├── create_images_table.php
│   │   └── add_role_to_users_table.php
│   └── 📂 seeders
│       └── DatabaseSeeder.php
├── 📂 routes
│   └── api.php
├── .env.example
└── README.md
🧪 Tests
Exécuter les tests avec PHPUnit :

bash
# Exécuter tous les tests
php artisan test

# Exécuter un test spécifique
php artisan test --filter PropertyTest
🎯 Fonctionnalités bonus implémentées
✅ Soft Deletes pour les biens

✅ Recherche full-text sur le titre et la description

✅ Suppression groupée d'images

✅ Sélection d'image principale

✅ Réponses formatées avec codes de statut appropriés

✅ Pagination avec valeurs personnalisables par page

✅ Filtres avancés avec critères multiples

📝 Licence
Ce projet a été créé à des fins d'évaluation technique pour Digitup Company.

👨‍💻 Auteur
Ikram Zerrouki
📧 Email : ikramzerrouki670@gmail.com
🐙 GitHub : @ikrame-zerrouki

🙏 Remerciements
Digitup Company pour l'opportunité

La communauté Laravel pour l'excellente documentation

✅ Projet réalisé en : Mars 2026































Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
