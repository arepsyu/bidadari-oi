<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_opd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertanyaan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opd_id')->constrained()->cascadeOnDelete();
            $table->unique(['pertanyaan_id', 'opd_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_opd');
    }
};
