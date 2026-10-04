<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\SectionItem;
use App\Modules\Gallery\Models\Gallery;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ReorderController extends Controller
{
    /**
     * Model key (as sent in the URL) => [Eloquent class, permission required to reorder it].
     *
     * @var array<string, array{0: class-string, 1: string}>
     */
    private const ALLOWED_MODELS = [
        'Post' => [Post::class, 'manage-posts'],
        'Service' => [Service::class, 'manage-services'],
        'Gallery' => [Gallery::class, 'manage-galleries'],
        'SectionItem' => [SectionItem::class, 'manage-sections'],
    ];

    public function __invoke(Request $request, string $model): JsonResponse
    {
        if (! array_key_exists($model, self::ALLOWED_MODELS)) {
            return ApiResponse::error('Unknown or disallowed model.', 422, 'INVALID_MODEL');
        }

        [$modelClass, $permission] = self::ALLOWED_MODELS[$model];

        if (! $request->user()->can($permission)) {
            return ApiResponse::error('You do not have permission to reorder this.', 403, 'FORBIDDEN');
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'uuid'],
            'items.*.order' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($modelClass, $validated) {
            foreach ($validated['items'] as $item) {
                $modelClass::whereKey($item['id'])->update(['sort_order' => $item['order']]);
            }
        });

        return ApiResponse::success(null, 'Order updated.');
    }
}
