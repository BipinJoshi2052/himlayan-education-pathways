# Performance (Lighthouse / PageSpeed)

Record of the changes made from the PageSpeed Insights reports for the public website, what each change does, and what still needs to be done. Written as of 2026-10-06.

## Changes already made (in the code)

| Report item | Change | Commit |
|---|---|---|
| Improve image delivery; enormous network payloads | Each uploaded image gets a `web` copy: WebP, 1200px maximum, quality 80. Public pages request that copy and fall back to the original if it doesn't exist. Applies to section and team images, course covers, gallery covers and notice images. | `2d936ca` |
| Use efficient cache lifetimes | `public/.htaccess` sets a 30-day browser cache for images, fonts, CSS and JavaScript. The stylesheet is versioned (`?v=` file time), so CSS changes still reach visitors straight away. | `db74824` |
| Render-blocking requests | Icon, carousel, popup and animation CSS load after the first paint (`media="print"` with `onload`, plus a `<noscript>` fallback). Early connections (`preconnect`) to the font and CDN hosts. | `db74824` |
| Font display | Themify icon font uses `font-display: swap`. | `db74824` |
| LCP request discovery; LCP not lazy-loaded | The hero's first slide is a real `<img>` with `fetchpriority="high"`, not a CSS background. Other slides are lazy-loaded. | `9f1c17d` |
| Critical request chains | Plugin scripts are `defer`, so they don't hold up parsing. jQuery stays synchronous, because the plugins depend on it. The two Google Fonts families are requested in one stylesheet. | `9f1c17d` |
| Minify / enormous payloads (text) | `.htaccess` compresses CSS, JavaScript, HTML and JSON on the way out. Our CSS source stays readable, since it's edited by hand. | `9f1c17d` |
| Mixed content (`http://` image) | Production always forces `https` for generated links. | `c0fc9b1` |
| Sitemap coverage (SEO, related) | About, Courses, Blog, FAQ, Gallery, Contact, Privacy and Terms are in the sitemap. | `ec00111` |

## Actions you need to take on the server

1. Pull the latest code.
2. Set the production `.env` value: `APP_URL=https://himalayaneducationpathways.edu.np`.
3. Clear caches: `php artisan optimize:clear`.
4. Create the web-sized image copies for existing uploads: `php artisan media-library:regenerate --only-missing`. This can take a while. Until it runs, public pages fall back to the full originals, which is why large files still appear in reports.
5. Confirm the server applies the new rules. Run `curl -sI https://himalayaneducationpathways.edu.np/web-assets/css/style.css` and look for `Cache-Control: public, max-age=2592000` and `Content-Encoding: gzip`. If they're missing, the host has `mod_headers`, `mod_expires` or `mod_deflate` turned off; ask cPanel support to enable them.
6. Test in an incognito window with extensions off, using PageSpeed Insights. Extensions produce most of the unattributed JavaScript and console warnings.

## Items from the reports that are not ours

- **Browser extension scripts** (`chrome-extension://...`), in the JavaScript minification, unused JavaScript and console messages. These come from the browser, not the site. Test in incognito to exclude them.
- **Permissions-Policy and "Failed to apply filter" console messages.** From extensions and Facebook's own scripts.
- **`sdk.js` load-time warning.** From Facebook's SDK.

## Still open

| Item | Why it's open | Possible next step |
|---|---|---|
| Unused CSS (about 45 KB) | Bootstrap and Font Awesome are full builds, and most of their rules go unused. | Build trimmed copies of both with only the classes we use. Needs a build tool. |
| Font Awesome brands font (about 116 KB) | Loaded from the CDN in full. | Host a subset of just the icons we use. |
| Legacy JavaScript (about 8 KB) | Owl Carousel 1.x is old and transpiled. | Replace the carousel, or accept the small cost. |
| Minify CSS (about 6.5 KB) | Our own CSS source is unminified; gzip already covers most of the gain. | Only if still flagged after gzip is confirmed. |
| Layout shift (CLS): images without dimensions | Images without a width and height cause content to jump as they load. Dimensions of the fixed template images are known (for example `quote.png` 512×390, `home-img2.png` 600×700, `about1.png` 756×749, `about3.png` 493×635, `review.png` 695×689, `course/1.png` 550×386). | Add `width` and `height` to those tags. For uploads, store the dimensions when each file is saved. |
| Forced reflow (about 50 ms) | Comes from the template's jQuery, Modernizr and WOW.js scripts. | Low priority. Rewriting template scripts is risky for a small gain. |
| Large upload originals | Originals are still referenced in places that need the full file, such as the gallery lightbox on click. | Intended for now. Add a smaller grid copy later if needed. |

## Where things live

- Image conversions: `registerMediaConversions()` in `app/Models/Section.php`, `app/Models/SectionItem.php`, `app/Models/Notice.php`, `app/Modules/Service/Models/Service.php`, `app/Modules/Gallery/Models/Gallery.php`.
- Caching and compression: `public/.htaccess`.
- Layout, preconnect, deferred CSS and scripts: `resources/views/layouts/web.blade.php`.
- Hero image and LCP: `resources/views/web/home.blade.php`, `.hero-full-bg` in `public/web-assets/css/web-overrides.css`.
- Facebook and TikTok embeds (already lazy, on demand): `docs/facebook-feed.md` and the TikTok component.
