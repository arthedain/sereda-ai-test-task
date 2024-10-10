<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeSubscriptionPlanRequest;
use App\Models\Team;
use App\Services\Subscriptions\SubscriptionPlanService;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        protected readonly SubscriptionService $subscriptionService,
        protected readonly SubscriptionPlanService $subscriptionPlanService,
    )
    {
    }

    public function changePlan(ChangeSubscriptionPlanRequest $request, Team $team): RedirectResponse
    {
        $planId = $request->get('plan_id');

        $plan = $this->subscriptionPlanService->get($planId);

        $this->subscriptionService->changePlan($team, $plan);

        return redirect()->route('dashboard');
    }
}
