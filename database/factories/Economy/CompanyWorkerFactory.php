<?php

namespace Database\Factories\Economy;

use App\Models\Economy\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Economy\CompanyWorker>
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
