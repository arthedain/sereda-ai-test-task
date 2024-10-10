<?php

namespace App\Http\Controllers;

use App\Helpers\PriceHelper;
use App\Models\User;
use App\Services\Subscriptions\SubscriptionPlanService;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly SubscriptionService $subscriptionService,
        protected readonly SubscriptionPlanService $subscriptionPlanService,
    )
    {
    }

    public function index(): View|Factory|Application
    {
        /**
         * @var $user User
         */
        $user = auth()->user();

        $user->loadMissing('team.members');

        $data = [];

        $subscriptionPlans = $this->subscriptionPlanService->list();


        if($user->hasActiveSubscription()) {
            $subscription = $user->getActiveSubscription();

            $subscriptionAmount = $this->subscriptionService->calcAmount($user->team, $subscription->plan);
            $subscriptionCurrency = $subscription->plan->currency;

            $data = [
                'subscription_name' => $subscription->plan->name,
                'subscription_amount' => PriceHelper::format($subscriptionAmount, $subscriptionCurrency),
                'team_members_count' => $user->team->members->count(),
                'subscription_interval' => Str::ucfirst(Str::lower($subscription->plan->interval->name)),
                'subscription_next_payment_date' => $subscription->next_payment_date->format('d.m.Y'),
            ];
        } else {
            redirect()->route('welcome');
        }

        if($user->hasPendingSubscription()) {
            $pendingSubscription = $user->getPendingSubscription();

            $pendingSubscriptionAmount = $this->subscriptionService->calcAmount($user->team, $pendingSubscription->plan);
            $pendingSubscriptionCurrency = $pendingSubscription->plan->currency;

            $pendingData = [
                'has_pending_subscription' => true,
                'pending_subscription_name' => $pendingSubscription->plan->name,
                'pending_subscription_amount' => PriceHelper::format($pendingSubscriptionAmount, $pendingSubscriptionCurrency),
                'pending_team_members_count' => $user->team->members->count(),
                'pending_subscription_interval' => Str::ucfirst(Str::lower($pendingSubscription->plan->interval->name)),
                'pending_subscription_start_date' => $pendingSubscription->start_date->format('d.m.Y'),
            ];

            $data = array_merge($data, $pendingData);
        }

        return view('dashboard', [
            'user' => $user,
            'team_id' => $user->team->id,
            'subscription_plans' => $subscriptionPlans,
            ...$data,
        ]);
    }
}
