<?php

namespace App\Models\Enums;

enum TransactionStatusEnum:int
{
    case Started = 1;

    case Failed = 2;

    case Succeed = 3;

    case Canceled = 4;
}
