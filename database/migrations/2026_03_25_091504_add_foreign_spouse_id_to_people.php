<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gunakan Schema::table untuk MENGUBAH tabel yang sudah ada
        Schema::table('people', function (Blueprint $table) {
            // Mengunci relasi spouse_id ke tabel spouses
            $table->foreign('spouse_id')->references('id')->on('spouses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['spouse_id']);
        });
    }
};
