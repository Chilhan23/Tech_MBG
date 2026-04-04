<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasLog extends Model
{
    protected $fillable = [
        'kelas_id',
        'tanggal',
        'diambil',
        'dikembalikan',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'diambil'      => 'datetime',
        'dikembalikan' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
