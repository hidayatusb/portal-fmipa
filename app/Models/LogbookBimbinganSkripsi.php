<?php

namespace App\Models;

use App\Enums\BimbinganApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookBimbinganSkripsi extends Model
{
    protected $table = 'logbook_bimbingan_skripsi';

    protected $fillable = [
        'bimbingan_skripsi_id',
        'pembimbing_id',
        'judul',
        'tanggal',
        'catatan',
        'status',
        'status_responded_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'status' => BimbinganApprovalStatus::class,
            'status_responded_at' => 'datetime',
        ];
    }

    public function bimbingan(): BelongsTo
    {
        return $this->belongsTo(BimbinganSkripsi::class, 'bimbingan_skripsi_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function isPending(): bool
    {
        return $this->status === BimbinganApprovalStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === BimbinganApprovalStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === BimbinganApprovalStatus::Rejected;
    }

    public function mahasiswaCanModify(): bool
    {
        return ! $this->isApproved();
    }
}
