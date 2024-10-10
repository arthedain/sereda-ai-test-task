<?php

namespace App\Enums;

enum SubscriptionStatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case EXPIRED = 3;
    case CANCELLED = 4;
    case PENDING = 5;
    case APPROACHED = 6;
    case UNCLAIMED = 7;
}
