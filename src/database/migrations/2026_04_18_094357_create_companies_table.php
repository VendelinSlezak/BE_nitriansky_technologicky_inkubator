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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->mediumText('company_name');
            $table->mediumText('company_address');
            $table->mediumText('description');
            $table->string('ico', 8);
            $table->string('dic', 10);
            $table->mediumText('category');
            $table->mediumText('name_of_contact_person');
            $table->boolean('is_approved_by_admin');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('logo_id')->constrained('files')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
