<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleRevenueCash extends Model
{
    use HasFactory;

    protected $fillable = ['cash_denomination_id', 'pieces'];

    public function cashDenomination()
    {
        return $this->belongsTo(CashDenomination::class, 'cash_denomination_id');
    }
}
