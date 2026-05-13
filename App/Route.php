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
            'route' => '/{lang}/api/idea/{uuid}', 
            'controller' => FeedController::class,
            'action' => 'getIdeasApi',
        ];

        $this->routes['contribute_api'] = [
            'route' => '/{lang}/api/contribute/{uuid}', 
            'controller' => FeedController::class,
            'action' => 'contributeApi',
        ];
        
        $this->routes['room_index'] = [
            'route' => '/{lang}/room',
            'controller' => RoomController::class,
            'action' => 'index',
        ];

        $this->routes['room_create'] = [
            'route' => '/{lang}/room/create',
            'controller' => RoomController::class,
            'action' => 'create',
        ];

        $this->routes['room_view'] = [
            'route' => '/{lang}/room/{uuid}',
            'controller' => RoomController::class,
            'action' => 'view',
        ];
    }
}
