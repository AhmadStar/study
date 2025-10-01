<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('streets')) {
            Schema::create('streets', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->string('shape', 255);
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('streets_translations')) {
            Schema::create('streets_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('streets_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'streets_id'], 'streets_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('streets');
        Schema::dropIfExists('streets_translations');
    }
};
