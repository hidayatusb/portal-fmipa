<?php

namespace App\Enums;

enum UserApprovalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Review',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
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
