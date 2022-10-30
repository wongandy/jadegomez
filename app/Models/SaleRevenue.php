<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class SaleRevenue extends Model
{
    use HasFactory;

    protected $appends = ['total_cashes', 'total_checks', 'total_sales', 'total_expenses'];

    protected $fillable = ['branch_id', 'user_id', 'notes'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function getTotalCashesAttribute()
    {
        return $this->saleRevenueCashes->sum(function ($item) {
            return $item->cashDenomination->number * $item->pieces;
        });
    }

    public function getTotalChecksAttribute()
    {
        return $this->saleRevenueChecks->sum('amount');
    }

    public function getTotalExpensesAttribute()
    {
        return $this->saleRevenueExpenses->sum('amount');
    }

    public function getTotalSalesAttribute()
    {
        return $this->totalCashes + $this->totalChecks + $this->totalExpenses;
    }

    public function saleRevenueCashes()
    {
        return $this->hasMany(SaleRevenueCash::class);
    }

    public function saleRevenueChecks()
    {
        return $this->hasMany(SaleRevenueCheck::class);
    }

    public function saleRevenueExpenses()
    {
        return $this->hasMany(SaleRevenueExpense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
