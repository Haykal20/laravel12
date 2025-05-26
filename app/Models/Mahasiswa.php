<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'semester',
        'mata_kuliah',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
