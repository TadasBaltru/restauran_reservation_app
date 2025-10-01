# Restaurant Reservation System - Database Schema

## Overview
This database schema supports a restaurant reservation system where each table is unique (no count field). The system allows restaurants to manage their tables and reservations efficiently.

## Table Structure

### 1. restaurants
Stores basic restaurant information.
- **id**: Primary key
- **name**: Restaurant name
- **timestamps**: Created/updated timestamps

### 2. tables
Each restaurant has many unique tables with specific capacity.
- **id**: Primary key
- **restaurant_id**: Foreign key to restaurants
- **name**: Table identifier (e.g., "Table 1", "A1", "Patio-3")
- **min_people_count**: Minimum number of people the table can accommodate
- **max_people_count**: Maximum number of people the table can accommodate
- **unique constraint**: (restaurant_id, name) - ensures table names are unique within each restaurant
- **timestamps**: Created/updated timestamps

### 3. reservations
Stores reservation details made by customers.
- **id**: Primary key
- **restaurant_id**: Foreign key to restaurants
- **reservation_name**: First name of the person making the reservation
- **reservation_surname**: Last name of the person making the reservation
- **email**: Contact email
- **phone**: Contact phone number
- **reservation_date**: Date and time of the reservation
- **duration_hours**: How long the reservation is for (1, 2, 3, etc.)
- **timestamps**: Created/updated timestamps
- **indexes**: On (restaurant_id, reservation_date) for performance

### 4. reservation_guests
Additional guests for a reservation (beyond the person who made it).
- **id**: Primary key
- **reservation_id**: Foreign key to reservations
- **name**: Guest's first name
- **surname**: Guest's last name
- **email**: Guest's email (optional/nullable)
- **timestamps**: Created/updated timestamps

### 5. reservation_tables (Pivot Table)
Many-to-many relationship between reservations and tables.
- **id**: Primary key
- **reservation_id**: Foreign key to reservations
- **table_id**: Foreign key to tables
- **unique constraint**: (reservation_id, table_id) - prevents duplicate table assignments
- **timestamps**: Created/updated timestamps

## Relationships

### Restaurant → Tables (One-to-Many)
- One restaurant can have many tables
- Each table belongs to exactly one restaurant
- Tables are unique within a restaurant (same restaurant can't have two "Table 1"s)

### Restaurant → Reservations (One-to-Many)  
- One restaurant can have many reservations
- Each reservation is for exactly one restaurant

### Reservation → Guests (One-to-Many)
- One reservation can have multiple guests
- Each guest record belongs to exactly one reservation
- The person making the reservation is NOT stored in reservation_guests (they're in the reservation record itself)

### Reservation ↔ Tables (Many-to-Many)
- One reservation can span multiple tables (for large parties)
- One table can be used by multiple reservations (at different times)
- This relationship is managed through the reservation_tables pivot table

## Key Design Decisions

### 1. Unique Tables
- Each table has a unique identifier within a restaurant
- No "count" field - each physical table is represented individually
- This allows precise control over which specific tables are reserved

### 2. Person vs Guests
- The reservation holder (person making the booking) is stored directly in the reservations table
- Additional guests are stored separately in reservation_guests
- Total party size = 1 (reservation holder) + count of reservation_guests

### 3. Flexible Table Assignment
- Reservations can span multiple tables through the pivot table
- Useful for large parties that need multiple tables
- Tables can be reassigned or combined as needed

### 4. Time-based Conflicts
- Reservation conflicts are determined by:
  - Same table(s)
  - Overlapping time periods (reservation_date + duration_hours)

## Laravel Eloquent Models

### Relationships Defined
- **Restaurant**: hasMany(Table), hasMany(Reservation)
- **Table**: belongsTo(Restaurant), belongsToMany(Reservation)
- **Reservation**: belongsTo(Restaurant), hasMany(ReservationGuest), belongsToMany(Table)
- **ReservationGuest**: belongsTo(Reservation)

### Additional Features
- **Reservation model** includes helper methods:
  - `getReservationEndTimeAttribute()`: Calculates end time
  - `getTotalPeopleCountAttribute()`: Gets total party size including reservation holder

## Usage Examples

### Creating a Reservation
```php
// Create reservation for 2024-01-15 at 7 PM for 2 hours
$reservation = Reservation::create([
    'restaurant_id' => 1,
    'reservation_name' => 'John',
    'reservation_surname' => 'Doe', 
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'reservation_date' => '2024-01-15 19:00:00',
    'duration_hours' => 2
]);

// Add guests
$reservation->guests()->create([
    'name' => 'Jane',
    'surname' => 'Doe',
    'email' => 'jane@example.com'
]);

// Assign tables
$tables = Table::where('restaurant_id', 1)
    ->whereIn('name', ['Table 5', 'Table 6'])
    ->get();
$reservation->tables()->attach($tables);
```

### Checking Table Availability
```php
// Find available tables for 4 people on 2024-01-15 from 7-9 PM
$availableTables = Table::where('restaurant_id', 1)
    ->where('min_people_count', '<=', 4)
    ->where('max_people_count', '>=', 4)
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
            ->from('reservation_tables')
            ->join('reservations', 'reservation_tables.reservation_id', '=', 'reservations.id')
            ->whereColumn('reservation_tables.table_id', 'tables.id')
            ->where('reservations.reservation_date', '<=', '2024-01-15 21:00:00')
            ->whereRaw('DATE_ADD(reservations.reservation_date, INTERVAL reservations.duration_hours HOUR) > ?', ['2024-01-15 19:00:00']);
    })
    ->get();
```

## Running the Migrations

To set up the database:

```bash
php artisan migrate
```

The migrations are created in chronological order to handle foreign key dependencies:
1. `create_restaurants_table`
2. `create_tables_table` 
3. `create_reservations_table`
4. `create_reservation_guests_table`
5. `create_reservation_tables_table`







