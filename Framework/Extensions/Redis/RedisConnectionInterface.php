<?php

declare(strict_types=1);

namespace Framework\Extensions\Redis;

use Redis;

interface RedisConnectionInterface
{
    public function getClient(): Redis;
}
