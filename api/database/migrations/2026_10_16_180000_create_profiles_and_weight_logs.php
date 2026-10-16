<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->unsignedSmallInteger('age_years');
            $table->string('sex');
            $table->unsignedSmallInteger('height_cm');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('weight_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->decimal('weight_kg', 5, 2);
            $table->date('recorded_on');
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'recorded_on', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weight_logs');
        Schema::dropIfExists('profiles');
    }
};
