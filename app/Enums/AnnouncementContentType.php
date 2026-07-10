<?php

namespace App\Enums;

enum AnnouncementContentType: string
{
    case Text = 'text';
    case Url = 'url';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Teks',
            self::Url => 'URL',
        };
    }
}
