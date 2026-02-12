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
            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();
            $table->decimal('room_rent', 10, 2)->default(0);
            $table->decimal('water', 10, 2)->default(0);
            $table->decimal('electricity', 10, 2)->default(0);
            $table->decimal('trash', 10, 2)->default(0);
            $table->decimal('parking', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_properties');
    }
};
