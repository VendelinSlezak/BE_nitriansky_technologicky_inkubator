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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposal_of_implementation_id')->constrained('files')->onDelete('cascade');
            $table->foreignId('cover_letter_id')->constrained('files')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
