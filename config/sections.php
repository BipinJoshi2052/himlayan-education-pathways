<?php

/*
 * The content sections the admin can create. The form shows these as
 * dropdowns (page -> component), the layout type is derived from the
 * component, and a page/component pair can only exist once. Stored data is
 * unchanged: a section is still identified by its page_slug and key.
 *
 * Each component lists its editable "settings" fields. The form builds
 * friendly inputs from them instead of a raw JSON box. Field types:
 *   text   — free text, default used when left empty
 *   link   — a destination picked from a list (see SectionSettings::LINKS)
 *   color  — background look picked from a list (see SectionSettings::BACKGROUNDS)
 *
 * Layout values: single_block | repeater | cards (see SectionLayoutType).
 */
return [
    'pages' => [
        'home' => [
            'label' => 'Home page',
            'components' => [
                'hero' => [
                    'label' => 'Hero banner (slides)',
                    'layout' => 'repeater',
                    'settings' => [
                        'show_primary' => ['type' => 'toggle', 'label' => 'Show the first button', 'default' => '1'],
                        'primary_cta' => ['type' => 'text', 'label' => 'First button text', 'default' => 'Explore Courses'],
                        'primary_link' => ['type' => 'link', 'label' => 'First button goes to', 'default' => 'courses'],
                        'show_secondary' => ['type' => 'toggle', 'label' => 'Show the second button', 'default' => '1'],
                        'secondary_cta' => ['type' => 'text', 'label' => 'Second button text', 'default' => 'Enroll Now'],
                        'secondary_link' => ['type' => 'link', 'label' => 'Second button goes to', 'default' => 'contact'],
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
                'stats' => ['label' => 'Statistics counter', 'layout' => 'cards'],
                'journey' => ['label' => 'Journey / process steps', 'layout' => 'repeater'],
                'about' => [
                    'label' => 'About us',
                    'layout' => 'single_block',
                    'settings' => [
                        'cta' => ['type' => 'text', 'label' => 'Button text', 'default' => 'Explore All Courses'],
                        'cta_link' => ['type' => 'link', 'label' => 'Button goes to', 'default' => 'courses'],
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
                'popular-categories' => ['label' => 'Popular categories', 'layout' => 'cards'],
                'why-choose-us' => [
                    'label' => 'Why choose us',
                    'layout' => 'cards',
                    'settings' => [
                        'show_button' => ['type' => 'toggle', 'label' => 'Show the button', 'default' => '1'],
                        'cta' => ['type' => 'text', 'label' => 'Button text', 'default' => 'View All Courses'],
                        'cta_link' => ['type' => 'link', 'label' => 'Button goes to', 'default' => 'courses'],
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
                'leadership' => ['label' => 'Team / leadership', 'layout' => 'repeater'],
                'testimonials' => ['label' => 'Student testimonials', 'layout' => 'repeater'],
                'career-cta' => [
                    'label' => 'Call to action',
                    'layout' => 'single_block',
                    'settings' => [
                        'cta' => ['type' => 'text', 'label' => 'Button text', 'default' => 'Explore German Courses'],
                        'cta_link' => ['type' => 'link', 'label' => 'Button goes to', 'default' => 'courses'],
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
            ],
        ],
        'about' => [
            'label' => 'About page',
            'components' => [
                'about-intro' => [
                    'label' => 'Intro',
                    'layout' => 'single_block',
                    'settings' => [
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
                'about-mission-vision' => ['label' => 'Mission & vision', 'layout' => 'cards'],
                'about-approach' => ['label' => 'Our approach', 'layout' => 'cards'],
                'about-stats' => ['label' => 'Statistics counter', 'layout' => 'repeater'],
                'career-cta' => [
                    'label' => 'Call to action',
                    'layout' => 'single_block',
                    'settings' => [
                        'cta' => ['type' => 'text', 'label' => 'Button text', 'default' => 'Explore German Courses'],
                        'cta_link' => ['type' => 'link', 'label' => 'Button goes to', 'default' => 'courses'],
                        'background' => ['type' => 'color', 'label' => 'Background colour', 'default' => 'image'],
                    ],
                ],
            ],
        ],
        'faq' => [
            'label' => 'FAQ page',
            'components' => [
                'faq-list' => ['label' => 'FAQ list', 'layout' => 'repeater'],
            ],
        ],
    ],
];
