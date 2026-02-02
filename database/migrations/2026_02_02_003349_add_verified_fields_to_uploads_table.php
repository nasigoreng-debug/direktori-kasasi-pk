// database/migrations/xxxx_add_verified_fields_to_uploads_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('uploads', function (Blueprint $table) {
            // Tambahkan kolom yang hilang
            if (!Schema::hasColumn('uploads', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('uploads', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('verified_at')
                    ->constrained('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('uploads', 'catatan')) {
                $table->text('catatan')->nullable()->after('file_size');
            }
        });
    }

    public function down()
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'verified_by', 'catatan']);
        });
    }
};
