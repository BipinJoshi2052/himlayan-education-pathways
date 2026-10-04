<?php

namespace Database\Factories;

use App\Enums\SectionLayoutType;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_slug' => $this->faker->unique()->slug(1),
            'key' => $this->faker->unique()->slug(2),
            'layout_type' => $this->faker->randomElement(SectionLayoutType::cases())->value,
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->optional()->sentence(6),
            'content' => $this->faker->optional()->paragraph(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
