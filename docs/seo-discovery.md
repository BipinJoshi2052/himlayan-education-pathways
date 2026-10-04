# SEO & Discovery Endpoints (robots.txt, sitemap.xml, llms.txt)

## Current state (as of 2026-10-02)

Four public, unauthenticated text/XML endpoints for crawlers — standard search engine ones and the emerging `/llms.txt` convention for LLM-based crawlers (ChatGPT, Claude, Perplexity).

| Route | Controller | Cache key | TTL |
|---|---|---|---|
| `GET /robots.txt` | `App\Http\Controllers\Web\Seo\RobotsController@index` | — (not cached, trivial to compute) | — |
| `GET /sitemap.xml` | `...\SitemapController@index` | `seo_sitemap_xml` | 6h |
| `GET /llms.txt` | `...\LlmsTxtController@summary` | `llms_txt_cache` | 24h |
| `GET /llms-full.txt` | `...\LlmsTxtController@full` | `llms_txt_full_cache` | 24h |

### A real bug, caught and fixed: Laravel's default skeleton already ships a static `public/robots.txt`

First test of `/robots.txt` returned `Disallow:` with nothing after it — wrong in both environments. The dynamic `RobotsController` logic was correct (verified directly via Tinker, independent of HTTP); the actual bug was that **`public/robots.txt` already existed** as a static file from Laravel's own project skeleton (`User-agent: *\nDisallow:` — allow everything, the stock default), and per the same file-shadowing behavior already documented in [admin-ui.md](admin-ui.md) for `/admin`, both the PHP dev server and a standard Apache/Nginx setup serve an existing static file before ever reaching Laravel's router. Deleted the file; the dynamic route now actually gets hit. **If this project ever adds a build step or deployment process that regenerates `public/`, make sure nothing re-creates a static `robots.txt` there.**

### Environment-based robots.txt behavior

- **Non-production**: `Disallow: /` — blocks everything, to stop accidental staging/local indexing. Verified via a direct HTTP request (this environment is `local`).
- **Production**: allows general crawling, disallows `/admin`, `/api`, `/storage/private`, declares `Sitemap: {url}/sitemap.xml`. Not independently HTTP-tested (this environment can't actually run as `production`), but the branch is simple, static string construction with no external dependency — verified by reading, not just assumed.

### Sitemap: two deliberate deviations from the literal spec, both documented inline in the code

1. **Cache backend**: the spec said "Laravel's file cache," but this project has a standing, documented architecture decision ([database.md](database.md), Phase 1) to use the `database` cache driver exclusively, for low-resource hosting. Introducing a second cache backend just for this one endpoint would contradict that decision for no real benefit, so this uses the default (database) store instead.
2. **Entity URLs point to pages that don't exist yet**: `/posts/{slug}`, `/services/{slug}`, `/galleries/{slug}` are sensible, predictable URL conventions, but **no public-facing "show" page has been built for any of these modules** — only admin CRUD exists so far (see [cms-sections.md](cms-sections.md), [settings.md](settings.md), etc. — every module's docs says the same thing: admin-side only). The sitemap's URL-collection logic is complete and correct; right now every entity URL it lists would 404 if visited. This isn't a bug to fix later so much as a fact to know before pointing a real search engine at this sitemap.

Also note: hreflang `<xhtml:link>` alternates currently point every locale at the *same* URL, because there's no locale-prefixed routing (no `/en/...`, `/ne/...` variants) anywhere in the app yet. The structure is ready for when that exists.

**Cache invalidation — verified working, not just wired**: `Post`, `Service`, and `Gallery` each declare `protected $dispatchesEvents = ['saved' => XSaved::class, 'deleted' => XSaved::class]`, and `App\Listeners\FlushSeoCache` (auto-discovered via a union-typed `handle(PostSaved|ServiceSaved|GallerySaved $event)` — this Laravel version has no `EventServiceProvider`, events are discovered from listener parameter types) forgets all three cache keys. Verified directly: primed the `seo_sitemap_xml` cache key, created a `Post`, confirmed the key was gone afterward — not just that the wiring compiles.

### llms.txt / llms-full.txt

Pulls `site_name`/`site_tagline`/`contact_email`/`contact_phone` from [Settings](settings.md) and lists active `Service` records (summary-only in `/llms.txt`, full description + duration/fee in `/llms-full.txt`). Falls back to a "(not configured...)" / "(none published yet)" line rather than silently omitting sections, so the output stays well-formed with nothing configured — verified by hitting it against an empty database.

### Admin: "Flush SEO Cache" button

On Settings → SEO & Social (not a separate page). Posts to `admin.settings.flush-seo-cache`, clears all three cache keys. The settings `update()` action also calls this automatically on every save (not just for the SEO group) — any settings change might affect what the sitemap/llms output would look like, so it errs toward flushing rather than trying to track exactly which keys matter. Verified: primed the cache keys directly, hit the button via HTTP, confirmed both `seo_sitemap_xml` and `llms_txt_cache` were cleared.

## Explicitly not built yet

- No public "show" pages for Posts/Services/Galleries (see above) — the sitemap links to them anyway as a forward-looking convention.
- No locale-prefixed routing, so hreflang alternates are not yet real distinct URLs.
- "About Us" and "Contact" static pages mentioned in the original spec aren't in the sitemap — no such routes/pages exist in this project to link to, and listing URLs that don't resolve to anything felt worse than just omitting them.
