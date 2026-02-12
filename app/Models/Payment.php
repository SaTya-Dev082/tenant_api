<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
use App\Models\PaymentPeriod;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'month',
        'year',
        'amount',
        'payment_date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
