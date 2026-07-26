<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained()->cascadeOnDelete();
            $table->string('kode')->nullable();
            $table->text('teks');
            $table->enum('tipe', ['text', 'textarea', 'number', 'date', 'file'])->default('file');
            $table->decimal('nilai_max', 8, 2)->nullable();
            $table->decimal('nilai_evaluasi', 8, 2)->nullable();
            $table->boolean('wajib')->default(true);
            $table->boolean('untuk_kecamatan')->default(false);
            $table->boolean('untuk_desa')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaans');
    }
};
