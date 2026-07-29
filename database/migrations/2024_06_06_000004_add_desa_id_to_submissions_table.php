<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'desa_id')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('desa_id')->nullable()->after('pertanyaan_id')->constrained('desas')->cascadeOnDelete();
            });
        }

        // Pastiin ada index biasa khusus buat user_id dulu, SEBELUM unique index lama
        // dihapus -- soalnya foreign key user_id butuh minimal 1 index yang nunjuk ke situ,
        // dan index lama itu (user_id, pertanyaan_id) kebetulan jadi satu-satunya yang dipakai.
        $idxUserId = DB::select("SHOW INDEX FROM submissions WHERE Key_name = 'submissions_user_id_index'");
        if (empty($idxUserId)) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('user_id', 'submissions_user_id_index');
            });
        }

        $indexLama = DB::select("SHOW INDEX FROM submissions WHERE Key_name = 'submissions_user_id_pertanyaan_id_unique'");
        if (! empty($indexLama)) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropUnique('submissions_user_id_pertanyaan_id_unique');
            });
        }

        $indexBaru = DB::select("SHOW INDEX FROM submissions WHERE Key_name = 'submissions_user_pertanyaan_desa_unique'");
        if (empty($indexBaru)) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->unique(['user_id', 'pertanyaan_id', 'desa_id'], 'submissions_user_pertanyaan_desa_unique');
            });
        }
    }

    public function down(): void
    {
        $indexBaru = DB::select("SHOW INDEX FROM submissions WHERE Key_name = 'submissions_user_pertanyaan_desa_unique'");
        if (! empty($indexBaru)) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropUnique('submissions_user_pertanyaan_desa_unique');
            });
        }

        Schema::table('submissions', function (Blueprint $table) {
            $table->unique(['user_id', 'pertanyaan_id'], 'submissions_user_id_pertanyaan_id_unique');
        });

        if (Schema::hasColumn('submissions', 'desa_id')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('desa_id');
            });
        }
    }
};
