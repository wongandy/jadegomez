<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Change extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'user_id',
        'sale_id',
        'number',
        'change_number',
        'status',
        'change_total',
    ];

    protected static $logName = 'Changes';

    public $type = "Item change";

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_change')->withPivot('item_purchase_id');
    }

    public function replacements()
    {
        return $this->belongsToMany(Item::class, 'item_change_replacement')->withPivot('item_purchase_id');
    }

    public function returnedItems()
    {
        return $this->belongsToMany(Item::class, 'item_change')->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name,
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_change.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_change.id', 'ASC')
        ->groupBy('item_change.item_id', 'item_change.change_id');
    }

    public function changedItems()
    {
        return $this->belongsToMany(Item::class, 'item_change_replacement')->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name,
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_change_replacement.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_change_replacement.id', 'ASC')
        ->groupBy('item_change_replacement.item_id', 'item_change_replacement.change_id');
    }

    public function itemChange()
    {
        return $this->belongsToMany(Item::class, 'item_change')->select(
                [
                    DB::raw("COUNT(*) as quantity"),
                    DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"),
                    'items.name',
                    'items.upc',
                    'items.id',
                    'items.with_serial_number',
                ]
            )
            ->withPivot('item_purchase_id')
            ->join('item_purchase', 'item_change.item_purchase_id', '=', 'item_purchase.id')
            ->orderBy('item_change.id', 'ASC')
            ->groupBy('item_change.item_id', 'item_change.sale_id');
    }

    public function itemChangeReplacement()
    {
        return $this->belongsToMany(Item::class, 'item_change_replacement')->select(
                [
                    DB::raw("COUNT(*) as quantity"),
                    DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"),
                    'items.name',
                    'items.upc',
                    'items.id',
                    'items.with_serial_number',
                ]
            )
            ->withPivot('item_purchase_id')
            ->join('item_purchase', 'item_change_replacement.item_purchase_id', '=', 'item_purchase.id')
            ->orderBy('item_change_replacement.id', 'ASC')
            ->groupBy('item_change_replacement.item_id');
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
