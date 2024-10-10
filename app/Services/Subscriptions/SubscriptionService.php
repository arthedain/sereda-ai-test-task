<?php

namespace App\Services\Subscriptions;

use App\Enums\SubscriptionDiscountTypeEnum;
use App\Enums\SubscriptionIntervalEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use Carbon\Carbon;
use Date;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService
{
    public function __construct()
    {

    }

    /**
     * Calculate the total subscription amount for a team.
     *
     * @param Team $team The team whose subscription amount is being calculated.
     * @param SubscriptionPlan $plan The subscription plan being applied.
     *
     * @return int The calculated total amount after discounts.
     */
    public function calcAmount(Team $team, SubscriptionPlan $plan): int
    {
        $membersCount = $team->members->count();
        $amount = $plan->price;

        $result = $amount * $membersCount;

        if($plan->price_discount) {
            $result = $this->calcDiscount($result, $plan);
        }

        return $result;
    }

    /**
     * Changes the subscription plan for the given team by creating a new pending subscription
     * if the team does not already have an active subscription for the specified plan.
     * It also handles the closure of any pending subscriptions and sets the appropriate
     * start and end dates for the new subscription.
     *
     * @param Team $team The team whose subscription plan is to be changed.
     * @param SubscriptionPlan $plan The new subscription plan to be assigned.
     * @return null|Subscription The newly created subscription or null if the team already has the active subscription for the same plan.
     */
    public function changePlan(Team $team, SubscriptionPlan $plan): ?Subscription
    {
        $team->loadMissing('subscriptions');

        $activeSubscription = $team->subscriptions->where('status', SubscriptionStatusEnum::ACTIVE)->first();

        if ($activeSubscription && $activeSubscription->plan_id == $plan->id) {
            return null;
        }

        $this->closePendingSubscription($team);

        if(!$activeSubscription) {
            return $this->create($team, $plan);
        }

        $startDate = $activeSubscription->end_date && $activeSubscription->end_date->greaterThan(now()) ? $activeSubscription->end_date : now();

        $endDate = $this->getEndDate($startDate, $plan->interval);

        $subscription = Subscription::create([
            'team_id' => $team->id,
            'plan_id' => $plan->id,
            'quantity' => 1,
            'status' => SubscriptionStatusEnum::PENDING,
            'start_date' => $startDate,
            'next_payment_date' => $startDate,
            'end_date' => $endDate,
            'trial_start_date' => null,
            'trial_end_date' => null,
            'canceled_at' => null,
        ]);

        /**
         * Implementation of the payment system
         */

        return $subscription;
    }

    /**
     * Processes the given collection of subscriptions by checking their statuses
     * and updating them accordingly. Active subscriptions are checked for
     * end dates and extended if necessary. Pending subscriptions that start today
     * are activated. Active subscriptions that are replaced by pending subscriptions
     * are cancelled.
     *
     * @param Collection $subscriptions Collection of Subscription objects to process.
     */
    public function processSubscriptions(Collection $subscriptions): void
    {
        /**
         * @var null|Subscription $activeSubscription
         */
        $activeSubscription = $subscriptions->where('status', SubscriptionStatusEnum::ACTIVE)->first();
        /**
         * @var null|Subscription $pendingSubscription
         */
        $pendingSubscription = $subscriptions->where('status', SubscriptionStatusEnum::PENDING)->first();

        if($pendingSubscription && $pendingSubscription->start_date->isToday()) {
            $activeSubscription->update([
                'status' => SubscriptionStatusEnum::CANCELLED,
                'canceled_at' => now(),
            ]);

            $pendingSubscription->update([
                'status' => SubscriptionStatusEnum::ACTIVE,
            ]);

            /**
             * Implementation of the payment system
             */
        }

        if($activeSubscription && $activeSubscription->next_payment_date->isToday()) {
            $endDate = $this->getEndDate(now(), $activeSubscription->plan->interval);
            $activeSubscription->update([
                'next_payment_date' => $endDate,
                'end_date' => $endDate,
            ]);

            /**
             * Implementation of the payment system
             */
        }
    }

    private function closePendingSubscription(Team $team): void
    {
        $team->subscriptions
            ->where('status', SubscriptionStatusEnum::PENDING)
            ->each(function ($subscription) {
                $subscription->status = SubscriptionStatusEnum::UNCLAIMED;
                $subscription->canceled_at = now();
                $subscription->save();
            });
    }

    /**
     * Determine the end date of a subscription based on the start date and subscription interval.
     *
     * @param Carbon $startDate The start date of the subscription.
     * @param SubscriptionIntervalEnum $interval The interval of the subscription (e.g., yearly, monthly).
     *
     * @return Carbon The calculated end date of the subscription.
     */
    private function getEndDate(Carbon $startDate, SubscriptionIntervalEnum $interval): Carbon
    {
        $date = $startDate->copy();

        return match ($interval) {
            SubscriptionIntervalEnum::YEARLY => $date->addYear(),
            default => $date->addMonth(),
        };
    }

    /**
     * @param Team $team
     * @param SubscriptionPlan $plan
     * @return Subscription
     */
    private function create(Team $team, SubscriptionPlan $plan): Subscription
    {
        $startDate = now();

        $endDate = $this->getEndDate($startDate, $plan->interval);

        $subscription = Subscription::create([
            'team_id' => $team->id,
            'plan_id' => $plan->id,
            'quantity' => 1,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'start_date' => $startDate,
            'next_payment_date' => $startDate,
            'end_date' => $endDate,
            'trial_start_date' => null,
            'trial_end_date' => null,
            'canceled_at' => null,
        ]);

        /**
         * Implementation of the payment system
         */

        return $subscription;
    }

    private function calcDiscount(int $amount, SubscriptionPlan $plan): int
    {
        $discount = $plan->price_discount;
        $discountType = $plan->price_discount_type;

        return match ($discountType) {
            SubscriptionDiscountTypeEnum::PERCENTAGE => $amount - ($discount / 100 * $amount),
            SubscriptionDiscountTypeEnum::AMOUNT => $amount - $discount,
        };
    }
}
