<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('car_car_feature', function (Blueprint $table) {
            $table->foreignUuid('car_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('car_feature_id')->constrained()->cascadeOnDelete();
            $table->primary(['car_id', 'car_feature_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_features');
        Schema::dropIfExists('car_car_feature');
    }
};
