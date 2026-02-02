<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'pengadilan_id',
        'jenis_putusan',
        'nomor_perkara_pa',
        'nomor_perkara_banding',
        'nomor_perkara_kasasi',
        'nomor_perkara_pk',
        'tanggal_putusan',
        'file_path',
        'original_filename',
        'file_size',
        'status',
    ];

    protected $casts = [
        'tanggal_putusan' => 'date',
        'file_size' => 'integer'
    ];

    // Accessor untuk URL file
    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    // Accessor untuk path lengkap
    public function getFullPathAttribute()
    {
        return $this->file_path ? storage_path('app/public/' . $this->file_path) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengadilan()
    {
        return $this->belongsTo(Pengadilan::class);
    }
}
