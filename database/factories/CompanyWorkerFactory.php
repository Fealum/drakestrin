<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyWorker>
 */
class CompanyWorkerFactory extends Factory
{
    public function definition(): array
    {
        $timestamp = now();

        return [
            'name' => fake()->name(),
            'type' => 3,
            'company_id' => Company::factory(),
            'hired' => $timestamp,
            'paid' => $timestamp,
        ];
    }
}
