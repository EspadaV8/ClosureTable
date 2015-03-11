<?php

namespace EspadaVTest\ClosureTable;

use DB;
use EspadaV8\ClosureTable\Models\Entity;
use Event;
use Mockery;
use Orchestra\Testbench\TestCase;
use Way\Tests\ModelHelpers;

/**
 * Class BaseTestCase
 * @package EspadaVTest\ClosureTable
 */
abstract class BaseTestCase extends TestCase
{
    use ModelHelpers;

    public static $debug = false;
    public static $sqliteInMemory = true;

    public function setUp()
    {
        parent::setUp();

        $this->app->bind(
            'EspadaV8\ClosureTable\Contracts\EntityInterface',
            'EspadaV8\ClosureTable\Models\Entity'
        );
        $this->app->bind(
            'EspadaV8\ClosureTable\Contracts\ClosureTableInterface',
            'EspadaV8\ClosureTable\Models\ClosureTable'
        );

        if (!static::$sqliteInMemory) {
            DB::statement('DROP TABLE IF EXISTS entities_closure');
            DB::statement('DROP TABLE IF EXISTS entities;');
            DB::statement('DROP TABLE IF EXISTS migrations');
        }

        $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        $artisan->call('migrate', [
            '--database' => 'closuretable',
            '--path' => '../tests/migrations'
        ]);

        $artisan->call('db:seed', [
            '--class' => 'EspadaVTest\ClosureTable\Seeds\EntitiesSeeder'
        ]);

        if (static::$debug) {
            Entity::$debug = true;
            Event::listen('illuminate.query', function ($sql, $bindings, $time) {
                $sql = str_replace(array('%', '?'), array('%%', '%s'), $sql);
                $fullSql = vsprintf($sql, $bindings);
                echo PHP_EOL . '- BEGIN QUERY -' . PHP_EOL . $fullSql . PHP_EOL . '- END QUERY -' . PHP_EOL;
            });
        }
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'closuretable');

        $options = [
            'driver' => 'mysql',
            'host' => 'localhost:33060',
            'database' => 'closuretabletest',
            'username' => 'homestead',
            'password' => 'secret',
            'prefix' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];

        if (static::$sqliteInMemory) {
            $options = [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ];
        }

        $app['config']->set('database.connections.closuretable', $options);
    }

    /**
     * Asserts if two arrays have similar values, sorting them before the fact in order to "ignore" ordering.
     * @param array $actual
     * @param array $expected
     * @param string $message
     * @param float $delta
     * @param int $depth
     */
    protected function assertArrayValuesEquals(array $actual, array $expected, $message = '', $delta = 0.0, $depth = 10)
    {
        $this->assertEquals($actual, $expected, $message, $delta, $depth, true);
    }
}
