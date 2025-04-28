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
        Schema::create('post_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('post_id')->nullable();
            $table->foreignId('payment_id')->nullable();
            $table->string('budget')->nullable();
            $table->enum('status', ['pending', 'running'])->default('pending');
            $table->string('boost_start_date')->nullable();
            $table->string('boost_duration_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_boosts');
    }
};
