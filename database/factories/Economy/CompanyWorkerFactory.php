<?php

namespace Database\Factories\Economy;

use App\Models\Economy\Company;
use App\Models\Economy\CompanyWorker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyWorker>
 */
class CompanyWorkerFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (CompanyWorker $worker) {
            if (! $worker->company_site_id) {
                $worker->update(['company_site_id' => $worker->company?->headquarters_site_id]);
            }
        });
    }

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
