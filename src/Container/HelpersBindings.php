<?php

namespace App\Container;

use App\Helpers\ViewRenderer;

trait HelpersBindings
{
    private function registerHelpers(): void
    {
        $this->set(ViewRenderer::class, fn() => new ViewRenderer(dirname(__DIR__)));
    }
}
