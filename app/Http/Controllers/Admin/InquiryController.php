<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Traits\HasTrashActions;
use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Services\IpGeolocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class InquiryController extends Controller
{
    use HasTrashActions;

    public function index(Request $request, IpGeolocationService $geolocation): View
    {
        $inquiries = QueryBuilder::for(Inquiry::class)
            ->filterTrash($request->query('trash'))
            ->allowedFilters(
                AllowedFilter::exact('status'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%")
                            ->orWhere('subject', 'like', "%{$value}%");
                    });
                }),
            )
            ->allowedSorts('created_at')
            ->defaultSort('-created_at')
            ->paginate(20)
            ->withQueryString();

        // Only the rows actually shown on this page, and only ones we
        // haven't looked up yet — keeps this page load bounded instead of
        // geolocating the whole table every time.
        foreach ($inquiries as $inquiry) {
            $this->resolveLocation($inquiry, $geolocation);
        }

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'unreadCount' => Inquiry::where('status', InquiryStatus::Unread)->count(),
            'activeCount' => Inquiry::count(),
            'trashedCount' => Inquiry::onlyTrashed()->count(),
            'statuses' => InquiryStatus::cases(),
        ]);
    }

    public function show(Inquiry $inquiry, IpGeolocationService $geolocation): View
    {
        if ($inquiry->status === InquiryStatus::Unread) {
            $inquiry->update(['status' => InquiryStatus::Read]);
        }

        $this->resolveLocation($inquiry, $geolocation);

        return view('admin.inquiries.show', ['inquiry' => $inquiry]);
    }

    public function updateNotes(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:unread,read,replied,archived'],
        ]);

        $inquiry->update($validated);

        return back()->with('status', 'Inquiry updated.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('status', 'Inquiry deleted.');
    }

    protected function trashModelClass(): string
    {
        return Inquiry::class;
    }

    private function resolveLocation(Inquiry $inquiry, IpGeolocationService $geolocation): void
    {
        if ($inquiry->location !== null || ! $inquiry->ip_address) {
            return;
        }

        $location = $geolocation->lookup($inquiry->ip_address);

        if ($location !== null) {
            $inquiry->update(['location' => $location]);
        }
    }
}
