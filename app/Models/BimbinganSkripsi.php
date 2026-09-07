<?php

namespace App\Models;

use App\Enums\BimbinganApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BimbinganSkripsi extends Model
{
    protected $table = 'bimbingan_skripsi';

    protected $fillable = [
        'mahasiswa_id',
        'pembimbing1_id',
        'pembimbing2_id',
        'pembimbing1_status',
        'pembimbing2_status',
        'pembimbing1_responded_at',
        'pembimbing2_responded_at',
    ];

    protected function casts(): array
    {
        return [
            'pembimbing1_status' => BimbinganApprovalStatus::class,
            'pembimbing2_status' => BimbinganApprovalStatus::class,
            'pembimbing1_responded_at' => 'datetime',
            'pembimbing2_responded_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function pembimbing1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing1_id');
    }

    public function pembimbing2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing2_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(LogbookBimbinganSkripsi::class, 'bimbingan_skripsi_id');
    }

    public function overallStatus(): BimbinganApprovalStatus
    {
        if (
            $this->pembimbing1_status === BimbinganApprovalStatus::Rejected
            || $this->pembimbing2_status === BimbinganApprovalStatus::Rejected
        ) {
            return BimbinganApprovalStatus::Rejected;
        }

        if (
            $this->pembimbing1_status === BimbinganApprovalStatus::Approved
            && $this->pembimbing2_status === BimbinganApprovalStatus::Approved
        ) {
            return BimbinganApprovalStatus::Approved;
        }

        return BimbinganApprovalStatus::Pending;
    }

    public function isFullyApproved(): bool
    {
        return $this->overallStatus() === BimbinganApprovalStatus::Approved;
    }

    public function hasAtLeastOneApproval(): bool
    {
        return $this->pembimbing1_status === BimbinganApprovalStatus::Approved
            || $this->pembimbing2_status === BimbinganApprovalStatus::Approved;
    }

    public function canUseLogbook(): bool
    {
        return $this->hasAtLeastOneApproval();
    }

    public function canBeResubmitted(): bool
    {
        return $this->overallStatus() === BimbinganApprovalStatus::Rejected;
    }

    /**
     * @return list<array{id: int, label: string}>
     */
    public function approvedPembimbingOptions(): array
    {
        $options = [];

        if ($this->pembimbing1_status === BimbinganApprovalStatus::Approved) {
            $options[] = [
                'id' => $this->pembimbing1_id,
                'label' => 'Pembimbing 1 — '.($this->pembimbing1?->name ?? 'Dosen'),
            ];
        }

        if ($this->pembimbing2_status === BimbinganApprovalStatus::Approved) {
            $options[] = [
                'id' => $this->pembimbing2_id,
                'label' => 'Pembimbing 2 — '.($this->pembimbing2?->name ?? 'Dosen'),
            ];
        }

        return $options;
    }

    public function statusForDosen(int $dosenId): ?BimbinganApprovalStatus
    {
        if ($this->pembimbing1_id === $dosenId) {
            return $this->pembimbing1_status;
        }

        if ($this->pembimbing2_id === $dosenId) {
            return $this->pembimbing2_status;
        }

        return null;
    }

    public function roleLabelForDosen(int $dosenId): ?string
    {
        if ($this->pembimbing1_id === $dosenId) {
            return 'Pembimbing 1';
        }

        if ($this->pembimbing2_id === $dosenId) {
            return 'Pembimbing 2';
        }

        return null;
    }

    public function respondAsDosen(int $dosenId, BimbinganApprovalStatus $status): void
    {
        if ($this->pembimbing1_id === $dosenId) {
            $this->update([
                'pembimbing1_status' => $status,
                'pembimbing1_responded_at' => now(),
            ]);

            return;
        }

        if ($this->pembimbing2_id === $dosenId) {
            $this->update([
                'pembimbing2_status' => $status,
                'pembimbing2_responded_at' => now(),
            ]);
        }
    }
}
