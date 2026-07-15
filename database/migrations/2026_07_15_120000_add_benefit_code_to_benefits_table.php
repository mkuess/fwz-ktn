<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('benefits', function (Blueprint $table) {
            $table->text('benefit_code')->nullable()->after('description');
        });
    }
    public function down(): void {
        Schema::table('benefits', function (Blueprint $table) {
            $table->dropColumn('benefit_code');
        });
    }
};
