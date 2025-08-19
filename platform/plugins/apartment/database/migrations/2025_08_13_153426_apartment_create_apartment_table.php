<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('apartments')) {
            Schema::create('apartments', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
                $table->string('apartment_number')->nullable();
                $table->integer('floor_number')->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('apartments_translations')) {
            Schema::create('apartments_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('apartments_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'apartments_id'], 'apartments_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
        Schema::dropIfExists('apartments_translations');
    }
};
