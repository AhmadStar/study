<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
                $table->string('name');
                $table->integer('population_estimate')->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('districts_translations')) {
            Schema::create('districts_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('districts_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'districts_id'], 'districts_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('districts_translations');
    }
};
