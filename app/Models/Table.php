<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Table extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'restaurant_id',
        'name',
        'min_people_count',
        'max_people_count',
    ];


    protected $casts = [
        'min_people_count' => 'integer',
        'max_people_count' => 'integer',
    ];


    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }


    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'reservation_tables')
                    ->withTimestamps();
    }
}
