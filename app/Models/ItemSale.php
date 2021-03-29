<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemSale extends Pivot
{
    use HasFactory;

    public function demo()
    {
        return $this->belongsTo(ItemPurchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
