<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * The only database the tests may touch.
     */
    private const TEST_DATABASE = 'workshop_registration_testing';

    /**
     * Refuse to boot unless the app is pointed at the testing database, so
     * RefreshDatabase can never wipe the dev one (a cached config would make
     * Laravel ignore phpunit.xml's database override).
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $connection = $app['config']->get('database.default');
        $database = (string) $app['config']->get("database.connections.{$connection}.database");

        $problem = match (true) {
            $app->configurationIsCached() => 'the app config is cached (run php artisan config:clear)',
            ! $app->environment('testing') => "APP_ENV is '{$app->environment()}', not 'testing'",
            $database !== self::TEST_DATABASE => "the database is '{$database}', not '" . self::TEST_DATABASE . "'",
            default => null,
        };

        if ($problem !== null) {
            fwrite(STDERR, "\nTESTS STOPPED: {$problem}. Nothing was touched.\n");

            throw new RuntimeException("Refusing to run tests: {$problem}.");
        }

        return $app;
    }
}
