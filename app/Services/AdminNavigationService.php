<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Admin\NavItem;
use App\Models\User;
use Illuminate\Support\Collection;

final class AdminNavigationService
{
    private const DASHBOARD_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>
        </svg>
        SVG;

    private const SHIELD_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M12.0865 22C11.9627 22 11.8388 21.9716 11.7271 21.9137L8.12599 20.0496C7.10415 19.5201 6.30481 18.9259 5.68063 18.2336C4.31449 16.7195 3.5544 14.776 3.54232 12.7599L3.50004 6.12426C3.495 5.35842 3.98931 4.67103 4.72826 4.41215L11.3405 2.10679C11.7331 1.96656 12.1711 1.9646 12.5707 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.779 16.6274 18.434 18.1581C17.8168 18.8602 17.0245 19.4632 16.0128 20.0025L12.4439 21.9088C12.3331 21.9686 12.2103 21.999 12.0865 22Z" fill="currentColor"></path>
            <path d="M11.3194 14.3209C11.1261 14.3219 10.9328 14.2523 10.7838 14.1091L8.86695 12.2656C8.57097 11.9793 8.56795 11.5145 8.86091 11.2262C9.15387 10.9369 9.63207 10.934 9.92906 11.2193L11.3083 12.5451L14.6758 9.22479C14.9698 8.93552 15.448 8.93258 15.744 9.21793C16.041 9.50426 16.044 9.97004 15.751 10.2574L11.8519 14.1022C11.7049 14.2474 11.5127 14.3199 11.3194 14.3209Z" fill="currentColor"></path>
        </svg>
        SVG;

    private const SECTIONS_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M21.25 13.4764C20.429 13.4764 19.761 12.8145 19.761 12.001C19.761 11.1865 20.429 10.5246 21.25 10.5246C21.449 10.5246 21.64 10.4463 21.78 10.3076C21.921 10.1679 22 9.97864 22 9.78146L21.999 7.10415C21.999 4.84102 20.14 3 17.856 3H6.144C3.86 3 2.001 4.84102 2.001 7.10415L2 9.86766C2 10.0648 2.079 10.2541 2.22 10.3938C2.36 10.5325 2.551 10.6108 2.75 10.6108C3.599 10.6108 4.239 11.2083 4.239 12.001C4.239 12.8145 3.571 13.4764 2.75 13.4764C2.336 13.4764 2 13.8093 2 14.2195V16.8949C2 19.158 3.858 21 6.143 21H17.857C20.142 21 22 19.158 22 16.8949V14.2195C22 13.8093 21.664 13.4764 21.25 13.4764Z" fill="currentColor"></path>
            <path d="M15.4303 11.5887L14.2513 12.7367L14.5303 14.3597C14.5783 14.6407 14.4653 14.9177 14.2343 15.0837C14.0053 15.2517 13.7063 15.2727 13.4543 15.1387L11.9993 14.3737L10.5413 15.1397C10.4333 15.1967 10.3153 15.2267 10.1983 15.2267C10.0453 15.2267 9.89434 15.1787 9.76434 15.0847C9.53434 14.9177 9.42134 14.6407 9.46934 14.3597L9.74734 12.7367L8.56834 11.5887C8.36434 11.3907 8.29334 11.0997 8.38134 10.8287C8.47034 10.5587 8.70034 10.3667 8.98134 10.3267L10.6073 10.0897L11.3363 8.61268C11.4633 8.35868 11.7173 8.20068 11.9993 8.20068H12.0013C12.2843 8.20168 12.5383 8.35968 12.6633 8.61368L13.3923 10.0897L15.0213 10.3277C15.2993 10.3667 15.5293 10.5587 15.6173 10.8287C15.7063 11.0997 15.6353 11.3907 15.4303 11.5887Z" fill="currentColor"></path>
        </svg>
        SVG;

    private const NOTICES_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M12 2C8.68629 2 6 4.68629 6 8V11.5L4.2 14.6C3.8 15.3 4.3 16.2 5.1 16.2H18.9C19.7 16.2 20.2 15.3 19.8 14.6L18 11.5V8C18 4.68629 15.3137 2 12 2Z" fill="currentColor"></path>
            <path d="M9.5 19C9.5 20.3807 10.6193 21.5 12 21.5C13.3807 21.5 14.5 20.3807 14.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
        </svg>
        SVG;

    private const USERS_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"></path>
            <path d="M12 14.5C7.30558 14.5 3.5 17.4176 3.5 21C3.5 21.5523 3.94772 22 4.5 22H19.5C20.0523 22 20.5 21.5523 20.5 21C20.5 17.4176 16.6944 14.5 12 14.5Z" fill="currentColor"></path>
        </svg>
        SVG;

    private const POSTS_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M4 4C4 2.89543 4.89543 2 6 2H14L20 8V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V4Z" fill="currentColor"/>
            <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M8 13H16M8 17H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        SVG;

    private const SERVICES_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor"/>
            <path d="M2 17L12 22L22 17M2 12L12 17L22 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        SVG;

    private const GALLERIES_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <rect opacity="0.4" x="2" y="4" width="20" height="16" rx="2" fill="currentColor"/>
            <path d="M2 16L8 10L13 15L16 12L22 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
        </svg>
        SVG;

    private const SETTINGS_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M12 15a3 3 0 100-6 3 3 0 000 6Z" fill="currentColor"/>
            <path d="M19.4 13a7.97 7.97 0 000-2l2.1-1.6a.5.5 0 00.1-.7l-2-3.4a.5.5 0 00-.6-.2l-2.5 1a8 8 0 00-1.7-1l-.4-2.6a.5.5 0 00-.5-.4h-4a.5.5 0 00-.5.4l-.4 2.6a8 8 0 00-1.7 1l-2.5-1a.5.5 0 00-.6.2l-2 3.4a.5.5 0 00.1.7L4.6 11a7.97 7.97 0 000 2l-2.1 1.6a.5.5 0 00-.1.7l2 3.4c.1.2.4.3.6.2l2.5-1c.5.4 1.1.8 1.7 1l.4 2.6c0 .2.3.4.5.4h4c.2 0 .5-.2.5-.4l.4-2.6c.6-.2 1.2-.6 1.7-1l2.5 1c.2.1.5 0 .6-.2l2-3.4a.5.5 0 00-.1-.7L19.4 13Z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        SVG;

    private const INQUIRIES_ICON = <<<'SVG'
        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
            <path opacity="0.4" d="M3 6.5A2.5 2.5 0 015.5 4h13A2.5 2.5 0 0121 6.5v11a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 17.5v-11Z" fill="currentColor"/>
            <path d="M3.5 6.5l8.5 6 8.5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        SVG;

    /**
     * The full admin sidebar menu, unfiltered. Add new items here as admin
     * modules are built — each one is only rendered if `visibleTo()` allows it.
     *
     * @return Collection<int, NavItem>
     */
    public function items(): Collection
    {
        return collect([
            new NavItem(
                label: 'Dashboard',
                route: 'admin.dashboard',
                icon: self::DASHBOARD_ICON,
            ),
            new NavItem(
                label: 'Content Sections',
                route: 'admin.sections.index',
                icon: self::SECTIONS_ICON,
                permission: 'manage-sections',
                activePattern: 'admin.sections.*',
            ),
            new NavItem(
                label: 'Posts & News',
                route: 'admin.posts.index',
                icon: self::POSTS_ICON,
                permission: 'manage-posts',
                activePattern: 'admin.posts.*',
            ),
            new NavItem(
                label: 'Services & Courses',
                route: 'admin.services.index',
                icon: self::SERVICES_ICON,
                permission: 'manage-services',
                activePattern: 'admin.services.*',
            ),
            new NavItem(
                label: 'Galleries',
                route: 'admin.galleries.index',
                icon: self::GALLERIES_ICON,
                permission: 'manage-galleries',
                activePattern: 'admin.galleries.*',
            ),
            new NavItem(
                label: 'Inquiries',
                route: 'admin.inquiries.index',
                icon: self::INQUIRIES_ICON,
                permission: 'manage-inquiries',
                activePattern: 'admin.inquiries.*',
            ),
            new NavItem(
                label: 'Notices',
                route: 'admin.notices.index',
                icon: self::NOTICES_ICON,
                permission: 'manage-notices',
                activePattern: 'admin.notices.*',
            ),
            new NavItem(
                label: 'Page Visits',
                route: 'admin.visits.index',
                icon: self::INQUIRIES_ICON,
                permission: 'manage-visits',
                activePattern: 'admin.visits.*',
            ),
            new NavItem(
                label: 'Users',
                route: 'admin.users.index',
                icon: self::USERS_ICON,
                permission: 'manage-users',
                activePattern: 'admin.users.*',
            ),
            new NavItem(
                label: 'Roles & Permissions',
                route: 'admin.roles.index',
                icon: self::SHIELD_ICON,
                permission: 'manage-roles',
                activePattern: 'admin.roles.*',
            ),
            new NavItem(
                label: 'Settings',
                route: 'admin.settings.index',
                icon: self::SETTINGS_ICON,
                permission: 'manage-settings',
            ),
        ]);
    }

    /**
     * The menu filtered to what the given user is allowed to see.
     *
     * @return Collection<int, NavItem>
     */
    public function visibleTo(?User $user): Collection
    {
        return $this->items()->filter(fn (NavItem $item) => $item->isVisibleTo($user))->values();
    }
}
