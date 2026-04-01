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
        'kelas',
        'jenis_kelamin',
    ];  
    
}
