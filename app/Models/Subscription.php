<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'plan_id',
        'quantity',
        'status',
        'start_date',
        'next_payment_date',
        'end_date',
        'trial_start_date',
        'trial_end_date',
        'canceled_at'
    ];

    protected $casts = [
        'status' => SubscriptionStatusEnum::class,
        'start_date' => 'date',
        'next_payment_date' => 'date',
        'end_date' => 'date',
        'trial_start_date' => 'date',
        'trial_end_date' => 'date',
        'canceled_at' => 'date'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id', 'id');
    }
}
