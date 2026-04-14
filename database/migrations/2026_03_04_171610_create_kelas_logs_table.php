<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah_ompreng')->nullable();
            $table->timestamp('diambil')->nullable();
            $table->timestamp('dikembalikan')->nullable();
            $table->timestamps();

            $table->unique(['kelas_id', 'tanggal']); // 1 log per kelas per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_logs');
    }
};
