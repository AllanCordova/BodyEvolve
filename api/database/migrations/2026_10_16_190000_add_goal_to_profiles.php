<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('age_years')->nullable()->change();
            $table->string('sex')->nullable()->change();
            $table->unsignedSmallInteger('height_cm')->nullable()->change();
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('goal')->nullable();
            $table->date('goal_started_on')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['goal', 'goal_started_on']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('age_years')->nullable(false)->change();
            $table->string('sex')->nullable(false)->change();
            $table->unsignedSmallInteger('height_cm')->nullable(false)->change();
        });
    }
};
