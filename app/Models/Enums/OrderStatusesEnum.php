<?php

namespace App\Models\Enums;

enum OrderStatusesEnum:int
{
    case Created = 1;

    case Accepted = 2;

    case Delivered = 3;

    case Canceled = 4;
}
