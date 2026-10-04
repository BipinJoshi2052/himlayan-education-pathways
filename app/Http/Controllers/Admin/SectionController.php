<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Cms\SaveSectionAction;
use App\Common\Services\LocaleOptions;
use App\Common\Traits\HasTrashActions;
use App\DTOs\Cms\SectionData;
use App\Enums\SectionLayoutType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\SectionRequest;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class SectionController extends Controller
{
    use HasTrashActions;

    public function index(Request $request): View
    {
        $sections = QueryBuilder::for(Section::class)
            ->filterTrash($request->query('trash'))
            ->allowedFilters(
                'page_slug',
                'key',
                AllowedFilter::exact('is_active'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('page_slug', 'like', "%{$value}%")
                            ->orWhere('key', 'like', "%{$value}%")
                            ->orWhere('title->en', 'like', "%{$value}%");
                    });
                }),
            )
            ->allowedSorts('page_slug', 'sort_order', 'created_at')
            ->defaultSort('page_slug')
            ->orderBy('sort_order')
            ->withCount('items')
            ->with('media')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sections.index', [
            'sections' => $sections,
            'activeCount' => Section::count(),
            'trashedCount' => Section::onlyTrashed()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.sections.create', [
            'section' => new Section,
            'layoutTypes' => SectionLayoutType::cases(),
            'locales' => array_keys(LocaleOptions::forAdmin()),
            'catalog' => config('sections.pages'),
            'takenKeys' => $this->takenKeys(null),
        ]);
    }

    /**
     * page_slug => [component keys already used, trashed ones included],
     * so the form can show those components as unavailable.
     *
     * @return array<string, array<int, string>>
     */
    private function takenKeys(?Section $current): array
    {
        return Section::withTrashed()
            ->when($current, fn ($q) => $q->whereKeyNot($current->id))
            ->get(['page_slug', 'key'])
            ->groupBy('page_slug')
            ->map(fn ($rows) => $rows->pluck('key')->values()->all())
            ->all();
    }

    public function store(SectionRequest $request, SaveSectionAction $action): RedirectResponse
    {
        $section = $action->handle(
            SectionData::fromArray($request->validated()),
            image: $request->file('image'),
            itemImages: $this->itemImages($request),
        );

        return redirect()->route('admin.sections.edit', $section)->with('status', 'Section created.');
    }

    public function edit(Section $section): View
    {
        $section->load('items');

        return view('admin.sections.edit', [
            'section' => $section,
            'layoutTypes' => SectionLayoutType::cases(),
            'locales' => array_keys(LocaleOptions::forAdmin()),
            'catalog' => config('sections.pages'),
            'takenKeys' => $this->takenKeys($section),
        ]);
    }

    public function update(SectionRequest $request, Section $section, SaveSectionAction $action): RedirectResponse
    {
        $action->handle(
            SectionData::fromArray($request->validated()),
            $section,
            image: $request->file('image'),
            itemImages: $this->itemImages($request),
        );

        return redirect()->route('admin.sections.edit', $section)->with('status', 'Section updated.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('admin.sections.index')->with('status', 'Section deleted.');
    }

    protected function trashModelClass(): string
    {
        return Section::class;
    }

    /**
     * $request->file('items') mirrors the items[] array shape, so
     * items[3][image] comes back as $itemImages[3]['image'] — same index
     * SectionData::fromArray() preserves via array_map, so these line up
     * with $data->items by key without any extra bookkeeping.
     *
     * @return array<int, \Illuminate\Http\UploadedFile>
     */
    private function itemImages(Request $request): array
    {
        $files = $request->file('items', []);

        return collect($files)
            ->map(fn (array $item) => $item['image'] ?? null)
            ->filter()
            ->all();
    }
}
