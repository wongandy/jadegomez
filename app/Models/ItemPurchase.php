<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ItemPurchase extends Pivot
{
    use HasFactory;

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function itemSale() {
        return $this->hasOne(ItemSale::class, 'item_purchase_id');
    }
}