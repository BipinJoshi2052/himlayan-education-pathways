<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\SectionLayoutType;
use App\Models\Section;
use App\Models\Setting;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Real production content for Himalayan Education Pathways, transcribed
 * from docs/website-content.md — not placeholder/dummy data. Safe to
 * re-run (every write is firstOrCreate/updateOrCreate keyed on the same
 * natural key the content doc uses, e.g. page_slug+key, slug).
 */
class HimalayanContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedHomeSections();
        $this->seedAboutSections();
        $this->seedFaq();
        $this->seedServices();
        $this->seedPosts();
    }

    private function seedSettings(): void
    {
        Setting::set('site_name', 'Himalayan Education Pathways', 'general');
        Setting::set('site_tagline', 'Structured German language classes from A1 to B2 in Chabahil, Kathmandu.', 'general');
        Setting::set('site_address', 'Shrestha Tailoring Building, 4th Floor / AC Complex, 4th Floor, Chabahil–7, Kathmandu, Nepal', 'general');
        // No real contact_email/contact_phone/admin_notification_email was
        // given anywhere in website-content.md (only social links + address
        // + registration/PAN) — deliberately left unset rather than
        // inventing one. Fill these in via Settings > General before launch.

        Setting::set('seo_meta_title', ['en' => 'German Language Classes in Kathmandu | Himalayan Education Pathways'], 'seo');
        Setting::set('seo_meta_description', ['en' => 'Learn German A1, A2, B1 and B2 at Himalayan Education Pathways in Chabahil, Kathmandu. Join structured German language classes, exam preparation and practical speaking sessions.'], 'seo');
        Setting::set('seo_meta_keywords', ['en' => 'German classes in Kathmandu, German language institute Kathmandu, German classes Chabahil, German language course Kathmandu, German classes Nepal, Learn German Kathmandu, German A1 classes Kathmandu, German B1 classes Kathmandu, German language institute Nepal'], 'seo');

        Setting::set('social_facebook_url', 'https://www.facebook.com/p/Himalayan-Education-Pathways-61589273141803/', 'social');
        Setting::set('social_instagram_url', 'https://www.instagram.com/himalayaneducationpathways/', 'social');
        Setting::set('social_tiktok_url', 'https://www.tiktok.com/@himalayan.edupathways25', 'social');
    }

    private function seedHomeSections(): void
    {
        // Repeater, not SingleBlock: each item is one hero slide (title +
        // content + its own image). A single item renders as the current
        // plain hero; 2+ items become an owl-carousel — see home.blade.php
        // and docs/public-website.md.
        $this->sectionWithItems('home', 'hero', SectionLayoutType::Repeater, sortOrder: 1, data: [
            'title' => 'Learn German. Build Your Path to Germany.',
            'settings' => ['primary_cta' => 'Explore Courses', 'secondary_cta' => 'Enroll Now'],
        ], items: [
            [
                'title' => 'Learn German. Build Your Path to Germany.',
                'description' => "Start your German language journey with Himalayan Education Pathways, your destination for structured German language classes in Chabahil, Kathmandu.\n\nLearn German from A1 to B2 through practical lessons focused on speaking, listening, reading, writing, grammar and vocabulary.\n\nWhether you're learning German for Ausbildung, higher education, career opportunities, examinations or life in Germany, start building your language skills with us.",
            ],
        ]);

        $this->sectionWithItems('home', 'stats', SectionLayoutType::Repeater, sortOrder: 2, data: [
            'title' => 'Home Statistics',
        ], items: [
            ['title' => '4', 'description' => 'German Language Levels'],
            ['title' => '2 Hours', 'description' => 'Daily Learning Sessions'],
            ['title' => 'A1–B2', 'description' => 'Structured German Courses'],
            ['title' => 'Chabahil', 'description' => 'Convenient Kathmandu Location'],
        ]);

        $this->sectionWithItems('home', 'journey', SectionLayoutType::Repeater, sortOrder: 3, data: [
            'title' => 'Start Your German Learning Journey With Us',
            'content' => 'Learning a new language opens doors to new cultures, education and professional opportunities. At Himalayan Education Pathways, we provide structured German language education designed to help learners progress confidently from beginner to upper-intermediate levels.',
        ], items: [
            ['title' => 'Experienced Guidance', 'icon_or_badge' => '01', 'description' => 'Learn German through structured lessons with guidance designed to make grammar, vocabulary and communication easier to understand.'],
            ['title' => 'Quality Education', 'icon_or_badge' => '02', 'description' => 'Develop speaking, listening, reading and writing skills through a balanced and progressive learning approach.'],
            ['title' => 'Practical Learning', 'icon_or_badge' => '03', 'description' => 'Use German actively through conversations, pronunciation exercises, listening activities and practical classroom sessions.'],
            ['title' => 'Continuous Support', 'icon_or_badge' => '04', 'description' => 'Receive guidance throughout your journey from German A1 through A2, B1 and B2.'],
        ]);

        $this->sectionWithItems('home', 'about', SectionLayoutType::SingleBlock, sortOrder: 4, data: [
            'title' => 'Learn German in Kathmandu With Himalayan Education Pathways',
            'content' => 'Himalayan Education Pathways is a German language institute located in Chabahil, Kathmandu, providing structured language education from beginner A1 through B2. Our courses are designed to develop practical communication alongside grammar, vocabulary, pronunciation, listening, reading and writing. Whether you are preparing for a German language examination, Ausbildung, higher education, professional opportunities or everyday life in Germany, we provide a structured path for improving your German.',
            'settings' => ['cta' => 'Explore All Courses'],
        ], items: [
            ['title' => 'German language courses from A1 to B2'],
            ['title' => 'Speaking and pronunciation practice'],
            ['title' => 'Grammar and vocabulary development'],
            ['title' => 'German exam preparation'],
            ['title' => 'Ausbildung-oriented language preparation'],
            ['title' => 'Practical communication exercises'],
            ['title' => 'Convenient Chabahil location'],
        ]);

        $this->sectionWithItems('home', 'popular-categories', SectionLayoutType::Cards, sortOrder: 5, data: [
            'title' => 'Explore Our German Learning Programs',
            'content' => 'Choose the learning path that matches your current German level and future goals.',
        ], items: [
            ['title' => 'German A1', 'description' => 'Start German from the beginning and learn essential vocabulary, grammar and everyday communication.'],
            ['title' => 'German A2', 'description' => 'Expand your basic German skills and communicate more confidently in familiar everyday situations.'],
            ['title' => 'German B1', 'description' => 'Develop independent German communication skills for study, work, Ausbildung and everyday situations.'],
            ['title' => 'German B2', 'description' => 'Build stronger fluency and communicate confidently about more complex academic, professional and everyday topics.'],
            ['title' => 'Exam Preparation', 'description' => 'Prepare your speaking, listening, reading and writing skills for German language examinations.'],
            ['title' => 'Ausbildung Preparation', 'description' => 'Develop German language skills for learners exploring vocational training opportunities in Germany.'],
            ['title' => 'German Speaking Practice', 'description' => 'Improve pronunciation, fluency and confidence through practical German conversations.'],
            ['title' => 'German for Germany', 'description' => 'Build useful language skills for learners planning to study, work or live in Germany.'],
        ]);

        $this->sectionWithItems('home', 'why-choose-us', SectionLayoutType::Cards, sortOrder: 6, data: [
            'title' => 'Why Choose Himalayan Education Pathways for German Classes?',
            'content' => 'Learning German requires consistency, practical communication and a clear progression from one language level to another. Our programs are structured to help learners gradually build confidence while developing all four essential language skills.',
            'settings' => ['cta' => 'View All Courses'],
        ], items: [
            ['title' => 'Structured A1–B2 Progression', 'description' => 'Progress systematically from beginner A1 through A2, B1 and B2.'],
            ['title' => 'Practical Speaking', 'description' => 'Develop confidence through regular speaking, pronunciation and conversation activities.'],
            ['title' => 'Exam-Oriented Preparation', 'description' => 'Strengthen the skills required for German language examinations through focused practice.'],
            ['title' => 'Germany-Focused Learning', 'description' => 'Suitable for learners studying German for Ausbildung, education, professional opportunities and life in Germany.'],
            ['title' => 'Convenient Location', 'description' => 'Attend German language classes conveniently in Chabahil, Kathmandu.'],
            ['title' => 'Balanced Language Skills', 'description' => 'Develop speaking, listening, reading and writing alongside grammar and vocabulary.'],
        ]);

        $this->sectionWithItems('home', 'leadership', SectionLayoutType::Repeater, sortOrder: 7, data: [
            'title' => 'Meet Our Leadership',
            'content' => 'Meet the people guiding Himalayan Education Pathways and its commitment to quality German language education.',
        ], items: [
            ['title' => 'Jeevan Thakuri', 'icon_or_badge' => 'Chief Executive Officer', 'description' => 'Committed to building an accessible and supportive German learning environment for students preparing for education, career and personal opportunities.'],
            ['title' => 'Gaurav Jung Shah', 'icon_or_badge' => 'Managing Director', 'description' => 'Focused on providing students with structured learning, practical guidance and a positive environment throughout their German language journey.'],
        ]);

        // Sample/placeholder testimonials — the source content doc explicitly
        // flags these as placeholders ("Replace these placeholders with
        // genuine student testimonials before publication"). Seeded as-is,
        // labeled "Sample Student" exactly as given, so the placeholder
        // nature stays visible in the data itself.
        $this->sectionWithItems('home', 'testimonials', SectionLayoutType::Repeater, sortOrder: 8, data: [
            'title' => 'What Our Students Say',
        ], items: [
            ['title' => 'Sample Student — German A1', 'description' => 'The classes helped me understand German grammar much more clearly. Regular speaking practice also made me more confident using the language.'],
            ['title' => 'Sample Student — German B1', 'description' => 'The lessons were structured and easy to follow. The speaking and writing practice helped me understand where I needed to improve.'],
            ['title' => 'Sample Student — Ausbildung Preparation', 'description' => 'I wanted a clear learning path for German instead of studying randomly by myself. The classes helped me progress step by step.'],
        ]);

        $this->section('home', 'career-cta', SectionLayoutType::SingleBlock, sortOrder: 9, data: [
            'title' => 'Planning Your Future in Germany?',
            'content' => "Build the German language foundation required for your future plans.\n\nWhether your goal is Ausbildung, higher education, professional development or everyday communication, start your German journey with structured language learning.",
            'settings' => ['cta' => 'Explore German Courses'],
        ]);
    }

    private function seedAboutSections(): void
    {
        $this->sectionWithItems('about', 'about-intro', SectionLayoutType::SingleBlock, sortOrder: 1, data: [
            'title' => 'Start Your German Journey With Us',
            'content' => 'Himalayan Education Pathways is a German language education institute located in Chabahil–7, Kathmandu, Nepal. Our goal is to provide structured, practical and accessible German language education to learners preparing for academic, professional and personal opportunities. From complete beginners starting A1 to learners progressing through A2, B1 and B2, our courses focus on building confidence across speaking, listening, reading and writing.',
        ], items: [
            ['title' => 'German A1–B2 courses'],
            ['title' => 'Practical speaking sessions'],
            ['title' => 'Grammar and vocabulary development'],
            ['title' => 'Exam-oriented preparation'],
            ['title' => 'Ausbildung-focused German preparation'],
            ['title' => 'Supportive learning environment'],
            ['title' => 'Convenient Chabahil location'],
        ]);

        $this->sectionWithItems('about', 'about-mission-vision', SectionLayoutType::Cards, sortOrder: 2, data: [
            'title' => 'Our Mission & Vision',
        ], items: [
            ['title' => 'Our Mission', 'description' => 'To provide structured and practical German language education that helps learners develop the communication skills and confidence required to pursue their academic, professional and personal goals.'],
            ['title' => 'Our Vision', 'description' => 'To become a trusted destination for German language education in Nepal by creating an effective, supportive and student-focused learning environment.'],
        ]);

        $this->sectionWithItems('about', 'about-approach', SectionLayoutType::Cards, sortOrder: 3, data: [
            'title' => 'Our Approach',
        ], items: [
            ['title' => 'Structured Learning', 'description' => 'Progress logically through German language levels from A1 to B2.'],
            ['title' => 'Practical Communication', 'description' => 'Apply vocabulary and grammar through speaking and real-world communication activities.'],
            ['title' => 'Balanced Skills', 'description' => 'Develop speaking, listening, reading and writing together.'],
            ['title' => 'Student Guidance', 'description' => 'Receive consistent guidance throughout your language-learning journey.'],
        ]);

        $this->sectionWithItems('about', 'about-stats', SectionLayoutType::Repeater, sortOrder: 4, data: [
            'title' => 'About Page Statistics',
        ], items: [
            ['title' => '4', 'description' => 'German Levels'],
            ['title' => 'A1–B2', 'description' => 'Complete Learning Path'],
            ['title' => '2 Hours', 'description' => 'Daily Classes'],
            ['title' => 'Kathmandu', 'description' => 'Local German Learning Center'],
        ]);

        $this->section('about', 'career-cta', SectionLayoutType::SingleBlock, sortOrder: 5, data: [
            'title' => 'Planning Your Future in Germany?',
            'content' => "Build the German language foundation required for your future plans.\n\nWhether your goal is Ausbildung, higher education, professional development or everyday communication, start your German journey with structured language learning.",
            'settings' => ['cta' => 'Explore German Courses'],
        ]);
    }

    private function seedFaq(): void
    {
        $this->sectionWithItems('faq', 'faq-list', SectionLayoutType::Repeater, sortOrder: 1, data: [
            'title' => 'Frequently Asked Questions',
        ], items: [
            ['title' => 'Where can I learn German in Kathmandu?', 'description' => 'Himalayan Education Pathways provides German language classes from A1 to B2 at our institute in Chabahil–7, Kathmandu.'],
            ['title' => 'What German courses do you offer?', 'description' => 'We currently provide German A1, A2, B1 and B2 language courses.'],
            ['title' => 'Can complete beginners join German A1?', 'description' => 'Yes. German A1 is specifically designed for learners starting German with little or no previous knowledge.'],
            ['title' => 'How long is the German A1 course?', 'description' => 'The A1 course runs for approximately two months with around two hours of classes per day.'],
            ['title' => 'How long is German A2?', 'description' => 'German A2 runs for approximately two months with around two hours of classes per day.'],
            ['title' => 'How long is German B1?', 'description' => 'German B1 runs for approximately 2.5 months with around two hours of classes per day.'],
            ['title' => 'How long is German B2?', 'description' => 'German B2 runs for approximately 2.5 months with around two hours of classes per day.'],
            ['title' => 'Do you provide German exam preparation?', 'description' => 'Yes. Exam-oriented preparation can include speaking, listening, reading, writing and mock-test exercises for A1–B2 learners.'],
            ['title' => 'Do you provide Goethe exam preparation?', 'description' => 'Students preparing for recognized German language examinations, including Goethe examinations, can contact us regarding preparation options.'],
            ['title' => 'Do you provide German classes for Ausbildung?', 'description' => 'Yes. Learners preparing for Ausbildung can study progressively from German A1 through B1/B2 according to their individual requirements.'],
            ['title' => 'What German level is required for Ausbildung?', 'description' => 'Requirements vary depending on the Ausbildung program, employer and other applicable conditions. Always verify the specific requirements of your intended program.'],
            ['title' => 'Do you provide German speaking practice?', 'description' => 'Speaking and practical communication form an important part of our German language courses.'],
            ['title' => 'Where are you located?', 'description' => 'We are located at Shrestha Tailoring Building / AC Complex, 4th Floor, Chabahil–7, Kathmandu, Nepal.'],
        ]);
    }

    private function seedServices(): void
    {
        $a1Description = '<h4>Overview</h4>'
            .'<p>German A1 develops the fundamental skills required to begin communicating in German. Students gradually learn how to introduce themselves, ask basic questions, understand simple conversations and communicate about common everyday topics.</p>'
            .'<h4>What You\'ll Learn</h4>'
            .'<ul><li>German alphabet and pronunciation</li><li>Greetings and introductions</li><li>Numbers and dates</li><li>Time and schedules</li><li>Family and relationships</li><li>Food and drinks</li><li>Shopping</li><li>Daily routines</li><li>Basic directions</li><li>Personal information</li><li>Basic sentence structure</li><li>Essential grammar</li><li>Beginner vocabulary</li><li>Simple conversations</li><li>Reading basic German</li><li>Writing simple messages</li></ul>'
            .'<h4>Skills Developed</h4>'
            .'<p><strong>Speaking:</strong> Introduce yourself and participate in simple everyday conversations.</p>'
            .'<p><strong>Listening:</strong> Understand basic expressions and slowly spoken German.</p>'
            .'<p><strong>Reading:</strong> Understand simple notices, messages and short texts.</p>'
            .'<p><strong>Writing:</strong> Write basic sentences, messages and personal information.</p>';

        $a2Description = '<p>German A2 builds upon the foundation developed during A1. Students learn to communicate more confidently about familiar situations such as family, education, shopping, travel, work and daily activities.</p>'
            .'<h4>Key Learning Areas</h4>'
            .'<ul><li>Expanded vocabulary</li><li>A2 grammar</li><li>Everyday conversations</li><li>Describing experiences</li><li>Talking about past events</li><li>Travel situations</li><li>Work and education</li><li>Reading comprehension</li><li>Listening exercises</li><li>Structured writing</li><li>Speaking practice</li></ul>';

        $b1Description = '<p>German B1 helps learners become increasingly independent German users. Students develop the ability to communicate about familiar topics, describe experiences, explain plans and express opinions.</p>'
            .'<h4>Key Learning Areas</h4>'
            .'<ul><li>Intermediate German grammar</li><li>Expanded vocabulary</li><li>German conversations</li><li>Expressing opinions</li><li>Describing experiences</li><li>Reading longer texts</li><li>Listening comprehension</li><li>Structured writing</li><li>Speaking exercises</li><li>Exam-oriented activities</li></ul>'
            .'<p>B1 can be particularly relevant for learners developing German skills for Ausbildung, study, work or life in Germany.</p>';

        $b2Description = '<p>German B2 focuses on developing stronger fluency, accuracy and independent communication. Learners work with more complex vocabulary, grammar, texts and conversations.</p>'
            .'<h4>Key Learning Areas</h4>'
            .'<ul><li>Advanced grammar</li><li>Expanded vocabulary</li><li>Complex conversations</li><li>Expressing detailed opinions</li><li>Professional communication</li><li>Reading complex texts</li><li>Listening comprehension</li><li>Structured writing</li><li>Speaking fluency</li><li>Exam-oriented preparation</li></ul>';

        $examDescription = '<p>Preparing for a German language examination requires both language knowledge and familiarity with exam-style activities. Our exam preparation focuses on developing confidence across the four major language skills.</p>'
            .'<p><strong>Speaking:</strong> Practice structured conversations, pronunciation and responding to common speaking tasks.</p>'
            .'<p><strong>Listening:</strong> Develop strategies for understanding spoken German and identifying important information.</p>'
            .'<p><strong>Reading:</strong> Improve comprehension of German texts, notices, messages and longer passages.</p>'
            .'<p><strong>Writing:</strong> Practice structured German writing appropriate to your language level.</p>'
            .'<p><strong>Mock Test Practice:</strong> Work with exam-oriented exercises to identify areas that need improvement.</p>'
            .'<h4>Available Levels</h4>'
            .'<ul><li>A1 Exam Preparation</li><li>A2 Exam Preparation</li><li>B1 Exam Preparation</li><li>B2 Exam Preparation</li></ul>'
            .'<p><em>Himalayan Education Pathways provides German language and exam preparation. Unless formally authorized, the institute should not be presented as an official Goethe examination center.</em></p>';

        $ausbildungDescription = '<p>Planning to pursue vocational training in Germany? Developing German communication skills is an important part of preparing for Ausbildung and everyday life in Germany. Himalayan Education Pathways provides structured German courses from A1 through B2 for learners preparing for their future in Germany.</p>'
            .'<h4>Build Your German Step by Step</h4>'
            .'<p><strong>A1:</strong> Build your German foundation.</p>'
            .'<p><strong>A2:</strong> Become more confident in everyday communication.</p>'
            .'<p><strong>B1:</strong> Develop independent German communication.</p>'
            .'<p><strong>B2:</strong> Strengthen fluency for more complex situations.</p>'
            .'<h4>What You\'ll Develop</h4>'
            .'<ul><li>Everyday German communication</li><li>Speaking confidence</li><li>Listening comprehension</li><li>Grammar</li><li>Vocabulary</li><li>Reading</li><li>Writing</li><li>Practical German communication</li></ul>'
            .'<p><em>Language requirements differ between Ausbildung programs, employers and circumstances. Students should verify the requirements applicable to their individual pathway.</em></p>';

        $services = [
            ['slug' => 'german-a1-classes-kathmandu', 'title' => 'German A1', 'summary' => 'Build a strong foundation in German with essential vocabulary, basic grammar, pronunciation and everyday communication.', 'description' => $a1Description, 'duration' => '2 Months', 'sort_order' => 1, 'is_featured' => true, 'meta_title' => 'German A1 Classes in Kathmandu | A1 German Course Nepal', 'meta_description' => 'Join German A1 classes in Kathmandu at Himalayan Education Pathways. Learn beginner German grammar, vocabulary, speaking, listening, reading and writing.'],
            ['slug' => 'german-a2-classes-kathmandu', 'title' => 'German A2', 'summary' => 'Continue your German journey by expanding your vocabulary, grammar and communication skills for everyday situations.', 'description' => $a2Description, 'duration' => '2 Months', 'sort_order' => 2, 'is_featured' => true],
            ['slug' => 'german-b1-classes-kathmandu', 'title' => 'German B1', 'summary' => 'Develop independent German communication skills through intermediate speaking, listening, reading, writing and grammar.', 'description' => $b1Description, 'duration' => '2.5 Months', 'sort_order' => 3, 'is_featured' => true],
            ['slug' => 'german-b2-classes-kathmandu', 'title' => 'German B2', 'summary' => 'Improve fluency, accuracy and confidence while developing advanced vocabulary, grammar and communication abilities.', 'description' => $b2Description, 'duration' => '2.5 Months', 'sort_order' => 4, 'is_featured' => true],
            ['slug' => 'german-exam-preparation-kathmandu', 'title' => 'German Exam Preparation', 'summary' => 'Prepare across speaking, listening, reading and writing with exam-oriented exercises and mock-test activities.', 'description' => $examDescription, 'duration' => null, 'sort_order' => 5, 'is_featured' => false, 'meta_title' => 'German Exam Preparation Kathmandu | A1 A2 B1 B2'],
            ['slug' => 'german-language-for-ausbildung-nepal', 'title' => 'German for Ausbildung', 'summary' => 'Build German communication skills progressively from A1 toward the language level appropriate for your individual Ausbildung pathway.', 'description' => $ausbildungDescription, 'duration' => null, 'sort_order' => 6, 'is_featured' => false, 'meta_title' => 'German Language for Ausbildung Nepal | German Classes Kathmandu'],
        ];

        foreach ($services as $data) {
            $service = Service::firstOrNew(['slug' => $data['slug']]);
            $service->title = $data['title'];
            $service->slug = $data['slug'];
            $service->summary = $data['summary'];
            $service->description = $data['description'];
            $service->duration = $data['duration'];
            $service->is_active = true;
            $service->is_featured = $data['is_featured'];
            $service->sort_order = $data['sort_order'];
            if (isset($data['meta_title'])) {
                $service->meta_title = $data['meta_title'];
            }
            if (isset($data['meta_description'])) {
                $service->meta_description = $data['meta_description'];
            }
            $service->save();
        }
    }

    private function seedPosts(): void
    {
        $a1GuideContent = '<p>German A1 is the starting point for learners beginning their German language journey. Whether you\'re learning German for Ausbildung, education, professional opportunities, travel or future plans in Germany, developing a strong A1 foundation can make progression to higher levels much easier.</p>'
            .'<h3>What Is German A1?</h3>'
            .'<p>A1 is the beginner level within the Common European Framework of Reference for Languages (CEFR). At this stage, learners begin developing the ability to understand and use simple German expressions related to familiar everyday situations.</p>'
            .'<h3>What Do You Learn in German A1?</h3>'
            .'<p>Typical topics include:</p>'
            .'<ul><li>Greetings</li><li>Introductions</li><li>Numbers</li><li>Dates</li><li>Time</li><li>Family</li><li>Food</li><li>Shopping</li><li>Daily routines</li><li>Directions</li><li>Personal information</li><li>Everyday vocabulary</li></ul>'
            .'<p>Students also begin learning essential German grammar and sentence structures.</p>'
            .'<h3>Develop All Four Language Skills</h3>'
            .'<p><strong>Speaking:</strong> Practice introducing yourself, asking questions and responding in simple conversations.</p>'
            .'<p><strong>Listening:</strong> Become familiar with common German expressions and basic spoken conversations.</p>'
            .'<p><strong>Reading:</strong> Learn to understand simple German notices, messages and short texts.</p>'
            .'<p><strong>Writing:</strong> Practice writing basic sentences, messages and personal information.</p>'
            .'<h3>Learn Vocabulary in Context</h3>'
            .'<p>Instead of memorizing isolated words, learn vocabulary around topics such as family, food, travel and daily activities. This makes vocabulary easier to recall when speaking and writing.</p>'
            .'<h3>Practice German Every Day</h3>'
            .'<p>Consistency is important when learning a new language. Even short daily activities can help:</p>'
            .'<ul><li>Review vocabulary</li><li>Listen to German</li><li>Read simple German texts</li><li>Practice speaking</li><li>Write basic sentences</li><li>Review grammar</li></ul>'
            .'<h3>Take Structured German Classes</h3>'
            .'<p>Structured classes provide learners with a clear progression and regular opportunities to practice German. Students looking for German A1 classes in Kathmandu can begin their learning journey at Himalayan Education Pathways in Chabahil.</p>';

        $posts = [
            ['slug' => 'how-to-learn-german-a1-nepal', 'type' => 'post', 'title' => "How to Learn German A1 in Nepal: A Beginner's Guide", 'summary' => "Starting German for the first time? Learn what A1 covers and how to build a strong foundation in grammar, vocabulary, speaking and listening.", 'content' => $a1GuideContent, 'meta_title' => "How to Learn German A1 in Nepal | Beginner's Guide", 'meta_description' => "Learn how to start German A1 in Nepal, including vocabulary, grammar, speaking, listening, reading and effective beginner study strategies."],
            ['slug' => 'german-a1-exam-guide', 'type' => 'post', 'title' => 'German A1 Exam: Complete Preparation Guide', 'summary' => 'Understand the key skills involved in A1-level German examinations and how to prepare speaking, listening, reading and writing.'],
            ['slug' => 'german-a1-vs-a2', 'type' => 'post', 'title' => "German A1 vs A2: What's the Difference?", 'summary' => 'Understand the differences between A1 and A2 German, including vocabulary, grammar and communication expectations.'],
            ['slug' => 'how-long-to-learn-german-b1', 'type' => 'post', 'title' => 'How Long Does It Take to Learn German B1?', 'summary' => 'Explore what B1 involves and the factors that can influence how quickly a learner progresses.'],
            ['slug' => 'german-language-levels-a1-to-b2', 'type' => 'post', 'title' => 'German Language Levels A1 to B2 Explained', 'summary' => 'Understand the progression from beginner A1 to upper-intermediate B2 and what learners generally develop at each stage.'],
            ['slug' => 'goethe-b1-exam-preparation-nepal', 'type' => 'post', 'title' => 'Goethe B1 Exam Preparation in Nepal', 'summary' => 'Explore practical strategies for developing speaking, listening, reading and writing skills when preparing for a B1 German examination.'],
            ['slug' => 'german-b2-exam-preparation', 'type' => 'post', 'title' => 'German B2 Exam Preparation Guide', 'summary' => 'Learn how to approach advanced reading, writing, listening and speaking activities when preparing at B2 level.'],
            ['slug' => 'german-language-for-ausbildung', 'type' => 'post', 'title' => 'German Language for Ausbildung: What You Should Know', 'summary' => 'Understand why German language ability matters for Ausbildung and how learners can progress through A1, A2, B1 and B2.'],
            ['slug' => 'german-speaking-exam-preparation', 'type' => 'post', 'title' => 'How to Prepare for a German Speaking Exam', 'summary' => 'Learn practical techniques for improving pronunciation, vocabulary, fluency and confidence before a German speaking examination.'],
        ];

        foreach ($posts as $index => $data) {
            $post = Post::firstOrNew(['slug' => $data['slug']]);
            $post->type = PostType::from($data['type']);
            $post->title = $data['title'];
            $post->slug = $data['slug'];
            $post->summary = $data['summary'];
            // Only the A1 guide has a full article body in the source
            // content doc — the other 8 fall back to their summary as a
            // placeholder-length body rather than inventing content that
            // wasn't provided. Replace with real articles before launch.
            $post->content = $data['content'] ?? $data['summary'];
            $post->status = PostStatus::Published;
            $post->published_at = now()->subDays((count($posts) - $index) * 3);
            $post->sort_order = $index + 1;
            if (isset($data['meta_title'])) {
                $post->meta_title = $data['meta_title'];
            }
            if (isset($data['meta_description'])) {
                $post->meta_description = $data['meta_description'];
            }
            $post->save();
        }
    }

    /**
     * @param  array{title?: string, content?: string, settings?: array}  $data
     */
    private function section(string $pageSlug, string $key, SectionLayoutType $layout, int $sortOrder, array $data): Section
    {
        $section = Section::firstOrNew(['page_slug' => $pageSlug, 'key' => $key]);
        $section->page_slug = $pageSlug;
        $section->key = $key;
        $section->layout_type = $layout;
        $section->title = $data['title'] ?? '';
        $section->content = $data['content'] ?? null;
        $section->settings = $data['settings'] ?? null;
        $section->is_active = true;
        $section->sort_order = $sortOrder;
        $section->save();

        return $section;
    }

    /**
     * @param  array{title?: string, content?: string, settings?: array}  $data
     * @param  array<int, array{title: string, description?: string, icon_or_badge?: string}>  $items
     */
    private function sectionWithItems(string $pageSlug, string $key, SectionLayoutType $layout, int $sortOrder, array $data, array $items): Section
    {
        $section = $this->section($pageSlug, $key, $layout, $sortOrder, $data);

        foreach ($items as $index => $itemData) {
            $item = $section->items()->firstOrNew(['title->en' => $itemData['title']]);
            $item->title = $itemData['title'];
            $item->description = $itemData['description'] ?? null;
            $item->icon_or_badge = $itemData['icon_or_badge'] ?? null;
            $item->sort_order = $index + 1;
            $item->save();
        }

        return $section;
    }
}
