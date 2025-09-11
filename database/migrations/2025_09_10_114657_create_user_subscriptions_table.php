<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();

            $table->decimal('plan_price', 10, 2)->nullable(); 
            $table->integer('plan_duration')->nullable(); 
            $table->integer('plan_no_of_ablums')->nullable(); 

            // Payment info
            $table->string('transaction_id')->unique()->nullable(); 
            $table->string('transaction_status')->nullable();       // e.g., 'completed', 'pending'
            $table->string('card_last_four', 4)->nullable();       // Last 4 digits of card
            $table->string('card_exp_month', 2)->nullable();       // Expiration month
            $table->string('card_exp_year', 4)->nullable();        // Expiration year
            $table->string('payment_token')->nullable();           // Token returned by gateway
            $table->enum('status', ['active', 'canceled', 'past_due', 'expired', 'trialing'])->default('trialing');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
