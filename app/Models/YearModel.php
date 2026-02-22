<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentModel;

class YearModel extends Model
{
    protected $fillable = [
        'year',
    ];

    public $hidden = [
        "created_at",
        "updated_at"
    ];
    public function payments()
    {
        return $this->hasMany(PaymentModel::class, 'year_id');
    }
}
