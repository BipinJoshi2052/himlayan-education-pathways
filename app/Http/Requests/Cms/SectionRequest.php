<?php

declare(strict_types=1);

namespace App\Http\Requests\Cms;

use App\Common\Services\SectionSettings;
use App\Enums\SectionLayoutType;
use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The admin picks a page and a component from dropdowns; the layout is
     * never chosen by hand — it comes from config/sections.php.
     */
    protected function prepareForValidation(): void
    {
        $component = config('sections.pages.'.$this->input('page_slug').'.components.'.$this->input('key'));

        $this->merge([
            'layout_type' => $component['layout'] ?? null,
            'settings' => $this->cleanSettings($component['settings'] ?? [], (array) $this->input('settings', [])),
        ]);
    }

    /**
     * Keeps only the fields the chosen component defines, with valid choices.
     * Anything else posted under settings is dropped.
     *
     * @param  array<string, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    private function cleanSettings(array $fields, array $input): array
    {
        $clean = [];

        foreach ($fields as $name => $def) {
            $value = trim((string) ($input[$name] ?? ''));

            if ($def['type'] === 'link') {
                $choice = array_key_exists($value, SectionSettings::LINKS) ? $value : $def['default'];
                $clean[$name] = $choice;

                if ($choice === 'custom') {
                    $clean[$name.'_url'] = trim((string) ($input[$name.'_url'] ?? ''));
                }
            } elseif ($def['type'] === 'color') {
                $clean[$name] = array_key_exists($value, SectionSettings::BACKGROUNDS) ? $value : $def['default'];
            } else {
                $clean[$name] = Str::limit($value, 255, '');
            }
        }

        return $clean;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Section|null $section */
        $section = $this->route('section');

        return [
            'page_slug' => ['required', 'string', Rule::in(array_keys(config('sections.pages')))],
            'key' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($section) {
                    $allowed = array_keys(config('sections.pages.'.$this->input('page_slug').'.components', []));

                    if (! in_array($value, $allowed, true)) {
                        $fail('Please choose a section from the list.');

                        return;
                    }

                    // Trashed rows still hold the (page_slug, key) unique index in
                    // the database, so they count as taken too.
                    $matches = Section::withTrashed()
                        ->where('page_slug', $this->input('page_slug'))
                        ->where('key', $value)
                        ->when($section, fn ($q) => $q->whereKeyNot($section->id))
                        ->get(['id', 'deleted_at']);

                    $label = config('sections.pages.'.$this->input('page_slug').'.components.'.$value.'.label');

                    if ($matches->whereNull('deleted_at')->isNotEmpty()) {
                        $fail("A \"{$label}\" section already exists on this page.");
                    } elseif ($matches->isNotEmpty()) {
                        $fail("A \"{$label}\" section exists in the trash. Restore it from the trash instead of creating a new one.");
                    }
                },
            ],
            'layout_type' => ['required', new Enum(SectionLayoutType::class)],
            'title.en' => ['required', 'string', 'max:255'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'subtitle.*' => ['nullable', 'string', 'max:255'],
            'content.*' => ['nullable', 'string'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'max:5120'],
            'items' => ['array'],
            'items.*.id' => ['nullable', 'uuid'],
            'items.*.title.en' => ['required_with:items.*', 'string', 'max:255'],
            'items.*.title.*' => ['nullable', 'string', 'max:255'],
            'items.*.description.*' => ['nullable', 'string'],
            'items.*.icon_or_badge' => ['nullable', 'string', 'max:255'],
            'items.*.link_url' => ['nullable', 'string', 'max:2048'],
            'items.*.sort_order' => ['nullable', 'integer'],
            'items.*.image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
