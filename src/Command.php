<?php

namespace NIIT\PHPTinker;

use Illuminate\Console\Command as Base;

class Command extends Base
{
    protected $signature = 'php-tinker';

    protected $description = '...';

    public function handle()
    {
        $app = $this->laravel;
        $shell = new \Psy\Shell();

        // Set up Laravel context
        $app->singleton('app', function () use ($app) {
            return $app;
        });

        // Initialize PsySH with Laravel context
        $shell->setScopeVariables([
            'app' => $app,
        ]);

        $shell->run();
    }
}
