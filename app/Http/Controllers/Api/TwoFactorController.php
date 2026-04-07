<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    /**
     * Generate a new 2FA secret and return the provisioning URI.
     * User must call confirm() with a valid code to enable 2FA.
     */
    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json(['message' => '2FA is already enabled.'], 400);
        }

        // Generate a base32 secret (16 characters = 80 bits)
        $secret = $this->generateBase32Secret();

        // Store secret (not yet enabled)
        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => false,
        ]);

        $appName = config('app.name', 'AutoScout24 SafeTrade');
        $otpauthUri = "otpauth://totp/" . rawurlencode($appName) . ":" . rawurlencode($user->email)
            . "?secret=" . $secret
            . "&issuer=" . rawurlencode($appName)
            . "&digits=6&period=30";

        return response()->json([
            'secret' => $secret,
            'otpauth_uri' => $otpauthUri,
        ]);
    }

    /**
     * Confirm 2FA setup by verifying a TOTP code from the user's authenticator app.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json(['message' => '2FA is already enabled.'], 400);
        }

        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'Please run setup first.'], 400);
        }

        if (!$this->verifyTotp($user->two_factor_secret, $request->code)) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        // Generate recovery codes
        $recoveryCodes = collect(range(1, 8))->map(fn () => Str::random(10))->all();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return response()->json([
            'message' => '2FA enabled successfully.',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Verify a TOTP code (used during login).
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['message' => '2FA is not enabled.'], 400);
        }

        // Check TOTP code first
        if ($this->verifyTotp($user->two_factor_secret, $request->code)) {
            return response()->json(['message' => '2FA verification successful.']);
        }

        // Check recovery codes
        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        if (in_array($request->code, $recoveryCodes, true)) {
            // Remove used recovery code
            $user->update([
                'two_factor_recovery_codes' => array_values(array_diff($recoveryCodes, [$request->code])),
            ]);
            return response()->json(['message' => '2FA verification successful (recovery code used).']);
        }

        return response()->json(['message' => 'Invalid verification code.'], 422);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['message' => '2FA is not enabled.'], 400);
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password.'], 422);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json(['message' => '2FA disabled successfully.']);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json(['message' => '2FA is not enabled.'], 400);
        }

        $recoveryCodes = collect(range(1, 8))->map(fn () => Str::random(10))->all();

        $user->update([
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return response()->json([
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Generate a base32-encoded random secret.
     */
    private function generateBase32Secret(int $length = 16): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[ord($bytes[$i]) % 32];
        }
        return $secret;
    }

    /**
     * Verify a TOTP code against a secret.
     * Allows ±1 time-step window for clock drift.
     */
    private function verifyTotp(string $secret, string $code): bool
    {
        $timeStep = 30;
        $currentTime = time();

        for ($offset = -1; $offset <= 1; $offset++) {
            $timestamp = (int) floor(($currentTime + ($offset * $timeStep)) / $timeStep);
            $expectedCode = $this->generateTotp($secret, $timestamp);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a TOTP code for a given timestamp.
     */
    private function generateTotp(string $secret, int $counter): string
    {
        $decodedSecret = $this->base32Decode($secret);
        $counterBytes = pack('J', $counter); // 64-bit big-endian

        $hash = hash_hmac('sha1', $counterBytes, $decodedSecret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $code = (
            (ord($hash[$offset]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a base32-encoded string.
     */
    private function base32Decode(string $input): string
    {
        $map = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $input = strtoupper(rtrim($input, '='));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            if (!isset($map[$char])) {
                continue;
            }
            $buffer = ($buffer << 5) | $map[$char];
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
