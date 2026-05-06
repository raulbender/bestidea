<?php

declare(strict_types=1);

namespace Framework\Http;

interface CsrfServiceInterface extends ScopedService
{
    public function __construct(SessionInterface $session);
    public function getToken(): string;
    public function isValid(string $submittedToken): bool;
    public function checkCsrf(): void;
}
