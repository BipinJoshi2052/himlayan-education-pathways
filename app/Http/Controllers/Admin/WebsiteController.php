<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Website-wide design choices — which visual layout a section uses — kept
 * apart from Settings (site-wide values like name, SEO, mail) and from
 * Content Sections (the actual text/image/CTA content, still edited there).
 * Tabbed the same way Settings is; more tabs land here as more sections
 * get an alternate design.
 */
final class WebsiteController extends Controller
{
    /**
     * @var array<string, array<int, string>>
     */
    private const GROUP_KEYS = [
        'hero' => ['hero_layout'],
    ];

    private const HERO_LAYOUTS = ['classic', 'full_image'];

    public function index(): View
    {
        return view('admin.website.index', [
            'heroLayout' => Setting::get('hero_layout', 'classic'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $group = $request->input('group');

        if (! array_key_exists($group, self::GROUP_KEYS)) {
            abort(422, 'Unknown website design group.');
        }

        if ($group === 'hero') {
            $validated = $request->validate([
                'hero_layout' => ['required', 'string', 'in:'.implode(',', self::HERO_LAYOUTS)],
            ]);

            Setting::set('hero_layout', $validated['hero_layout'], 'hero');
        }

        Cache::forget('app_settings');

        return redirect()->route('admin.website.index', ['tab' => $group])->with('status', 'Design saved.');
    }
}
