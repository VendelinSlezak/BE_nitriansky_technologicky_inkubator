<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'collection' => $this->faker->randomElement(['attachment', 'profile_photo']),
            'visibility' => $this->faker->randomElement(['public', 'private']),
            'disk' => 'local',
            'path' => 'uploads/' . $this->faker->unique()->sha256 . '.pdf',
            'original_name' => $this->faker->word . '.pdf',
            'stored_name' => Str::random(40) . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1024, 5242880), // 1KB až 5MB
        ];
    }
}