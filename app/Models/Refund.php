<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class Refund extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'user_id',
        'sale_id',
        'number',
        'refund_number',
        'status',
        'refund_total',
        'refund_total_for_reports'
    ];

    protected static $logName = 'Refund';

    public function getStatusAttribute($value)
    {
        if ($value == 'active') {
            return 'refund item';
        }

        return $value;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName item return :subject.refund_number";
    }

    public function refunded()
    {
        return $this->belongsToMany(Item::class)->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name, ' at ', FORMAT(item_refund.sold_price, 2),
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_refund.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_refund.id', 'ASC')
        ->groupBy('item_refund.item_id', 'item_refund.refund_id');
    }

    public function item()
    {
        return $this->belongsToMany(Item::class)->withPivot('item_purchase_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->select(
                [
                    DB::raw("COUNT(*) as quantity"),
                    DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"),
                    DB::raw("CONCAT(GROUP_CONCAT(serial_number SEPARATOR ', ')) AS serial_numbers"),
                    'items.name', 
                    'items.upc',
                    'items.id',
                    'items.with_serial_number',
                    'items.selling_price', 
                    'item_refund.sold_price'
                ]
            )
            ->withPivot('item_purchase_id')
            ->join('item_purchase', 'item_refund.item_purchase_id', '=', 'item_purchase.id')
            ->orderBy('item_refund.id', 'ASC')
            ->groupBy('item_refund.item_id', 'item_refund.sale_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
