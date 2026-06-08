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
            $table->mediumText('name');
            $table->string('status', 100); // "draft" / "waiting_for_approval" / "active" / "finished"
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposal_of_implementation_id')->nullable()->constrained('files')->onDelete('set null');
            $table->foreignId('cover_letter_id')->nullable()->constrained('files')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
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
