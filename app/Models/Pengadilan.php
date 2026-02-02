<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengadilan extends Model
{
    // ✅ Tentukan nama tabel secara eksplisit
    protected $table = 'pengadilan';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'wilayah',
        'telepon',
        'email'
    ];

    // Relationship dengan Upload
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    // Relationship dengan User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
