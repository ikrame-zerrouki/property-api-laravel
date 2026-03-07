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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Foreign key to user (agent/owner)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Property details
            $table->string('type'); // appartement, villa, terrain, magasin, bureau
            $table->integer('pieces'); // Number of rooms
            $table->float('surface'); // Surface area in m²
            $table->decimal('prix', 10, 2); // Price
            $table->string('ville'); // City
            $table->text('description'); // Description
            $table->string('statut')->default('disponible'); // disponible, vendu, location
            $table->boolean('is_published')->default(false);
            $table->string('title'); // Auto-generated title

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
