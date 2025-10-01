# 🍽️ Restaurant Reservation Management System

A modern, intelligent restaurant reservation management system built with **Laravel 12**, **Livewire 3**, and **Alpine.js**. Features an advanced table allocation algorithm that optimally assigns tables based on party size, availability, and multiple optimization strategies.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)

---

## ✨ Features

### 🎯 Core Functionality
- **Smart Table Allocation**: Intelligent algorithm with multiple optimization strategies
  - Perfect single table matching
  - Optimal multi-table combinations using backtracking
  - Minimum waste seat allocation
- **Public Reservation System**: User-friendly interface for making reservations
- **Multi-Guest Support**: Add unlimited guests to reservations
- **Real-time Availability Checking**: Instant feedback on table availability
- **Conflict Prevention**: Automatic detection of overlapping reservations

### 🏢 Restaurant Management (Admin)
- **Full CRUD Operations**: Create, read, update, and delete restaurants
- **Dynamic Table Management**: Add/remove tables on-the-fly with Livewire
- **Capacity Analytics**: View total tables and maximum capacity per restaurant
- **Responsive Admin Dashboard**: Modern, intuitive interface

### 🔒 Security & Authorization
- **Role-based Access Control**: Admin middleware for restaurant management
- **Laravel Breeze Authentication**: Secure user authentication out of the box
- **CSRF Protection**: All forms protected against cross-site request forgery
- **SQL Injection Prevention**: Eloquent ORM for safe database queries

### 🎨 Modern UI/UX
- **Responsive Design**: Mobile-first approach with TailwindCSS
- **Real-time Interactions**: Livewire components for seamless user experience
- **Alpine.js Enhancements**: Lightweight client-side interactivity
- **Loading States**: Visual feedback during operations
- **Toast Notifications**: User-friendly success/error messages

---

## 🚀 Quick Start

### Prerequisites
- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
- **Git**

### Installation

```bash
# 1. Clone the repository
git clone <https://github.com/TadasBaltru/restauran_reservation_app>
cd restauran_reservation_app

# 2. Copy environment configuration

# Linux/Mac
cp .env.example .env

# 3. Install PHP dependencies via Docker
docker run --rm -v $PWD:/var/www/html -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs

# 4. Start Docker containers
./vendor/bin/sail up -d

# 5. Generate application key
./vendor/bin/sail artisan key:generate

# 6. Run database migrations
./vendor/bin/sail artisan migrate

# 7. Seed sample data
./vendor/bin/sail artisan db:seed

# 8. Install frontend dependencies
./vendor/bin/sail npm install

# 9. Build frontend assets
./vendor/bin/sail npm run dev
```

### Access the Application

🎉 **Public Reservation Page**: http://localhost  
🔐 **Admin Dashboard**: http://localhost/restaurants

**Default Admin Credentials** (created by seeder):
- Email: `admin@restaurant.com`
- Password: `password`

---


## 🏗️ Architecture

### Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend Framework** | Laravel | 12.x |
| **Frontend Framework** | Livewire | 3.6+ |
| **JavaScript** | Alpine.js | ~3.x |
| **PHP Version** | PHP | 8.2+ |
| **Database** | MySQL | 8.0 |
| **Development** | Laravel Sail | Docker |

### Project Structure

```
restauran_reservation_app/
├── app/
│   ├── Console/
│   │   └── Commands/              # Artisan commands
│   ├── Http/
│   │   ├── Controllers/           # HTTP controllers
│   │   ├── Middleware/            # Custom middleware (AdminMiddleware)
│   │   └── Requests/              # Form validation requests
│   ├── Livewire/
│   │   ├── Reservation/           # Reservation Livewire components
│   │   │   ├── Create.php         # Public reservation form
│   │   │   ├── Index.php          # User's reservations list
│   │   │   └── Show.php           # Reservation details
│   │   └── Restaurant/            # Restaurant management components
│   │       ├── Create.php         # Create restaurant
│   │       ├── Edit.php           # Edit restaurant
│   │       ├── Index.php          # List restaurants
│   │       └── Show.php           # Restaurant details
│   ├── Models/
│   │   ├── Reservation.php        # Reservation model
│   │   ├── ReservationGuest.php   # Guest model
│   │   ├── Restaurant.php         # Restaurant model
│   │   ├── Table.php              # Table model
│   │   └── User.php               # User model
│   └── Services/
│       ├── ReservationService.php        # Reservation business logic
│       ├── RestaurantService.php         # Restaurant business logic
│       └── TableAllocationService.php    # Smart allocation algorithm
├── database/
│   ├── factories/                 # Model factories for testing
│   ├── migrations/                # Database schema migrations
│   └── seeders/                   # Database seeders
│       ├── DatabaseSeeder.php
│       └── RestaurantSeeder.php   # Sample restaurants & admin user
├── resources/
│   ├── css/
│   │   └── app.css                # TailwindCSS styles
│   ├── js/
│   │   ├── app.js                 # Main JavaScript entry
│   │   └── bootstrap.js           # Laravel Echo, Axios config
│   └── views/
│       ├── components/            # Blade components
│       ├── layouts/
│       │   ├── app.blade.php      # Authenticated layout
│       │   ├── guest.blade.php    # Guest layout
│       │   └── public.blade.php   # Public layout
│       └── livewire/              # Livewire component views
├── routes/
│   ├── auth.php                   # Authentication routes
│   ├── console.php                # Artisan commands
│   └── web.php                    # Web routes
├── tests/
│   ├── Feature/
│   │   ├── Auth/                  # Authentication tests
│   │   └── ReservationAllocationTest.php  # Allocation algorithm tests
│   └── Unit/
│       ├── RestaurantTest.php
│       ├── RestaurantValidationTest.php
│       └── TableAllocationServiceTest.php
├── compose.yaml                   # Docker Compose configuration
└── phpunit.xml                    # PHPUnit configuration
```

### Database Schema

```sql
restaurants
├── id
├── name
└── timestamps

tables
├── id
├── restaurant_id (FK)
├── name
├── min_people_count
├── max_people_count
└── timestamps

reservations
├── id
├── restaurant_id (FK)
├── reservation_name
├── reservation_surname
├── email
├── phone
├── reservation_date (datetime)
├── duration_hours
└── timestamps

reservation_guests
├── id
├── reservation_id (FK)
├── name
├── surname
├── email (nullable)
└── timestamps

reservation_tables (pivot)
├── reservation_id (FK)
├── table_id (FK)
└── timestamps

users
├── id
├── name
├── email
├── password
├── is_admin (boolean)
└── timestamps
```

---

## 🧠 Smart Table Allocation Algorithm

The `TableAllocationService` implements a sophisticated multi-strategy allocation algorithm:

### Allocation Strategies

1. **Perfect Single Match** ⭐
   - Finds a single table where `max_people_count == party_size`
   - Zero wasted seats
   - Fastest allocation

2. **Optimal Combination** 🎯
   - Uses backtracking algorithm to find best multi-table combination
   - Minimizes number of tables used
   - Minimizes wasted seats
   - Considers all viable combinations

3. **Minimum Single Match** 💡
   - Fallback strategy
   - Finds single table where `min_people_count == party_size`
   - Accepts some seat waste for simplicity

### Conflict Detection

- Checks for overlapping reservations
- Considers reservation duration
- Prevents double-booking automatically

### Algorithm Features

- **Time Complexity**: Optimized with pruning strategies
- **Space Complexity**: Efficient backtracking with minimal memory overhead
- **Availability Filtering**: Pre-filters available tables before allocation
- **Capacity Validation**: Ensures `min_people_count ≤ party_size ≤ max_people_count`

---

## 🧪 Testing
 .env.testing file must also be prepared, current setup is using seperate mysql databases
### Run All Tests

```bash
# Run the complete test suite
./vendor/bin/sail artisan test


### Test Coverage

- ✅ **Feature Tests**: End-to-end reservation flow
- ✅ **Unit Tests**: Service layer business logic
- ✅ **Allocation Tests**: All allocation strategies
- ✅ **Conflict Tests**: Overlapping reservation handling
- ✅ **Edge Cases**: Single person, large parties, no availability

### Key Test Files

- `tests/Feature/ReservationAllocationTest.php` - Core allocation scenarios
- `tests/Unit/TableAllocationServiceTest.php` - Algorithm unit tests
- `tests/Unit/RestaurantTest.php` - Restaurant model tests
- `tests/Unit/RestaurantValidationTest.php` - Validation rules

---

## 💻 Development

### Available Commands

```bash
# Start development environment
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run Artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan tinker

# Frontend development
./vendor/bin/sail npm run dev        # Watch for changes
./vendor/bin/sail npm run build      # Production build

# Code quality
./vendor/bin/sail composer lint      # Fix code style (PSR-12)
./vendor/bin/sail artisan test       # Run tests

# Database
./vendor/bin/sail mysql              # MySQL CLI
./vendor/bin/sail artisan migrate:fresh --seed  # Reset & seed
```

## 📊 Usage Examples

### Making a Reservation (Public)

1. Visit the homepage: `http://localhost`
2. Select a restaurant from the dropdown
3. Choose date and time
4. Add guests (optional)
5. Fill in contact information
6. Click "Check Availability" to verify table availability
7. Submit the reservation

### Managing Restaurants (Admin)

1. Navigate to `/login`
2. Navigate to `/restaurants`
3. Click "Add Restaurant"
4. Enter restaurant name
5. Add tables dynamically with Livewire
6. Set minimum and maximum capacity for each table
7. Save the restaurant

### Viewing Reservations
Admin can also view reservations at `/reservations`

---

## 🔧 Configuration


The application uses two databases:
- **Production**: `restauran_reservation_app`
- **Testing**: `testing` (auto-created during tests)



## 🐛 Debugging

### Laravel Debugbar

The project includes Laravel Debugbar for development:

```php
// Automatically enabled when APP_DEBUG=true
// Access at bottom of page or via /_debugbar
```

### Code Style

This project follows **PSR-12** coding standards:

```bash
./vendor/bin/sail composer lint
```
<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
</p>
