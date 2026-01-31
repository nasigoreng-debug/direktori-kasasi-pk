<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pengadilan_id')->nullable()->constrained('pengadilan');
            $table->enum('role', ['admin', 'user'])->default('user');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pengadilan_id']);
            $table->dropColumn(['pengadilan_id', 'role']);
        });
    }
};
