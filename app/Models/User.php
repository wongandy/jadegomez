<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissionsTrait, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'branch_id'];
    protected static $logName = 'User';

    public function getDescriptionForEvent(string $eventName): string
    {
        return ":causer.name $eventName user :subject.name";
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleApproved()
    {
        return $this->hasMany(Sale::class, 'approved_by');
    }

    public function saleApprovedOn()
    {
        return $this->hasMany(Sale::class, 'end_of_day_at', '2021-02-25 16:13:46');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
        // return $this->belongsToMany(Role::class)->select('role_id', 'user_id', 'role_user.name')->using(RoleUser::class);
    }
}
