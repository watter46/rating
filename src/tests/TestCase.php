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
    
    public function setUp(): void
    {
        parent::setUp();
        
        if (!self::$migrated) {
            $this->artisan('migrate');

            self::$migrated = true;
        }

        $this->actingAs(User::factory()->create());
        
        if (!$this->seeder) return;

        $this->artisan('db:seed', ['--class' => $this->seeder]);
    }
}
