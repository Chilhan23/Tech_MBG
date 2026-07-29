<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan composite index (waktu_ambil, student_id) agar query
     * range per hari dapat memanfaatkan index dan tidak full-scan.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Composite index: waktu_ambil di depan agar range scan per hari
            // dapat memanfaatkan index, kemudian student_id untuk covering index
            // pada COUNT(DISTINCT student_id).
            $table->index(['waktu_ambil', 'student_id'], 'absensis_waktu_ambil_student_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('absensis_waktu_ambil_student_id_index');
        });
    }
};
