<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;

/**
 * Public display for CMS-managed pages created in Admin > Pages.
 *
 * This is separate from the site's existing hardcoded pages (about,
 * privacy-policy, terms-conditions, etc.) which continue to work exactly
 * as before at their original routes. CMS pages are served at
 * /page/{slug} so nothing existing is disturbed.
 */
class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        return view('page.show', [
            'page' => $page,
        ]);
    }
}
