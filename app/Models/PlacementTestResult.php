<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementTestResult extends Model
{
    protected $primaryKey = 'nim';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'nilai',
        'hasil',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function isLulus(): bool
    {
        return strtolower($this->hasil) === 'lulus';
    }
}
