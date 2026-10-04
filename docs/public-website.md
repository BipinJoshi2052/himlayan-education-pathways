# Public Website (Eduleb template + real content)

## Current state (as of 2026-10-02)

The public-facing site is built from the `website-template/eduleb-redone` HTML template (assets copied to `public/web-assets/`, 15MB — the whole template, since multiple real pages use its `img/course`, `img/team`, `img/blog`, `img/testimonial`, `img/bg` folders, unlike the admin template where most of a 24MB demo was unused), wired entirely to the backend built in earlier phases — no static content in the public Blade views.

| Page | Route | Pulls from |
|---|---|---|
| Home | `web.home` → `/` | `Section`s (`page_slug='home'`): `hero`, `stats`, `journey`, `about`, `popular-categories`, `why-choose-us`, `leadership`, `testimonials`, `career-cta`; active `Service`s; 3 latest published `Post`s |
| About | `web.about` → `/about` | `Section`s (`page_slug='about'`): `about-intro`, `about-mission-vision`, `about-approach`, `about-stats`, `career-cta` |
| Courses | `web.courses.index`/`.show` → `/courses`, `/courses/{slug}` | `Service` (active, by slug) |
| FAQ | `web.faq` → `/faq` | `Section` `page_slug='faq'` `key='faq-list'` (repeater; title=question, description=answer) |
| Blog | `web.blog.index`/`.show` → `/blog`, `/blog/{slug}` | `Post` (published, by slug) |
| Contact | `web.contact` → `/contact` | Form posts to the existing `contact.send` endpoint (see [inquiries.md](inquiries.md)) via `fetch()`, not a page reload |

`resources/views/layouts/web.blade.php` is the shared shell — nav (incl. a Courses dropdown built from active `Service`s), footer (business info, social links, contact info) all read from `Setting`, and `<x-web.seo-head>` ([settings.md](settings.md)) in the `<head>`.

### No real logo or business photos exist

The navbar/footer "logo" is plain text (`$siteName` from Settings), not an `<img>` — no logo file was provided, and fabricating one felt worse than a clean text wordmark. Course/team/testimonial/blog images fall back to the template's own stock photography when a model has no uploaded media (`getFirstMediaUrl(...)` returning empty). These are clearly stock/placeholder and should be swapped for real photos via the admin media uploads (Posts' featured image, Services' cover image) as they become available — nothing fabricates a specific real photo that doesn't exist.

### A real CSS bug, found and fixed (not guessed at)

The home page's "German Language Courses" cards (and the `/courses` index, same card markup) visually broke — text and the "View Course" button ran together instead of stacking. Root cause, found by reading the actual shipped CSS rather than trial-and-error: the template's `.single_course p { display: inline; }` rule was written for exactly two short inline labels ("12 Course" / "2 Hrs 32 Min") sitting side by side — the original template's course card **never contained a button at all** (the only "View Course" CTA was once per section, not per card). Adding a real summary paragraph and a real "View Course" link per card broke under both of those unstated assumptions, and `.btn_one` itself has no explicit `display`, so it fell back to an `<a>` tag's default `inline`.

Fixed by wrapping the dynamic per-card content in `.single-course-body` and adding scoped overrides in `public/web-assets/css/web-overrides.css` (`display: block` on its `<p>` children, `display: inline-block` on `.btn_one` inside it) — scoped to that one wrapper class, not a template-wide rule change, so nothing else that uses `.single_course p` or `.btn_one` elsewhere is affected. Verified by inspecting the rendered HTML's nesting directly, not just assumed from the CSS change.

## Real content, not placeholders

`database/seeders/HimalayanContentSeeder.php` — every string in it is transcribed from `docs/website-content.md` (the business's actual prepared content for "Himalayan Education Pathways"), not Lorem Ipsum. Idempotent (`firstOrNew`/`firstOrCreate` keyed on the same natural keys the content itself implies — `page_slug`+`key` for sections, `slug` for services/posts), safe to re-run. Seeds: 9 `Setting` rows (business info, SEO defaults, social links), 15 `Section`s with 64 `SectionItem`s across home/about/FAQ, 6 `Service`s (German A1/A2/B1/B2, Exam Prep, Ausbildung — full curriculum HTML for A1, lighter "Key Learning Areas" lists for A2–B2 and the other two, matching exactly what the source doc provided for each), 9 `Post`s (only the "How to Learn German A1 in Nepal" post has a full article body in the source doc — seeded as given; the other 8 currently only have their short blurb as `content`, honestly, rather than inventing full articles that weren't provided — **write real bodies for these before launch**).

**Known gaps carried over from the content doc itself, not introduced here:**
- No real `contact_email`/`contact_phone`/`admin_notification_email` — none was given anywhere in the source content (only address + social links + registration/PAN numbers). Fill these in via Settings → General before launch, or the contact-form notification (see [inquiries.md](inquiries.md)) has nothing to send to beyond the seeded admin account's own email.
- The 3 testimonials are explicitly labeled "Sample Student" in the source doc, which itself says to replace them with genuine reviews before publishing — seeded as-is, labeling intact, not disguised as real.

### Production SQL export

`database/sql/himalayan-content-seed.sql` — a `mysqldump --no-create-info --complete-insert` of the `settings`, `sections`, `section_items`, `services`, `posts` tables (103 `INSERT IGNORE INTO` statements; changed from plain `INSERT` to `INSERT IGNORE` so accidentally running it twice doesn't hard-fail on a duplicate key). **Verified, not just generated**: truncated all five tables in this dev database, re-imported the file, and confirmed the row counts and the actual rendered pages matched exactly before vs. after. Assumes the production database already has the schema (migrations run) but no conflicting rows in these specific tables — review for collisions first if production isn't a clean slate.

## Explicitly not built yet

- No locale switcher on the public site itself, despite `Section`/`Service`/`Post` content being translatable — everything renders in the app's single configured locale (`en`).
- No public gallery pages (the `Gallery` module has no public "show" route — see [seo-discovery.md](seo-discovery.md), which already documented this sitemap gap before this phase).
- Blog sidebar (categories/popular-posts/tags) from the template's `blog_single.html` was simplified to just "Popular Posts" — categories and tags aren't modeled anywhere in the `Post` schema yet.
- Course detail pages dropped the template's star ratings, fake review counts, seat counts and "related course" fabricated prices entirely — none of that is real data, and the source content doc explicitly says not to invent statistics or ratings.
