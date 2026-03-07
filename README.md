

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















🏠 Real Estate Management API
A robust RESTful API built with Laravel for managing real estate properties with role-based access control (Admin, Agent, Visitor). This project implements a clean three-layer architecture (Controller → Service → Repository) following industry best practices.

📋 Table of Contents
Features

Tech Stack

Architecture

Installation

Environment Variables

User Roles & Permissions

API Documentation

API Examples

Project Structure

Testing

Bonus Features

✨ Features
Core Features
✅ Authentication via Laravel Sanctum (token-based)

✅ Role-based access control (Admin, Agent, Visitor)

✅ Complete CRUD operations for properties

✅ Advanced filtering (city, type, price range, status)

✅ Full-text search on title and description

✅ Image upload with validation (size, type)

✅ Auto-generated titles based on property details

Architecture
✅ Three-layer architecture: Controller → Service → Repository

✅ DTOs for data transfer between layers

✅ Form Requests for validation

✅ API Resources for response formatting

✅ Policies for authorization

✅ Soft deletes for properties (Bonus)

🛠 Tech Stack
Laravel 10.x

PHP 8.1+

MySQL / MariaDB

Laravel Sanctum (API Authentication)

Laravel Storage (File handling)

🏗 Architecture
text
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Request   │────▶│  Controller │────▶│   Service   │────▶│  Repository │────▶ Database
└─────────────┘     └─────────────┘     └─────────────┘     └─────────────┘
                           │                    │                    │
                      Form Request              DTO                   │
                           │                    │                    │
                      Validation           Business Logic        DB Queries
Layer Responsibilities:
Controller: Handles HTTP requests/responses, authorization

Service: Contains business logic, orchestrates repositories

Repository: Database interactions, queries, filters

DTO: Data transfer between layers

Form Request: Validation rules

Resource: JSON response formatting

🚀 Installation
Prerequisites
PHP >= 8.1

Composer

MySQL >= 5.7

Node.js & NPM (optional, for frontend)

Step-by-step Installation
bash
# 1. Clone the repository
git clone https://github.com/yourusername/real-estate-api.git
cd real-estate-api

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env file
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Create storage link
php artisan storage:link

# 8. Start the development server
php artisan serve
🔧 Environment Variables
Key environment variables used in the project:

env
# App Configuration
APP_NAME="Real Estate API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
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

# Filesystem
FILESYSTEM_DISK=public
👥 User Roles & Permissions
Role	Create	Read	Update	Delete	Publish	Upload Images
Admin	✅ All	✅ All	✅ All	✅ All	✅ All	✅ All
Agent	✅	✅ All	✅ Own only	✅ Own only	✅ Own only	✅ Own only
Visitor	❌	✅ Published only	❌	❌	❌	❌
Default Users (from seeder)
Admin: admin@example.com / password

Agent: agent@example.com / password

Visitor: visitor@example.com / password

📚 API Documentation
Authentication Endpoints
Method	Endpoint	Description	Access
POST	/api/login	User login	Public
POST	/api/register	User registration	Public
POST	/api/logout	User logout	Auth
GET	/api/user	Get current user	Auth
Property Endpoints
Method	Endpoint	Description	Access
GET	/api/properties	List all properties	Public
GET	/api/properties/{id}	Get property details	Public
POST	/api/properties	Create new property	Admin/Agent
PUT	/api/properties/{id}	Update property	Admin/Agent (owner)
DELETE	/api/properties/{id}	Delete property	Admin/Agent (owner)
PATCH	/api/properties/{id}/toggle-publish	Publish/unpublish	Admin/Agent (owner)
Image Endpoints
Method	Endpoint	Description	Access
POST	/api/properties/{id}/images	Upload images	Admin/Agent (owner)
GET	/api/properties/{id}/images	Get property images	Public
DELETE	/api/images/{id}	Delete image	Admin/Agent (owner)
POST	/api/images/bulk-delete	Delete multiple images	Admin/Agent (owner)
PATCH	/api/images/{id}/set-primary	Set primary image	Admin/Agent (owner)
🔍 API Examples
1. User Login
bash
POST /api/login
Content-Type: application/json

{
    "email": "agent@example.com",
    "password": "password"
}
Response:

json
{
    "success": true,
    "message": "Login successful",
    "token": "1|laravel_sanctum_token_here",
    "user": {
        "id": 2,
        "name": "Agent User",
        "email": "agent@example.com",
        "role": "agent"
    }
}
2. Create Property (with images)
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
    "description": "Beautiful apartment in central location",
    "statut": "disponible",
    "is_published": true,
    "images[]": @file1.jpg,
    "images[]": @file2.jpg
}
Response:

json
{
    "success": true,
    "message": "Property created successfully",
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
3. Filter Properties
bash
GET /api/properties?ville=Alger&type=appartement&prix_min=1000000&prix_max=3000000&statut=disponible&search=beautiful&per_page=10
Response:

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
4. Upload Images
bash
POST /api/properties/1/images
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "images[]": @image1.jpg,
    "images[]": @image2.jpg
}
Response:

json
{
    "success": true,
    "message": "2 image(s) uploaded successfully",
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
📁 Project Structure
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
🧪 Testing
Run tests using PHPUnit:

bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter PropertyTest
🎯 Bonus Features Implemented
✅ Soft Deletes for properties

✅ Full-text search on title and description

✅ Bulk image delete functionality

✅ Primary image selection

✅ Formatted responses with proper status codes

✅ Pagination with customizable per-page values

✅ Advanced filtering with multiple criteria

📝 License
This project is created for technical assessment purposes at Digitup Company.

👨‍💻 Author
Your Name

Email: your.email@example.com

GitHub: @yourusername

🙏 Acknowledgments
Digitup Company for the opportunity

Laravel community for excellent documentation

📧 For questions or support: your.email@example.com

✅ Project completed on: March 2025
