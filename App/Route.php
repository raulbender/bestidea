<?php

declare(strict_types=1);

namespace App;

use App\Components\Home\HomeController;
use Framework\BaseRoute;

class Route extends BaseRoute
{
    protected function initRoutes(): void
    {
        $this->routes['home'] = [
            'route' => '/',
            'controller' => HomeController::class,
            'action' => 'index',
        ];



    }
}
