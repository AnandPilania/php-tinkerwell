<?php

namespace AnandPilania\WebTinker;

use Illuminate\Contracts\Foundation\Application;
use Psy\Shell;

trait Support
{
    protected Shell $shell;

    public function getShell(): Shell
    {
        return $this->shell ?? ($this->shell = new Shell());
    }

    public function setContext(Application $app): self
    {
        $shell = $this->getShell();

        $app->singleton('app', function () use ($app) {
            return $app;
        });

        $shell->setScopeVariables([
            'app' => $app,
        ]);

        return $this;
    }
}
