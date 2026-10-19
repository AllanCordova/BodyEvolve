<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('user_id');
            $table->string('exercise_code');
            $table->unsignedSmallInteger('sets_done');
            $table->unsignedSmallInteger('reps_done');
            $table->decimal('load_kg', 6, 2)->nullable();
            $table->string('variation')->nullable();
            $table->timestamp('recorded_at');

            $table->foreign('session_id')->references('id')->on('training_sessions')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'exercise_code', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_records');
    }
};
