<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static $setUpHasRunOnce = false;
    protected function setUp(): void {

        parent::setUp();

        if (!self::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh --seed --env=testing');
            self::$setUpHasRunOnce = true;
        }
    }
}
