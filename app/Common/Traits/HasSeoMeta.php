<?php

declare(strict_types=1);

namespace App\Common\Traits;

use Illuminate\Support\HtmlString;

/**
 * Shared SEO surface for any model that has meta_title/meta_description/
 * meta_keywords columns. Those columns are expected to already be declared
 * translatable via HasTranslations on the consuming model (Post/Service/
 * Gallery each list them in their own $translatable array) — this trait
 * does not duplicate that, it only adds the rendering helper and a single
 * source of truth for "which columns count as SEO fields" that the
 * seo-inputs Blade component and form requests can share.
 */
trait HasSeoMeta
{
    /**
     * @return array<int, string>
     */
    public static function seoAttributes(): array
    {
        return ['meta_title', 'meta_description', 'meta_keywords'];
    }

    public function renderSeoTags(): HtmlString
    {
        $title = $this->seoAttr('meta_title') ?? $this->seoAttr('title');
        $description = $this->seoAttr('meta_description') ?? $this->seoAttr('summary') ?? $this->seoAttr('description');
        $keywords = $this->seoAttr('meta_keywords');

        $tags = [];

        if ($title !== null) {
            $tags[] = '<title>'.e($title).'</title>';
            $tags[] = '<meta property="og:title" content="'.e($title).'">';
        }

        if ($description !== null) {
            $tags[] = '<meta name="description" content="'.e($description).'">';
            $tags[] = '<meta property="og:description" content="'.e($description).'">';
        }

        if ($keywords !== null) {
            $tags[] = '<meta name="keywords" content="'.e($keywords).'">';
        }

        $tags[] = '<meta property="og:type" content="website">';
        $tags[] = '<meta property="og:url" content="'.e(request()->url()).'">';

        return new HtmlString(implode("\n", $tags));
    }

    /**
     * Model::shouldBeStrict() throws on accessing an attribute that doesn't
     * exist on this model (e.g. Gallery has no "summary") — a plain
     * `$this->summary ?? null` would not catch that, since it's a thrown
     * exception, not a PHP null/undefined-index. Check the raw attribute
     * bag first instead.
     */
    protected function seoAttr(string $key): ?string
    {
        if (! array_key_exists($key, $this->getAttributes())) {
            return null;
        }

        $value = $this->getAttribute($key);

        return filled($value) ? $value : null;
    }
}
