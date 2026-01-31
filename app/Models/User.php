<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pengadilan_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ✅ Relationship dengan pengadilan
    public function pengadilan()
    {
        return $this->belongsTo(Pengadilan::class, 'pengadilan_id');
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
}