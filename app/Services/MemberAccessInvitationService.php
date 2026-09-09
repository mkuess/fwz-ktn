<?php

namespace App\Services;

use App\Mail\MemberApprovedMail;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MemberAccessInvitationService
{
    public const CODE_LIFETIME_DAYS = 7;

    public function send(Member $member): void
    {
        $email = strtolower(trim($member->email));
        $code = (string) random_int(100000, 999999);

        if (empty($member->membership_number)) {
            $member->membership_number = $member->generateMembershipNumber();
        }

        DB::table('member_password_reset_codes')->updateOrInsert([
            'email' => $email,
        ], [
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addDays(self::CODE_LIFETIME_DAYS),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resetUrl = URL::temporarySignedRoute(
            'member.reset.code',
            now()->addDays(self::CODE_LIFETIME_DAYS),
            ['email' => $email],
        );

        try {
            Mail::to($member->email)->send(new MemberApprovedMail(
                $code,
                $resetUrl,
                self::CODE_LIFETIME_DAYS,
            ));
        } catch (\Throwable $exception) {
            DB::table('member_password_reset_codes')->where('email', $email)->delete();

            throw $exception;
        }

        $member->forceFill([
            'activation_token' => null,
            'activation_sent_at' => now(),
            'membership_number' => $member->membership_number,
        ])->save();
    }
}
