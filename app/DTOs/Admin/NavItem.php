<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use App\Models\User;

final readonly class NavItem
{
    public function __construct(
        public string $label,
        public string $route,
        public string $icon,
        public ?string $permission = null,
        /**
         * routeIs() pattern(s) used to decide the sidebar "active" state.
         * Defaults to the link's own route name; override with a wildcard
         * (e.g. 'admin.sections.*') when the item links to one route of a
         * multi-route module (index/create/edit/...) that should all count
         * as that item being active.
         */
        public string $activePattern = '',
    ) {}

    public function isVisibleTo(?User $user): bool
    {
        if ($this->permission === null) {
            return true;
        }

        return $user?->can($this->permission) ?? false;
    }

    public function isActive(): bool
    {
        return request()->routeIs($this->activePattern !== '' ? $this->activePattern : $this->route);
    }
}
