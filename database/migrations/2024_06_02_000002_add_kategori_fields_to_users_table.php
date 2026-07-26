<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('kategori', ['opd', 'kecamatan', 'desa'])->nullable()->after('role');
            $table->foreignId('opd_id')->nullable()->after('kategori')->constrained('opds')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opd_id');
            $table->dropColumn('kategori');
        });
    }
};
