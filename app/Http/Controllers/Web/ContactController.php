<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Actions\Contact\SubmitInquiryAction;
use App\Common\Helpers\ApiResponse;
use App\DTOs\Contact\InquiryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function show(): View
    {
        return view('web.contact');
    }

    public function send(ContactRequest $request, SubmitInquiryAction $action): JsonResponse
    {
        $data = InquiryData::fromRequest($request->validated(), $request->ip(), $request->userAgent());

        $action->handle($data);

        return ApiResponse::success(null, "Thanks — we've received your message and will be in touch.");
    }
}
