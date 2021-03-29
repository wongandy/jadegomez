<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BranchItem extends Pivot
{
    protected $fillable = ['branch_id', 'item_id', 'quantity'];
    // public function branch()
    // {
    //     return $this->belongsTo(Branch::class);
    // }

    // public function item()
    // {
    //     return $this->belongsTo(Branch::class);
    // }

    // public function supplier()
    // {
    //     return $this->belongsTo(Supplier::class);
    // }
}
