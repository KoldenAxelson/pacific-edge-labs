<?php

namespace App\Traits;

/**
 * Provides reusable helper methods for database seeders.
 *
 * Includes utilities for table truncation, environment checks, progress output,
 * and random date generation to support safe and flexible data seeding.
 */
trait SeederHelpers
{
    /**
     * Truncate tables and reset auto-increment
     */
    protected function truncateTable(string $table): void
    {
        \DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
    }

    /**
     * Check if seeder should run (useful for production safety)
     */
    protected function shouldSeed(string $environment = 'local'): bool
    {
        return app()->environment($environment);
    }

    /**
     * Display progress message
     */
    protected function info(string $message): void
    {
        if (method_exists($this, 'command')) {
            $this->command->info($message);
        }
    }

    /**
     * Generate random date within range
     */
    protected function randomDateBetween(string $startDate, string $endDate): \DateTime
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $randomTimestamp = mt_rand($start, $end);

        return new \DateTime('@' . $randomTimestamp);
    }
}
