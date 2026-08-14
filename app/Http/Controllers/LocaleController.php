<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Switches the UI language between English (default) and Indonesian.
 * Only affects static UI copy — data coming back from the OMDb API is
 * never translated, per the test brief.
 */
class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, ['en', 'id'])) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
