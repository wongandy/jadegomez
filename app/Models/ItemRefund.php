<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRefund extends Pivot
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
}
