<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model 
{
    # Fillable attributes for mass assignment
    protected $fillable = [
        'nisn',
        'name',
        'jurusan',
        'kelas_id', 
        'kelas',
        'jenis_kelamin',
    ];  
    public function absensis(){
        return $this->hasMany(Absensi::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
