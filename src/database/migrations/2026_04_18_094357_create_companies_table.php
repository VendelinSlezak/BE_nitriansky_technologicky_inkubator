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
            $table->string('company_name, 80');
            $table->string('company_address, 150');
            $table->mediumText('description');
            $table->longText('organization_identification_number');
            $table->longText('tax_identification_number');
            $table->mediumText('category');
            $table->mediumText('description');
            $table->string('name_of_contact_person, 60');
            $table->boolean('is_approved_by_admin');
            $table->boolean('show_logo_image');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
