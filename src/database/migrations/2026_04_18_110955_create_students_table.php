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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('university', 90);
            $table->boolean('is_accepted_by_admin');
            $table->boolean('is_invited_to_the_team');
            $table->boolean('is_a_teamleader');
            $table->foreignId('curriculum_vitae_id')->constrained('attachments')->onDelete('cascade');    
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
