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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('program', 1);
            $table->string('name', 45);
            $table->boolean('automatically_create_team_after_approval');
            $table->mediumText('description');
            $table->integer('proposal_file_id');
            $table->float('budget')->nullable();
            $table->string('status', 45);
            $table->mediumText('comment_of_commission')->nullable();
            $table->mediumText('final_assessment')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('program_a_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
