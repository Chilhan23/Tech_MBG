<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'waktu_ambil',
        'waktu_kembali',
    ];

    protected $casts = [
        'waktu_ambil'   => 'datetime',
        'waktu_kembali' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}