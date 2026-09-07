<?php

namespace App\Enums;

enum BimbinganApprovalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Persetujuan',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'ki-time',
            self::Approved => 'ki-check',
            self::Rejected => 'ki-cross',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'kt-badge-warning',
            self::Approved => 'kt-badge-success',
            self::Rejected => 'kt-badge-destructive',
        };
    }
}
