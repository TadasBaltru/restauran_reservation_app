# 🍽️ Restaurant Reservation App

A modern, full-featured restaurant reservation management system built with Laravel 11, Livewire 3, and MySQL.

## ✨ Features

- **Restaurant Management**: Create and manage multiple restaurants
- **Table Management**: Configure tables with different capacities
- **Smart Reservations**: Intelligent table allocation system
- **Guest Management**: Track reservation guests
- **User Authentication**: Secure admin and user roles
- **Real-time UI**: Interactive interface with Livewire
- **Fully Dockerized**: Easy setup with Laravel Sail
- **Comprehensive Testing**: Feature and unit tests included

## 🚀 Quick Start

This application is fully dockerized using Laravel Sail. Get started in minutes!

### Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Git

### Installation

```bash
# Clone the repository
git clone <your-repo-url>
cd restauran_reservation_app

# Copy environment configuration
copy env.example.reference .env

# Install dependencies via Docker
docker run --rm -v "%cd%:/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs

# Start Docker containers
./vendor/bin/sail up -d

# Setup application
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed

# Install frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

🎉 **Access your app**: http://localhost

📚 **Full Documentation**: 
- [Quick Start Guide](DOCKER_QUICKSTART.md)
- [Complete Docker Setup Guide](DOCKER_SETUP.md)
- [System Architecture](RESTAURANT_MANAGEMENT_SYSTEM.md)

## 🧪 Testing

```bash
# Run all tests
./vendor/bin/sail artisan test

# Run with coverage
./vendor/bin/sail artisan test --coverage
```

## 📊 Database

The app uses two MySQL databases:
- **Production**: `restauran_reservation_app`
- **Testing**: `testing` (auto-created)

Connection details:
- **Host**: localhost
- **Port**: 3306
- **Username**: sail
- **Password**: password

## 🛠️ Technology Stack

- **Backend**: Laravel 11 (PHP 8.4)
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Database**: MySQL 8.0
- **Container**: Docker + Laravel Sail
- **Testing**: PHPUnit

## 📁 Project Structure

```
├── app/
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── Http/              # Controllers & middleware
├── database/
│   ├── migrations/        # Database migrations
│   ├── factories/         # Model factories
│   └── seeders/           # Database seeders
├── resources/
│   └── views/             # Blade templates
├── tests/
│   ├── Feature/           # Feature tests
│   └── Unit/              # Unit tests
├── compose.yaml           # Docker configuration
└── DOCKER_SETUP.md        # Docker documentation
```

## 📝 Common Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test

# Access MySQL CLI
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs -f
```

## 🔒 Security

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

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

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
