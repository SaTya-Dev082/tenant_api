<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\Tenant;

class Payment extends Model
{
    protected $fillable = [
        'room_id',
        'tenant_id',
        'amount',
        'payment_date',
    ];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
