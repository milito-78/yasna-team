<?php

namespace App\Models\Enums;

enum UserStatusesEnum: int
{
    case Register = 1;
    case Active = 2;
    case Block = 3;


}
