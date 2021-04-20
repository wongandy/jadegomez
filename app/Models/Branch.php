<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'address', 'contact_number'];
    protected static $logName = 'Branch';

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName branch :subject.address";
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('quantity');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }
}
