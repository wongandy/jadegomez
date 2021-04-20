<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'contact_number'];
    protected static $logName = 'Supplier';

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName supplier :subject.name";
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
