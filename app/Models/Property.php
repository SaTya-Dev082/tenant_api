<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

class Property extends Model
{
    protected $fillable = [
        'tenant_id',
        'room_rent',
        'water',
        'electricity',
        'trash',
        'moto_parking',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
