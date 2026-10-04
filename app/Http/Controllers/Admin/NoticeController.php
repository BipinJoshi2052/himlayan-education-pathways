<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Notice\SaveNoticeAction;
use App\Common\Services\LocaleOptions;
use App\Common\Traits\HasTrashActions;
use App\DTOs\Notice\NoticeData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notice\NoticeRequest;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\QueryBuilder;

final class NoticeController extends Controller
{
    use HasTrashActions;

    public function index(Request $request): View
    {
        $notices = QueryBuilder::for(Notice::class)
            ->filterTrash($request->query('trash'))
            ->allowedSorts('sort_order', 'created_at')
            ->defaultSort('-created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.notices.index', [
            'notices' => $notices,
            'activeCount' => Notice::count(),
            'trashedCount' => Notice::onlyTrashed()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.notices.form', [
            'notice' => new Notice,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function store(NoticeRequest $request, SaveNoticeAction $action): RedirectResponse
    {
        $notice = $action->handle(NoticeData::fromArray($request->validated()), image: $request->file('image'));

        return redirect()->route('admin.notices.edit', $notice)->with('status', 'Notice created.');
    }

    public function edit(Notice $notice): View
    {
        return view('admin.notices.form', [
            'notice' => $notice,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function update(NoticeRequest $request, Notice $notice, SaveNoticeAction $action): RedirectResponse
    {
        $action->handle(NoticeData::fromArray($request->validated()), $notice, $request->file('image'));

        return redirect()->route('admin.notices.edit', $notice)->with('status', 'Notice updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $notice->delete();

        return redirect()->route('admin.notices.index')->with('status', 'Notice deleted.');
    }

    protected function trashModelClass(): string
    {
        return Notice::class;
    }
}
