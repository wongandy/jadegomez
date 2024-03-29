<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;

class Transfer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['sending_branch_id', 'user_id', 'receiving_branch_id', 'number', 'details', 'transfer_number', 'notes', 'status'];
    protected static $logName = 'Transfer';
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s A');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName transfer :subject.transfer_number";
    }

    public function items()
    {
        // return $this->belongsToMany(Item::class);

        return $this->belongsToMany(Item::class)->select(
            [DB::raw("COUNT(*) as quantity"), 
            DB::raw("CONCAT('(', GROUP_CONCAT(serial_number SEPARATOR ', '), ')') AS serial_number"), 
            'items.name', 
            'items.id', 
            'items.upc',
            'items.with_serial_number', 
            'items.selling_price'])
        ->join('item_purchase', 'item_transfer.item_purchase_id', '=', 'item_purchase.id')
        ->groupBy('item_transfer.item_id', 'item_transfer.transfer_id');
    }

    public function itemPurchaseId()
    {
        return $this->belongsToMany(Item::class)->withPivot('item_purchase_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function sendingBranch()
    {
        return $this->belongsTo(Branch::class, 'sending_branch_id');
    }

    public function receivingBranch()
    {
        return $this->belongsTo(Branch::class, 'receiving_branch_id');
    }

    public function test()
    {
        return $this->belongsToMany(Item::class)->withPivot('item_id', 'transfer_id', 'item_purchase_id');
    }
}
