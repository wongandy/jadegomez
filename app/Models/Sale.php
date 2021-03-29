<?php

namespace App\Models;

use App\Models\Item;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'branch_id', 'user_id', 'sale_number', 'number', 'gross_total', 'discount', 'net_total', 'status'];

    public function items()
    {
        return $this->belongsToMany(Item::class)->select(
            [DB::raw("COUNT(*) as quantity"), 
            DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"), 
            'items.name', 
            'items.upc',
            'items.id',
            'items.with_serial_number', 
            'items.selling_price', 
            'item_sale.sold_price',
            'item_sale.item_purchase_id'])
        ->join('item_purchase', 'item_sale.item_purchase_id', '=', 'item_purchase.id')
        ->groupBy('item_sale.item_id', 'item_sale.sale_id');
    }

    public function itemPurchaseId()
    {
        return $this->belongsToMany(Item::class)->withPivot('item_purchase_id');
    }
    public function qty()
    {
        return $this->hasMany(ItemSale::class)->groupBy('item_purchase_id');
    }

    public function updateSaleStatusToPaid()
    {
        return $this->belongsToMany(Item::class)->select('item_purchase.id', 'item_purchase.status')->join('item_purchase', 'item_sale.item_purchase_id', '=', 'item_purchase.id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
