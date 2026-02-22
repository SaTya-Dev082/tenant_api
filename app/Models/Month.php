<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentModel;

class Month extends Model
{
    protected $fillable = [
        'name',
    ];

    public $hidden = [
        "created_at",
        "updated_at"
    ];
    public function payments()
    {
        return $this->hasMany(PaymentModel::class, 'month_id');
    }
}
