<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use DateTimeInterface;

class Defective extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_id',
        'user_id',
        'sale_id',
        'number',
        'defective_number',
        'status',
    ];

    protected static $logName = 'Defect';

    public $type = "Item replacement";

    public function getStatusAttribute($value)
    {
        if ($value == 'active') {
            return 'defective item';
        }

        return $value;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName item defective :subject.refund_number";
    }

    public function test()
    {
        return $this->belongsToMany(Item::class, 'item_defective')->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name, ' at ', FORMAT(item_defective.sold_price, 2),
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_defective.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_defective.id', 'ASC')
        ->groupBy('item_defective.item_id', 'item_defective.defective_id');
    }

    public function declaredDefective()
    {
        return $this->belongsToMany(Item::class, 'item_defective')->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name,
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_defective.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_defective.id', 'ASC')
        ->groupBy('item_defective.item_id', 'item_defective.defective_id');
    }

    public function defectiveReplacement()
    {
        return $this->belongsToMany(Item::class, 'item_defective_replacement')->select([
            'name',
            'serial_number',
            DB::raw("
                CONCAT(COUNT(*), ' x ', items.name,
                IF (items.with_serial_number, CONCAT('<br>', '(', GROUP_CONCAT(item_purchase.serial_number SEPARATOR ', '), ')<br><br>'), '<br><br>')) 
                AS detail")
        ])
        ->join('item_purchase', 'item_defective_replacement.item_purchase_id', '=', 'item_purchase.id')
        ->orderBy('item_defective_replacement.id', 'ASC')
        ->groupBy('item_defective_replacement.item_id', 'item_defective_replacement.defective_id');
    }

    public function item()
    {
        return $this->belongsToMany(Item::class, 'item_defective')->withPivot('item_purchase_id');
    }

    public function itemDefectiveReplacements()
    {
        return $this->belongsToMany(Item::class, 'item_defective_replacement')->withPivot('item_purchase_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_defective')->select(
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
            ->join('item_purchase', 'item_defective.item_purchase_id', '=', 'item_purchase.id')
            ->orderBy('item_defective.id', 'ASC')
            ->groupBy('item_defective.item_id', 'item_defective.sale_id');
    }

    public function items2()
    {
        return $this->belongsToMany(Item::class, 'item_defective_replacement')->select(
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
            ->join('item_purchase', 'item_defective_replacement.item_purchase_id', '=', 'item_purchase.id')
            ->orderBy('item_defective_replacement.id', 'ASC')
            ->groupBy('item_defective_replacement.item_id');
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
