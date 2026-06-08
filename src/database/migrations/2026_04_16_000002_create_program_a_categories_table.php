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
        Schema::create('program_a_categories', function (Blueprint $table) {
            $table->id();
            $table->mediumText('title');
            $table->mediumText('description_of_skills');
            $table->enum('status', ['visible', 'invisible'])->default('visible');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_a_categories');
    }
};
