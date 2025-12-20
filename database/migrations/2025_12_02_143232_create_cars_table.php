<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('car_model_id')->constrained('car_models')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->cascadeOnDelete();

            $table->string('name');
            $table->integer('year');
            $table->integer('mileage');
            $table->string('transmission');
            $table->string('fuel_type');
            $table->string('engine_size')->nullable();
            $table->string('color');
            $table->decimal('price', 15, 2);
            $table->string('status')->default('Available');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
