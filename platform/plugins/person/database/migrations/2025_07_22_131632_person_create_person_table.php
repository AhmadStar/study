<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('people')) {
            Schema::create('people', function (Blueprint $table) {
                $table->id();
                $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
                $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
                $table->string('name');
                $table->enum('gender', ['male', 'female']);
                $table->date('date_of_birth')->nullable();
                $table->string('education_level')->nullable();
                $table->string('national_id')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->string('short_info')->nullable();
                $table->longText('full_info')->nullable();
                $table->string('image')->nullable();
                $table->json('gallery')->nullable();
                $table->json('audio_links')->nullable();
                $table->json('video_links')->nullable();
                $table->string('status')->default('published')->index();
            });
        }

        if (! Schema::hasTable('people_translations')) {
            Schema::create('people_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('people_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'people_id'], 'people_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
        Schema::dropIfExists('people_translations');
    }
};
