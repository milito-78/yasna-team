<?php

namespace App\Models\Enums;

enum ProductChangeTypesEnum: string
{
    case IncreaseVolume = "1";
    case DecreaseVolume = "-1";
}
