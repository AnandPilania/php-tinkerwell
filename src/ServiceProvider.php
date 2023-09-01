<?php
/*
 * @author : Anand Pilania
 * @mailto : Anand.Pilania@niit.com
 * @updated : 9/1/23, 9:04 AM
 */

namespace NIIT\PHPTinker;

use Illuminate\Support\ServiceProvider as Base;

class ServiceProvider extends Base
{
    public function boot(): void
    {
        $this->app['router']->match(['GET', 'POST'], 'php-tinker', Executor::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'php-tinker');

        $this->commands([
            Command::class,
        ]);
    }
}
