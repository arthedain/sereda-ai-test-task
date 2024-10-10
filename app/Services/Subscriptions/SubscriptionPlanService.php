<?php

namespace App\Services\Subscriptions;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionPlanService
{
    public function list(): Collection
    {
        return SubscriptionPlan::all();
    }

    public function get(int $planId): ?SubscriptionPlan
    {
        return SubscriptionPlan::find($planId);
    }
}
