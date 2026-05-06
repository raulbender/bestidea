<?php

declare(strict_types=1);

namespace Framework;

use Framework\Admin\AdminRepository;
use Framework\Admin\AdminRepositoryInterface;
use Framework\Admin\AdminService;
use Framework\Admin\AdminServiceInterface;
use Framework\BaseUser\BaseSignUpService;
use Framework\BaseUser\BaseSignUpServiceInterface;
use Framework\Database\Connection;
use Framework\Database\DatabaseInterface;
use Framework\Database\PdoAdapter;
use Framework\Extensions\Mail\MailInterface;
use Framework\Extensions\Mail\ResendMailService;
use Framework\Extensions\Redis\RedisConnection;
use Framework\Extensions\Redis\RedisConnectionInterface;
use Framework\Extensions\Redis\RedisService;
use Framework\Extensions\Redis\RedisServiceInterface;
use Framework\Http\CsrfService;
use Framework\Http\CsrfServiceInterface;
//use Framework\Http\PHPSession;
use Framework\Http\MiddlewareInterface;
use Framework\Http\SessionMiddleware;
use Framework\Http\Request;
use Framework\Http\SessionInterface;
use Framework\Http\RRSession;
use Framework\Utils\Translator;

Container::bind(MiddlewareInterface::class, SessionMiddleware::class);
Container::bind(RedisConnectionInterface::class, RedisConnection::class);
Container::bind(RedisServiceInterface::class, RedisService::class);
Container::bind(BaseSignUpServiceInterface::class, BaseSignUpService::class);
Container::bind(AdminRepositoryInterface::class, AdminRepository::class);
Container::bind(AdminServiceInterface::class, AdminService::class);
Container::bind(SessionInterface::class, RRSession::class);
Container::bind(CsrfServiceInterface::class, CsrfService::class);
Container::bind(Translator::class, Translator::class);
Container::bind(MailInterface::class, ResendMailService::class);
Container::bind(DatabaseInterface::class, PdoAdapter::class);


