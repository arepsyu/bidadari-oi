<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klaster_id')->constrained()->cascadeOnDelete();
            $table->string('kode')->nullable();
            $table->string('nama');
            $table->integer('urutan')->default(0);
            $table->decimal('nilai_max', 8, 2)->nullable();
            $table->decimal('nilai_evaluasi', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};
