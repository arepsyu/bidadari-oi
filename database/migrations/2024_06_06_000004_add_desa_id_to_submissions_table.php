<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('desa_id')->nullable()->after('pertanyaan_id')->constrained('desas')->cascadeOnDelete();
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique('submissions_user_id_pertanyaan_id_unique');
            $table->unique(['user_id', 'pertanyaan_id', 'desa_id'], 'submissions_user_pertanyaan_desa_unique');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique('submissions_user_pertanyaan_desa_unique');
            $table->unique(['user_id', 'pertanyaan_id'], 'submissions_user_id_pertanyaan_id_unique');
            $table->dropConstrainedForeignId('desa_id');
        });
    }
};
