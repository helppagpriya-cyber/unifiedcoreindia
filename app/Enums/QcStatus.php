<?php

namespace App\Enums;

enum QcStatus: string
{
    case PENDING = 'pending';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case REWORK = 'rework';
}
