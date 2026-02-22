<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PracticeStatus: string implements HasLabel, HasColor
{
    case ISTRUTTORIA = 'istruttoria';
    case DELIBERATA = 'deliberata';
    case EROGATA = 'erogata';
    case RESPINTA = 'respinta';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ISTRUTTORIA => 'In Istruttoria',
            self::DELIBERATA => 'Deliberata',
            self::EROGATA => 'Erogata',
            self::RESPINTA => 'Respinta',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ISTRUTTORIA => 'warning',
            self::DELIBERATA => 'info',
            self::EROGATA => 'success',
            self::RESPINTA => 'danger',
        };
    }
}
