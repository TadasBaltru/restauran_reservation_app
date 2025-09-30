<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupTestDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:setup-db {--type=sqlite : Database type (sqlite, mysql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up and configure the testing database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info("Setting up {$type} testing database...");

        switch ($type) {
            case 'sqlite':
                $this->setupSqliteTest();
                break;

            case 'mysql':
                $this->setupMysqlTest();
                break;

            default:
                $this->error("Unsupported database type: {$type}");
                return 1;
        }

        $this->info('Testing database setup complete!');
        return 0;
    }

    /**
     * Set up SQLite testing database.
     */
    protected function setupSqliteTest(): void
    {
        $this->info('SQLite testing database configured.');
        $this->info('Using in-memory database (:memory:) for maximum speed.');

        $this->line('');
        $this->line('Your phpunit.xml is configured to use:');
        $this->line('  - DB_CONNECTION=sqlite');
        $this->line('  - DB_DATABASE=:memory:');
        $this->line('');
        $this->line('Benefits:');
        $this->line('  ✅ Extremely fast');
        $this->line('  ✅ No cleanup needed');
        $this->line('  ✅ Complete test isolation');
    }

    /**
     * Set up MySQL testing database.
     */
    protected function setupMysqlTest(): void
    {
        $testDbName = 'restaurant_reservation_test';

        $this->info("Creating MySQL test database: {$testDbName}");

        try {
            // Get current database config but without database name
            $config = config('database.connections.mysql');
            unset($config['database']); // Remove database to connect to MySQL server without specific DB

            config(['database.connections.mysql_setup' => $config]);

            DB::connection('mysql_setup')->statement("CREATE DATABASE IF NOT EXISTS `{$testDbName}`");

            $this->info("✅ Test database '{$testDbName}' created successfully!");

            // Now run migrations on the test database
            $this->info('Running migrations on test database...');

            // Temporarily switch to test database to run migrations
            config(['database.connections.mysql_test_temp' => array_merge($config, ['database' => $testDbName])]);

            $this->call('migrate:fresh', [
                '--database' => 'mysql_test_temp',
                '--force' => true
            ]);

            $this->info('✅ Test database setup complete with migrations!');
        } catch (\Exception $e) {
            $this->error("Failed to create test database: " . $e->getMessage());
            $this->line('');
            $this->line('Make sure:');
            $this->line('  1. MySQL is running');
            $this->line('  2. Your database credentials are correct');
            $this->line('  3. Your user has CREATE DATABASE privileges');
        }
    }
}
