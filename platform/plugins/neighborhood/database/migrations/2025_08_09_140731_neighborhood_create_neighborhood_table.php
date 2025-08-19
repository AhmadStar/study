<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('neighborhoods')) {
            Schema::create('neighborhoods', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('neighborhoods_translations')) {
            Schema::create('neighborhoods_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('neighborhoods_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'neighborhoods_id'], 'neighborhoods_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('neighborhoods');
        Schema::dropIfExists('neighborhoods_translations');
    }
};
