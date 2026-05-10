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
            $table->foreignId('subscriber_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subscribed_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subscriber_id', 'subscribed_user_id']);
            $table->index('subscribed_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
