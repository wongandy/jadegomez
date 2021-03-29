<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'supplier_id', 'user_id', 'number', 'purchase_number', 'status'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->select([DB::raw("COUNT(*) AS quantity"), DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"), 'items.id', 'items.name', 'status'])->withPivot(['cost_price', 'status', 'serial_number'])->as('show')->groupBy('item_id', 'purchase_id');
    }

    // public function items()
    // {
    //     return $this->belongsToMany(Item::class)->withPivot('status');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasSoldItem()
    {
        return $this->belongsToMany(Item::class)->wherePivot('status', 'sold');
    }
}
