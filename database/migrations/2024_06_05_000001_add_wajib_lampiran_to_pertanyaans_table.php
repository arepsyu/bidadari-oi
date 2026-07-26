<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->boolean('wajib_lampiran')->default(false)->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            $table->dropColumn('wajib_lampiran');
        });
    }
};
