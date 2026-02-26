<?php

namespace App\Http\Controllers\Frontend\Student;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * Handles the student locale (language) preference.
 */
class LocaleController extends Controller
{
    private const SUPPORTED = ['en', 'es'];

    /**
     * POST /user/locale
     * Save the authenticated user's locale preference and store in session.
     * Returns JSON for XHR, redirect-back for plain form submissions.
     */
    public function setLocale(Request $request): JsonResponse|RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported locale.',
                ], 422);
            }
            return back();
        }

        // Persist in session immediately (works for guests too)
        $request->session()->put('locale', $locale);

        // Persist in user_prefs for authenticated users
        if ($user = Auth::user()) {
            $user->SetPref('locale', $locale);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale'  => $locale,
            ]);
        }

        return back();
    }
}
