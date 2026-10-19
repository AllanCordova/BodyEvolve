<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('modality_code');
            $table->date('trained_on');
            $table->unsignedSmallInteger('duration_min');
            $table->decimal('intensity_factor', 4, 2);
            $table->boolean('already_in_lifestyle');
            $table->decimal('net_kcal', 8, 2);
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'trained_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
