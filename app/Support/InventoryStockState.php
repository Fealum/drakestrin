<?php

namespace App\Support;

enum InventoryStockState: int
{
    case COMMITTED_TOOL = -3;
    case PRODUCTION = -2;
    case RESERVED = -1;
}
