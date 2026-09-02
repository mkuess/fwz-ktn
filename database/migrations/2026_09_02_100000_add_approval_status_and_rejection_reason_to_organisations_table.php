<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table): void {
            $table->string('approval_status')->default('pending')->after('is_approved');
            $table->text('rejection_reason')->nullable()->after('approval_status');
        });

        DB::table('organisations')
            ->where('is_approved', true)
            ->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table): void {
            $table->dropColumn(['approval_status', 'rejection_reason']);
        });
    }
};
