<?php

namespace App\Models\Enums;

enum ProductChangeReasonsEnum: int
{
    case System = 1;
    case Order = 2;
    case GatewayCallback = 3;

    case Admin = 4;
}
