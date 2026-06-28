<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Dosen = 'dosen';
    case Mahasiswa = 'mahasiswa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Dosen => 'Dosen',
            self::Mahasiswa => 'Mahasiswa',
        };
    }
}
