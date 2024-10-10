<?php

namespace Database\Seeders;

use App\Enums\SubscriptionDiscountTypeEnum;
use App\Enums\SubscriptionIntervalEnum;
use App\Enums\SubscriptionPlanStatusEnum;
use App\Enums\TeamMemberRoleEnum;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $emails = [
            'owner@example.com',
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
            'user4@example.com',
            'user5@example.com',
            'user6@example.com',
        ];

        foreach ($emails as $email) {
            User::factory()->create([
                'email' => $email,
            ]);
        }

        SubscriptionPlan::insert(
            [
                [
                    'name' => 'Lite',
                    'slug' => 'lite',
                    'price' => 400, // 4$
                    'price_discount' => null,
                    'price_discount_type' => null,
                    'currency' => 'USD',
                    'interval' => SubscriptionIntervalEnum::MONTHLY,
                    'trial_period_days' => 0,
                    'description' => 'Free plan',
                    'status' => SubscriptionPlanStatusEnum::ACTIVE,

                ],
                [
                    'name' => 'Starter',
                    'slug' => 'starter',
                    'price' => 600, // 6$
                    'price_discount' => null,
                    'price_discount_type' => null,
                    'currency' => 'USD',
                    'interval' => SubscriptionIntervalEnum::MONTHLY,
                    'trial_period_days' => 0,
                    'description' => 'Pro plan',
                    'status' => SubscriptionPlanStatusEnum::ACTIVE,
                ],
                [
                    'name' => 'Premium',
                    'slug' => 'premium',
                    'price' => 1000, // 10$
                    'price_discount' => 20,
                    'price_discount_type' => SubscriptionDiscountTypeEnum::PERCENTAGE,
                    'currency' => 'USD',
                    'interval' => SubscriptionIntervalEnum::YEARLY,
                    'trial_period_days' => 0,
                    'description' => 'Enterprise plan',
                    'status' => SubscriptionPlanStatusEnum::ACTIVE,
                ]
            ]
        );

        $team = Team::create([
            'name' => 'Main',
            'owner_id' => User::first()->id,
        ]);

        User::all()->each(function (User $user) use ($team) {
            $role = TeamMemberRoleEnum::MEMBER;

            if($user->id === $team->owner_id)
            {
                $role = TeamMemberRoleEnum::OWNER;
            }

            TeamMember::create(
                [
                    'team_id' => $team->id,
                    'user_id' => $user->id,
                    'role' => $role,
                ]
            );
        });

        Subscription::create([
            'team_id' => $team->id,
            'plan_id' => SubscriptionPlan::where('slug', 'lite')->first()->id,
            'quantity' => 1,
            'status' => SubscriptionPlanStatusEnum::ACTIVE,
            'start_date' => now(),
            'next_payment_date' => now()->addMonth(),
            'end_date' => now()->addMonth(),
            'trial_start_date' => null,
            'trial_end_date' => null,
            'canceled_at' => null,
        ]);
    }
}
