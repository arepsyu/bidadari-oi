<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu')->after('file_original_name');
            $table->text('catatan_admin')->nullable()->after('status');
            $table->foreignId('verified_by')->nullable()->after('catatan_admin')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['status', 'catatan_admin', 'verified_at']);
        });
    }
};
