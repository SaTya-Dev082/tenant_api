<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Payment;

class Room extends Model
{
    protected $fillable = [
        'owner_id',
        'room_number',
        'description',
        'price',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class, 'room_id');
    }

    public function property()
    {
        return $this->hasOne(Property::class, 'room_id');
    }
    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'room_id');
    }
}
