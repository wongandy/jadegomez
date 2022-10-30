<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleRevenueCheck extends Model
{
    use HasFactory;

    protected $fillable = ['bank_name', 'check_number', 'amount'];
}
