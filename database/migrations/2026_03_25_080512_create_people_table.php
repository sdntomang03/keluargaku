<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gunakan Schema::create untuk MEMBUAT tabel
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('people')->nullOnDelete();

            // Buat kolom spouse_id (tanpa constrained)
            $table->unsignedBigInteger('spouse_id')->nullable();

            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('photo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
