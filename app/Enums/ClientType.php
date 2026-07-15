<?php

namespace App\Enums;

enum ClientType: string
{
    case GOVERNMENT = 'government';
    case RAILWAY = 'railway';
    case PSU = 'psu';
    case UNIVERSITY = 'university';
    case HOSPITAL = 'hospital';
    case PRIVATE = 'private';
    case NGO = 'ngo';

    public function label(): string
    {
        return match ($this) {
            self::GOVERNMENT => 'Government',
            self::RAILWAY => 'Railway',
            self::PSU => 'PSU',
            self::UNIVERSITY => 'University',
            self::HOSPITAL => 'Hospital',
            self::PRIVATE => 'Private',
            self::NGO => 'NGO',
        };
    }
}
