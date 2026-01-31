<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'catatan',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'tanggal_putusan' => 'date',
        'verified_at' => 'datetime',
    ];

    // ✅ Relationship dengan pengadilan (table singular)
    public function pengadilan(): BelongsTo
    {
        return $this->belongsTo(Pengadilan::class, 'pengadilan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
