<?php

namespace App\Support;

enum PostTransferAction: string
{
    case GIVE = 'give';
    case DROP = 'drop';
    case PICKUP = 'pickup';
    case COMPANY_DEPOSIT = 'company_deposit';
    case COMPANY_WITHDRAWAL = 'company_withdrawal';
}
