<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JudulSkripsi extends Model
{
    protected $table = 'judul_skripsi';

    protected $fillable = [
        'nim',
        'nama_mahasiswa',
        'judul',
        'pembimbing1',
        'pembimbing2',
    ];
}
