<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleRevenueExpense extends Model
{
    use HasFactory;

    protected $fillable = ['detail', 'amount'];
}
