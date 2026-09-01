<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('member_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('members')
                ->nullOnDelete();
            $table->boolean('is_admin')->default(false)->after('password');
        });

        DB::table('users')->update(['is_admin' => true]);

        $adminMembers = DB::table('members')
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->whereNull('deleted_at')
            ->whereNotNull('password')
            ->get();

        foreach ($adminMembers as $member) {
            $name = trim(($member->first_name ?? '').' '.($member->last_name ?? ''));
            $attributes = [
                'member_id' => $member->id,
                'name' => $name !== '' ? $name : $member->email,
                'password' => $member->password,
                'is_admin' => true,
                'updated_at' => now(),
            ];

            if (DB::table('users')->where('email', $member->email)->exists()) {
                DB::table('users')
                    ->where('email', $member->email)
                    ->update($attributes);
            } else {
                DB::table('users')->insert([
                    ...$attributes,
                    'email' => $member->email,
                    'email_verified_at' => $member->email_verified_at,
                    'remember_token' => null,
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('member_id');
            $table->dropColumn('is_admin');
        });
    }
};
