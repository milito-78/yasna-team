<?php

namespace App\Models\Enums;

enum ProductChangeStatusesEnum: string
{
    case Locked = "locked";
    case Unlocked = "unlocked";
    case Increase = "increase";
    case Decrease = "decrease";
}
