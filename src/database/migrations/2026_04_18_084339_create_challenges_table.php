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
            $table->foreignId('proposal_file_id')->constrained('files')->onDelete('cascade');
            $table->float('reward')->nullable();
            $table->string('status', 45); // "proposed" / "open" / "in_evaluation" / "rejected_by_commission" / "accepted_by_commission" / "in_progress" / "finished"
            $table->mediumText('final_assessment')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('program_a_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->dateTime('date_of_completion')->nullable();
            $table->mediumText('commission_comment')->nullable();
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
