<?php

namespace AnandPilania\WebTinker;

use Illuminate\Console\Command as Base;

class PHPTinkerCommand extends Base
{
    use Support;

    protected $signature = 'php-tinker';

    protected $description = '...';

    public function handle()
    {
        $app = $this->laravel;

        $shell = $this->setContext($app)->getShell();

        $shell->run();
    }
}
