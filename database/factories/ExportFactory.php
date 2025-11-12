<?php

namespace Database\Factories;

use App\Enums\ExportStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Export>
 */
class ExportFactory extends Factory
{

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => ExportStatus::PENDING,
            'file_path' => null,
            'meta' => [
                'status' => 'approved',
                'period' => now()->format('Y-m'),
            ],
        ];
    }
}
