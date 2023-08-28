<?php

namespace App\Models;

use App\Models\User;
use App\Models\Branch;
use DateTimeInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, LogsActivity;
    use HasEagerLimit;

    protected $fillable = ['branch_id', 'supplier_id', 'user_id', 'number', 'details', 'purchase_number', 'status'];
    protected static $logName = 'Purchase';
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName purchase :subject.purchase_number";
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->select(
            [DB::raw("COUNT(*) AS quantity"),
            DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"),
            'items.id',
            'items.name', 
            'status'])->withPivot([
                'cost_price', 
                'status', 
                'serial_number'])->as('show')
            ->groupBy('item_id', 'purchase_id');
    }

    // public function items()
    // {
    //     return $this->belongsToMany(Item::class)->withPivot('status');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
