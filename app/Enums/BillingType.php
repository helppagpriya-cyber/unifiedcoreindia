<?php

namespace App\Enums;

enum BillingType: string
{
    case PER_PAGE = 'per_page';
    case PER_FILE = 'per_file';
    case FIXED_COST = 'fixed_cost';
    case MANPOWER = 'manpower';
}
