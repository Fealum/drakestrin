<?php

namespace App\Support;

enum InventoryMutationKind: string
{
    case ADJUSTMENT = 'adjustment';
    case CONSUMPTION = 'consumption';
    case PRODUCTION = 'production';
    case STATE_CHANGE = 'state_change';
    case TRANSFER = 'transfer';
}
