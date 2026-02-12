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
     * Truncate tables and reset auto-increment.
     *
     * @param string $table The table name to truncate
     * @return void
     */
    protected function truncateTable(string $table): void
    {
        \DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
    }

    /**
     * Check if seeder should run (useful for production safety).
     *
     * @param string $environment The environment to check against
     * @return bool True if the current environment matches the specified environment
     */
    protected function shouldSeed(string $environment = 'local'): bool
    {
        return app()->environment($environment);
    }

    /**
     * Display progress message.
     *
     * @param string $message The message to display
     * @return void
     */
    protected function info(string $message): void
    {
        if (method_exists($this, 'command')) {
            $this->command->info($message);
        }
    }

    /**
     * Generate random date within range.
     *
     * @param string $startDate The start date string
     * @param string $endDate The end date string
     * @return \DateTime A random date between the specified dates
     */
    protected function randomDateBetween(string $startDate, string $endDate): \DateTime
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $randomTimestamp = mt_rand($start, $end);

        return new \DateTime('@' . $randomTimestamp);
    }
}
