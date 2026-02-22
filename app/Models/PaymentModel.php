<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
use App\Models\Month;
use App\Models\YearModel;

class PaymentModel extends Model
{
    protected $fillable = [
        'tenant_id',
        'month_id',
        'year_id',
        'amount',
        'payment_date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function months()
    {
        return $this->belongsTo(\App\Models\Month::class, 'month_id');
    }

    public function years()
    {
        return $this->belongsTo(YearModel::class, 'year_id');
    }
}
