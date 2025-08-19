<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('buildings')) {
            Schema::create('buildings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->string('address')->nullable();
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->string('building_number')->nullable();
                $table->integer('floors_count')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('buildings_translations')) {
            Schema::create('buildings_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('buildings_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'buildings_id'], 'buildings_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('buildings_translations');
    }
};
