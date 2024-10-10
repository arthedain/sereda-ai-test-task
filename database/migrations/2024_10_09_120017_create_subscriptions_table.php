<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->integer('quantity');
            $table->integer('status');
            $table->date('start_date');
            $table->date('next_payment_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('trial_start_date')->nullable();
            $table->date('trial_end_date')->nullable();
            $table->date('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
