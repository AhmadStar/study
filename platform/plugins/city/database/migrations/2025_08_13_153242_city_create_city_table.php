<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('country')->nullable();
                $table->integer('population_estimate')->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cities_translations')) {
            Schema::create('cities_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('cities_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'cities_id'], 'cities_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('cities_translations');
    }
};
