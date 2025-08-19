<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('families')) {
            Schema::create('families', function (Blueprint $table) {
                $table->id();
                $table->string('family_number')->unique();
                $table->foreignId('father_id')->nullable()->constrained('people')->nullOnDelete();
                $table->foreignId('mother_id')->nullable()->constrained('people')->nullOnDelete();
                $table->string('address')->nullable();
                $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
                $table->string('family_code')->unique();
                $table->unsignedBigInteger('region_id')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->unsignedBigInteger('village_id')->nullable();
                $table->string('housing_type')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('families_translations')) {
            Schema::create('families_translations', function (Blueprint $table) {
                $table->string('lang_code');
                $table->foreignId('families_id');
                $table->string('name', 255)->nullable();

                $table->primary(['lang_code', 'families_id'], 'families_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
        Schema::dropIfExists('families_translations');
    }
};
