<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();

            // Nomor perkara lengkap
            $table->string('nomor_perkara_pa')->nullable();
            $table->string('nomor_perkara_banding')->nullable();
            $table->string('nomor_perkara_kasasi')->nullable();
            $table->string('nomor_perkara_pk')->nullable();

            $table->enum('jenis_putusan', ['kasasi', 'pk']);
            $table->date('tanggal_putusan');

            $table->string('file_path');
            $table->string('original_filename');
            $table->integer('file_size');

            $table->enum('status', ['draft', 'submitted', 'verified', 'rejected'])->default('submitted');

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('pengadilan_id')->constrained('pengadilan');

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
