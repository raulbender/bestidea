<?php

declare(strict_types=1);

namespace App;

use Framework\Container;
use App\Components\Feed\FeedRepositoryInterface;
use App\Components\Feed\FeedRepository;
use App\Components\Feed\FeedServiceInterface;
use App\Components\Feed\FeedService;

Container::bind(FeedRepositoryInterface::class, FeedRepository::class);
Container::bind(FeedServiceInterface::class, FeedService::class);
