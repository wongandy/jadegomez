<?php

namespace App\Models;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, LogsActivity;
    use HasEagerLimit;

    protected $fillable = ['name', 'details', 'upc', 'selling_price', 'with_serial_number'];
    protected static $logName = 'Item';

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName item :subject.name";
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class)->as('remaining')->withPivot('quantity');
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class)->withPivot('id', 'branch_id', 'cost_price', 'serial_number');
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class)->withPivot('item_id', 'sale_id', 'branch_id', 'item_purchase_id')->take(5);
        // return $this->belongsToMany(Sale::class)->using(ItemSale::class)->take(5);
    }

    public function refunds()
    {
        return $this->belongsToMany(Refund::class);
    }

    public function defectives()
    {
        return $this->belongsToMany(Defective::class);
    }

    public function allSoldItems()
    {
        return $this->belongsToMany(Sale::class)
                    ->select('item_sale.item_id', 'item_purchase.id AS item_purchase_id', 'serial_number')
                    ->join('item_purchase', 'item_sale.item_purchase_id', '=', 'item_purchase.id');
    }

    public function remainingSoldItems()
    {
        return $this->belongsToMany(Sale::class)
                    ->select('item_sale.item_id', 'item_purchase.id AS item_purchase_id', 'serial_number')
                    ->join('item_purchase', 'item_sale.item_purchase_id', '=', 'item_purchase.id');
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
