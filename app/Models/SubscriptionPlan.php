<?php

namespace App\Models;

use App\Enums\SubscriptionDiscountTypeEnum;
use App\Enums\SubscriptionIntervalEnum;
use App\Enums\SubscriptionPlanStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'price_discount',
        'price_discount_type',
        'currency',
        'interval',
        'trial_period_days',
        'description',
        'status'
    ];

    protected $casts = [
        'interval' => SubscriptionIntervalEnum::class,
        'status' => SubscriptionPlanStatusEnum::class,
        'price_discount_type' => SubscriptionDiscountTypeEnum::class,
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
