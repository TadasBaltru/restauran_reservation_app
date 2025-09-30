<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Additional test database setup can go here if needed
    }
    
    /**
     * Get the testing database configuration.
     */
    protected function getTestingDatabaseConfig(): array
    {
        return config('database_testing.strategies.sqlite_memory');
    }
    
    /**
     * Switch to a specific testing database during test.
     * 
     * @param string $strategy 'sqlite_memory', 'sqlite_file', or 'mysql_test'
     */
    protected function switchTestDatabase(string $strategy = 'sqlite_memory'): void
    {
        $config = config("database_testing.strategies.{$strategy}");
        
        if (!$config) {
            throw new \InvalidArgumentException("Unknown testing database strategy: {$strategy}");
        }
        
        config(['database.connections.testing' => $config]);
        config(['database.default' => 'testing']);
        
        // Refresh the database connection
        app('db')->purge('testing');
    }
}