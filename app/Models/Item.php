<?php

namespace App\Models;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'details', 'upc', 'selling_price', 'with_serial_number'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class)->as('remaining')->withPivot('quantity');
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class)->withPivot('id', 'branch_id', 'cost_price', 'serial_number');
    }

    public function hasPurchased()
    {
        return $this->belongsToMany(Purchase::class)->withPivot('id', 'branch_id', 'cost_price', 'serial_number');
    }

    public function transfers()
    {
        return $this->belongsToMany(Transfer::class);
    }
}
