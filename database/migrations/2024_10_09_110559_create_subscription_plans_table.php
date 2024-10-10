<?php

use App\Enums\SubscriptionIntervalEnum;
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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('currency');
            $table->integer('price');
            $table->integer('price_discount')->nullable();
            $table->integer('price_discount_type')->nullable();
            $table->string('interval')->default(SubscriptionIntervalEnum::MONTHLY);
            $table->string('trial_period_days')->default(0);
            $table->string('status');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
