<?php

declare(strict_types=1);

/*
 * Static public-site text (navigation, footer, and the fixed labels on each
 * page). Content entered in the admin (sections, posts, courses) is translated
 * per record in the database instead — see the content seed.
 */
return [
    'nav' => [
        'home' => 'Home',
        'about' => 'About',
        'courses' => 'Courses',
        'faq' => 'FAQ',
        'blog' => 'Blog',
        'gallery' => 'Gallery',
        'contact' => 'Contact',
        'enroll_now' => 'Enroll Now',
        'language' => 'Language',
    ],

    'footer' => [
        'quick_links' => 'Quick Links',
        'about_us' => 'About Us',
        'german_courses_link' => 'German Courses',
        'faq' => 'FAQ',
        'blog' => 'Blog',
        'gallery' => 'Gallery',
        'contact_us' => 'Contact Us',
        'german_courses_heading' => 'German Courses',
        'contact_info' => 'Contact Info',
        'tagline' => 'Structured German language classes from A1 to B2 in Chabahil, Kathmandu.',
        'copyright' => 'All Rights Reserved.',
    ],

    'share' => [
        'label' => 'Share:',
    ],

    'home' => [
        'courses_heading' => 'German Language Courses in Kathmandu',
        'view_all_courses' => 'View all Courses',
        'view_course' => 'View Course',
        'students_say' => 'What Our Students Say',
        'testimonial_alt' => 'Student testimonials',
        'blog_heading' => 'Latest Blog & News',
        'blog_subtitle' => 'German learning tips, exam preparation guides and resources for students learning German in Nepal.',
        'read_more' => 'Read More',
    ],

    'about' => [
        'title' => 'About Us',
    ],

    'faq' => [
        'title' => 'Frequently Asked Questions',
        'coming_soon' => 'FAQs coming soon.',
    ],

    'blog' => [
        'title' => 'German Language Learning Blog',
        'no_posts' => 'No posts published yet.',
        'loading_more' => 'Loading more posts...',
        'popular_posts' => 'Popular Posts',
    ],

    'gallery' => [
        'title' => 'Gallery',
        'photos_count' => ':count photos',
        'view_photos' => 'View photos',
        'no_galleries' => 'No galleries yet.',
    ],

    'contact' => [
        'title' => 'Contact Us',
        'our_location' => 'Our Location',
        'telephone' => 'Telephone',
        'send_email' => 'Send Email',
        'name' => 'Name',
        'your_email' => 'Your Email',
        'phone_number' => 'Phone Number',
        'your_subject' => 'Your Subject',
        'your_message' => 'Your Message',
        'send_message' => 'Send Message',
        'get_directions' => 'Get Directions',
        'check_form' => 'Please check the form and try again.',
        'error_generic' => 'Something went wrong. Please try again.',
        'error_short' => 'Something went wrong.',
    ],

    'errors' => [
        '404_title' => 'Page Not Found',
        '404_heading' => 'Oops! Page not found',
        '404_text' => 'Sorry, we could not find the page you were looking for. It may have moved, or the link may be incorrect.',
        '404_back_home' => 'Back to Home',
        '404_image_alt' => 'Page not found',
    ],

    'meta' => [
        'about' => [
            'title' => 'About Us | Himalayan Education Pathways',
            'description' => 'Learn about Himalayan Education Pathways, a German language institute in Chabahil, Kathmandu offering structured A1 to B2 courses.',
        ],
        'faq' => [
            'title' => 'Frequently Asked Questions | Himalayan Education Pathways',
            'description' => 'Answers to common questions about German A1-B2 classes, fees, schedules and Ausbildung preparation at Himalayan Education Pathways, Kathmandu.',
        ],
        'contact' => [
            'title' => 'Contact Us | Himalayan Education Pathways',
            'description' => 'Get in touch with Himalayan Education Pathways in Chabahil, Kathmandu for German language classes. Call, email or visit us today.',
        ],
        'courses_index' => [
            'title' => 'German Courses A1 to B2 | Himalayan Education Pathways',
            'description' => 'Browse German language courses from A1 to B2 in Kathmandu, including exam preparation and Ausbildung-focused classes at Himalayan Education Pathways.',
        ],
        'blog_index' => [
            'title' => 'German Learning Blog | Himalayan Education Pathways',
            'description' => 'German learning tips, exam preparation guides and resources for students learning German in Nepal.',
        ],
        'gallery_index' => [
            'title' => 'Photo Gallery | Himalayan Education Pathways',
            'description' => 'Browse photos from classes, events and student activities at Himalayan Education Pathways in Chabahil, Kathmandu.',
        ],
        'privacy' => [
            'title' => 'Privacy Policy | Himalayan Education Pathways',
            'description' => 'How Himalayan Education Pathways collects, uses and protects your personal information on this website.',
        ],
        'terms' => [
            'title' => 'Terms of Use | Himalayan Education Pathways',
            'description' => 'The terms and conditions for using the Himalayan Education Pathways website and enquiring about our German language courses.',
        ],
    ],

    'courses' => [
        'title' => 'German Language Courses',
        'coming_soon' => 'Courses coming soon.',
        'features' => 'Course Features',
        'duration' => 'Duration',
        'fee' => 'Fee',
        'other_courses' => 'Other Courses',
    ],
];
