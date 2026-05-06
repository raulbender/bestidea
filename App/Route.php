<?php

declare(strict_types=1);

namespace App;

use App\Components\Home\HomeController;
use App\Components\Feed\FeedController;
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

        $this->routes['feed'] = [
            'route' => '/feed',
            'controller' => FeedController::class,
            'action' => 'index',
        ];

        $this->routes['feed_api'] = [
            'route' => '/api/ideas',
            'controller' => FeedController::class,
            'action' => 'getIdeasApi',
        ];



    }
}
