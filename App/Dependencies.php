<?php

declare(strict_types=1);

namespace App;

use Framework\Container;
use App\Components\Feed\FeedRepositoryInterface;
use App\Components\Feed\FeedRepository;
use App\Components\Feed\FeedServiceInterface;
use App\Components\Feed\FeedService;
use App\Components\Room\RoomRepositoryInterface;
use App\Components\Room\RoomRepository;
use App\Components\Room\RoomServiceInterface;
use App\Components\Room\RoomService;


Container::bind(FeedRepositoryInterface::class, FeedRepository::class);
Container::bind(FeedServiceInterface::class, FeedService::class);
Container::bind(RoomRepositoryInterface::class, RoomRepository::class);
Container::bind(RoomServiceInterface::class, RoomService::class);
