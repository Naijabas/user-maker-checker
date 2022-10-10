<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        //Load .env.testing environment
        $app->loadEnvironmentFrom('.env');

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }
}
