<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'reservation_name',
        'reservation_surname',
        'email',
        'phone',
        'reservation_date',
        'duration_hours',
    ];


    protected $casts = [
        'reservation_date' => 'datetime',
        'duration_hours' => 'integer',
    ];


    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }


    public function guests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }


    public function tables(): BelongsToMany
    {
        return $this->belongsToMany(Table::class, 'reservation_tables')
                    ->withTimestamps();
    }


    public function getReservationEndTimeAttribute(): Carbon
    {
        return $this->reservation_date->addHours($this->duration_hours);
    }


    public function getTotalPeopleCountAttribute(): int
    {
        return $this->guests()->count() + 1;
    }
}
