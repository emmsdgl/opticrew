<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'fi'])) {
            abort(400, 'Invalid language');
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);

        // Redirect back to previous page
        return redirect()->back();
    }

    /**
     * API endpoint for AJAX language switching
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchApi(Request $request)
    {
        $locale = $request->input('locale');

        // Validate locale
        if (!in_array($locale, ['en', 'fi'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language'
            ], 400);
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => 'Language switched successfully'
        ]);
    }
}
