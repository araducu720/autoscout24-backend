<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('dealer');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'locale' => $user->locale,
                'currency' => $user->currency,
                'country' => $user->country,
                'email_verified' => $user->hasVerifiedEmail(),
                'bank_details' => $user->hasBankDetails() ? [
                    'bank_name' => $user->bank_name,
                    'iban' => $this->maskIban($user->iban),
                    'bic' => $user->bic,
                    'account_holder' => $user->account_holder,
                    'verified' => $user->bank_details_verified,
                ] : null,
                'has_bank_details' => $user->hasBankDetails(),
                'is_dealer' => $user->dealer !== null,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'locale' => 'sometimes|string|in:en,de,fr,it,es,nl,pl,pt,ro,cs,sv',
            'currency' => 'sometimes|string|in:EUR,GBP,CHF,PLN,RON,CZK,SEK',
            'country' => 'nullable|string|size:2',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Update bank details.
     */
    public function updateBankDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'iban' => [
                'required',
                'string',
                'max:34',
                'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/',
            ],
            'bic' => 'nullable|string|max:11|regex:/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/',
            'account_holder' => 'required|string|max:100',
        ], [
            'iban.regex' => 'Please enter a valid IBAN format.',
            'bic.regex' => 'Please enter a valid BIC/SWIFT format.',
        ]);

        // Convert to uppercase
        $validated['iban'] = strtoupper(str_replace(' ', '', $validated['iban']));
        if (!empty($validated['bic'])) {
            $validated['bic'] = strtoupper($validated['bic']);
        }

        // Reset verification status when bank details change
        $bankDetailsChanged = $user->iban !== $validated['iban'] 
            || $user->account_holder !== $validated['account_holder'];

        $user->update([
            ...$validated,
            'bank_details_verified' => $bankDetailsChanged ? false : $user->bank_details_verified,
            'bank_details_verified_at' => $bankDetailsChanged ? null : $user->bank_details_verified_at,
        ]);

        return response()->json([
            'message' => 'Bank details updated successfully',
            'data' => [
                'bank_name' => $user->bank_name,
                'iban' => $this->maskIban($user->iban),
                'bic' => $user->bic,
                'account_holder' => $user->account_holder,
                'verified' => $user->bank_details_verified,
            ],
        ]);
    }

    /**
     * Get bank details for auto-fill (for SafeTrade transactions).
     */
    public function getBankDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasBankDetails()) {
            return response()->json([
                'has_bank_details' => false,
                'data' => null,
            ]);
        }

        return response()->json([
            'has_bank_details' => true,
            'data' => [
                'bank_name' => $user->bank_name,
                'iban' => $user->iban,
                'bic' => $user->bic,
                'account_holder' => $user->account_holder,
                'verified' => $user->bank_details_verified,
            ],
        ]);
    }

    /**
     * Update user preferences (locale, currency).
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'locale' => 'sometimes|string|in:en,de,fr,it,es,nl,pl,pt,ro,cs,sv',
            'currency' => 'sometimes|string|in:EUR,GBP,CHF,PLN,RON,CZK,SEK',
            'country' => 'nullable|string|size:2',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Preferences updated successfully',
            'data' => [
                'locale' => $user->locale,
                'currency' => $user->currency,
                'country' => $user->country,
            ],
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:8', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->update([
            'password' => $validated['new_password'],
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Mask IBAN for display (show only last 4 characters).
     */
    private function maskIban(?string $iban): ?string
    {
        if (!$iban) {
            return null;
        }

        $length = strlen($iban);
        if ($length <= 4) {
            return $iban;
        }

        return str_repeat('•', $length - 4) . substr($iban, -4);
    }
}
