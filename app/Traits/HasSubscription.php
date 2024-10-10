<?php

namespace App\Traits;

use App\Enums\SubscriptionStatusEnum;
use App\Models\Subscription;

trait HasSubscription
{
    public function hasActiveSubscription(): bool
    {
        $this->loadMissing('team.subscriptions');

        return $this->team->subscriptions->where('status', SubscriptionStatusEnum::ACTIVE)->count() > 0;
    }

    public function getActiveSubscription(): Subscription|null
    {
        $this->loadMissing(['team.subscriptions.plan']);

        return $this->team->subscriptions->where('status', SubscriptionStatusEnum::ACTIVE)->first() ?? null;
    }
    public function getPendingSubscription(): Subscription|null
    {
        $this->loadMissing(['team.subscriptions.plan']);

        return $this->team->subscriptions->where('status', SubscriptionStatusEnum::PENDING)->first() ?? null;
    }

    public function hasPendingSubscription(): bool
    {
        $this->loadMissing('team.subscriptions');

        return $this->team->subscriptions->where('status', SubscriptionStatusEnum::PENDING)->count() > 0;
    }
}
