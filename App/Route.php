<?php

declare(strict_types=1);

namespace App;

use App\Components\Home\HomeController;
use App\Components\Feed\FeedController;
use App\Components\Room\RoomController;
use Framework\BaseRoute;

class Route extends BaseRoute {
    protected function initRoutes(): void {
        $this->routes['home'] = [
            'route' => '/',
            'controller' => HomeController::class,
            'action' => 'index',
        ];

        $this->routes['home_about'] = [
            'route' => '/{lang}/about',
            'controller' => HomeController::class,
            'action' => 'about',
        ];

        $this->routes['feed_api'] = [
            'route' => '/{lang}/api/ideas',
            'controller' => FeedController::class,
            'action' => 'getIdeasApi',
        ];


        $this->routes['feed_view'] = [
            'route' => '/{lang}/feed',
            'controller' => FeedController::class,
            'action' => 'index',
        ];

        $this->routes['room_create'] = [
            'route' => '/{lang}/room',
            'controller' => RoomController::class,
            'action' => 'index',
        ];

        $this->routes['room_store'] = [
            'route' => '/{lang}/room/{id}',
            'controller' => RoomController::class,
            'action' => 'room',
        ];
    }
}
