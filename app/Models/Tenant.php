<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Property;

class Tenant extends Model
{
    protected $fillable = [
        'room_id',
        'name',
        'email',
        'phone_number',
        'start_date',
        'end_date',
        'image_path',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }
}
