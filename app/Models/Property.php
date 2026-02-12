<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

class Property extends Model
{
    protected $fillable = [
        'room_id',
        'room_rent',
        'water',
        'electricity',
        'trash',
        'parking',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
