<?php

namespace AnandPilania\WebTinker;

use Illuminate\Support\ServiceProvider as Base;

class ServiceProvider extends Base
{
    public function boot(): void
    {
        $this->app['router']->match(['GET', 'POST'], 'web-tinker', Executor::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'web-tinker');

        $this->commands([
            PHPTinkerCommand::class,
        ]);
    }
}
