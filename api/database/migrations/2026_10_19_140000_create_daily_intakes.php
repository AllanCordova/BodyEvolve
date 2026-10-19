<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_intakes', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->date('intake_date');
            $table->unsignedInteger('kcal');
            $table->decimal('protein_g', 6, 2);
            $table->decimal('fat_g', 6, 2);
            $table->decimal('carb_g', 6, 2);
            $table->decimal('fiber_g', 6, 2);
            $table->timestamps();

            $table->primary(['user_id', 'intake_date']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_intakes');
    }
};
