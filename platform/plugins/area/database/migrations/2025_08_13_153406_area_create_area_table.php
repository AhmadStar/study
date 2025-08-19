<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('areas')) {
            Schema::create('areas', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('areas_translations')) {
            Schema::create('areas_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('areas_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'areas_id'], 'areas_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
        Schema::dropIfExists('areas_translations');
    }
};
