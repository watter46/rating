<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Models\User;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected static $migrated = false;

    protected $seeder;
    
    public function setup(): void
    {
        parent::setUp();
        
        if (!self::$migrated) {
            $this->artisan('migrate');

            self::$migrated = true;
        }

        $this->artisan('db:seed', ['--class' => $this->seeder]);
        
        $this->actingAs(User::factory()->create());
    }
}
