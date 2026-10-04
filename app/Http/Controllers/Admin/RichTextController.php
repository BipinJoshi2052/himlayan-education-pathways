<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Backs the image button in every admin rich-text (Quill) editor — see
 * public/admin-assets/js/rich-text.js. Quill's default image button embeds
 * a base64 data URI straight into the stored HTML, which bloats the
 * translatable JSON columns fast; this uploads to disk instead and hands
 * back a real URL.
 */
final class RichTextController extends Controller
{
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('rich-text', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }
}
