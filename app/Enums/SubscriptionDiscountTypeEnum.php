<?php

namespace App\Enums;

enum SubscriptionDiscountTypeEnum: int
{
    case PERCENTAGE = 1;
    case AMOUNT = 2;
}
