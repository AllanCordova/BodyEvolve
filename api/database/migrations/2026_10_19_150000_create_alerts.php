<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('alert_date');
            $table->string('kind');
            $table->string('message');
            $table->boolean('fails_goal');
            $table->boolean('resolved')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'alert_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
