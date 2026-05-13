<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'preferred_language',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function identities()
    {
        return $this->hasMany(PortalIdentity::class);
    }

    public function currentIdentity()
    {
        return $this->hasOne(PortalIdentity::class)->where('is_current', true)->latestOfMany();
    }

    public function notifications()
    {
        return $this->hasMany(PortalNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function timelineEvents()
    {
        return $this->hasMany(PortalTimelineEvent::class);
    }
}
