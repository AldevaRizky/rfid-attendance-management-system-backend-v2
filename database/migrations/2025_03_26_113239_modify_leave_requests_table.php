<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['approved_by']);

            // Hapus kolom yang tidak dibutuhkan
            $table->dropColumn(['duration', 'status', 'approved_by']);

            // Mengubah opsi enum pada kolom 'type'
            $table->enum('type', ['sick', 'personal'])->change();

            // Tambahkan kolom untuk file lampiran
            $table->string('attachment')->nullable()->after('reason');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Mengembalikan kolom yang dihapus
            $table->integer('duration')->after('end_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->after('reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');

            // Hapus kolom 'attachment'
            $table->dropColumn('attachment');

            // Kembalikan opsi enum pada kolom 'type'
            $table->enum('type', ['sick', 'personal', 'annual', 'other'])->change();
        });
    }

};
