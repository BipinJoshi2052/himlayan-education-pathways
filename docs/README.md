# Documentation

Living documentation for the `laravel-boilerplate` project, maintained module-by-module as features are built. Update the relevant file whenever a module changes — this is meant to stay current, not be a one-time write-up.

## Index

- [architecture.md](architecture.md) — core conventions: primary keys, Action/DTO pattern, strict models
- [database.md](database.md) — connection, driver choices, local setup
- [packages.md](packages.md) — installed packages, what each is used for, and custom packages
- [auth.md](auth.md) — authentication module (in progress)
- [api-responses.md](api-responses.md) — standardized JSON envelope and centralized exception handling
- [admin-ui.md](admin-ui.md) — Hope UI admin theme integration, sidebar/navbar, permission-filtered nav, theme toggle
- [cms-sections.md](cms-sections.md) — schema-agnostic content sections/items CMS, admin CRUD, repeater UI
- [settings.md](settings.md) — key-value Settings, SEO fallback resolution, SMTP config, a real caching bug and fix
- [inquiries.md](inquiries.md) — public contact form, queued admin notification, inbox UI
- [seo-discovery.md](seo-discovery.md) — robots.txt, sitemap.xml, llms.txt/llms-full.txt
- [performance.md](performance.md) — PageSpeed/Lighthouse changes made, server steps to run, and open items
- [facebook-feed.md](facebook-feed.md) — Facebook Page posts on the About page: settings, getting the Page ID and token, troubleshooting
- [security.md](security.md) — security posture and hardening notes (planned)

## Conventions for this folder

- One file per module/concern. Create a new file rather than growing an existing one into unrelated territory.
- Date-stamp significant decisions inline (e.g. "as of 2026-10-02") so later readers know what was true when written.
- When a module is replaced or removed, update or delete its doc in the same change — don't leave stale docs behind.
