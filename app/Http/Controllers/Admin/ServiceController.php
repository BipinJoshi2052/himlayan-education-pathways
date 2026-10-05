<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Services\LocaleOptions;
use App\Common\Traits\HasTrashActions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\ServiceRequest;
use App\Modules\Service\Actions\SaveServiceAction;
use App\Modules\Service\DTOs\ServiceData;
use App\Modules\Service\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class ServiceController extends Controller
{
    use HasTrashActions;

    public function index(Request $request): View
    {
        $services = QueryBuilder::for(Service::class)
            ->filterTrash($request->query('trash'))
            ->allowedFilters(
                AllowedFilter::exact('is_active'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('slug', 'like', "%{$value}%")
                            ->orWhere('title->en', 'like', "%{$value}%");
                    });
                }),
            )
            ->allowedSorts('sort_order', 'created_at')
            ->defaultSort('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'activeCount' => Service::count(),
            'trashedCount' => Service::onlyTrashed()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', [
            'service' => new Service,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function store(ServiceRequest $request, SaveServiceAction $action): RedirectResponse
    {
        $service = $action->handle(
            ServiceData::fromArray($request->validated()),
            coverImage: $request->file('cover_image'),
            brochurePdf: $request->file('brochure_pdf'),
        );

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', [
            'service' => $service,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function update(ServiceRequest $request, Service $service, SaveServiceAction $action): RedirectResponse
    {
        $action->handle(
            ServiceData::fromArray($request->validated()),
            $service,
            $request->file('cover_image'),
            $request->file('brochure_pdf'),
        );

        if ($request->boolean('remove_cover_image') && ! $request->hasFile('cover_image')) {
            $service->clearMediaCollection('cover_image');
        }

        return redirect()->route('admin.services.edit', $service)->with('status', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    protected function trashModelClass(): string
    {
        return Service::class;
    }
}
