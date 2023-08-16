<?php

namespace App\Models\Enums;

enum ProductStatusesEnum: int
{
    case Active = 1;

    case Inactive = 2;

    case OutOffStock = 3;
}
