<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\User;

final readonly class ToggleThemeModeAction
{
    public function handle(User $user): string
    {
        $user->theme_mode = $user->theme_mode === 'dark' ? 'light' : 'dark';
        $user->save();

        return $user->theme_mode;
    }
}
