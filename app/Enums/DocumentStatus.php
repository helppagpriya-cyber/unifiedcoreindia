<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case SCANNED = 'scanned';
    case QC = 'qc';
    case COMPLETED = 'completed';
}
