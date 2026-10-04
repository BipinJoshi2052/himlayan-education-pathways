<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ToggleThemeModeAction;
use App\Common\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProfileController extends Controller
{
    public function toggleTheme(Request $request, ToggleThemeModeAction $action): JsonResponse
    {
        $themeMode = $action->handle($request->user());

        return ApiResponse::success(['theme_mode' => $themeMode], 'Theme updated.');
    }
}
