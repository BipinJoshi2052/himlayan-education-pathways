<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SectionItem>
 */
class SectionItemFactory extends Factory
{
    protected $model = SectionItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(8),
            'icon_or_badge' => null,
            'link_url' => null,
            'sort_order' => 0,
        ];
    }
}
