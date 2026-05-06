<?php

declare(strict_types=1);

namespace Framework\Utils\Error;

interface ErrorResponseInterface
{
    public function getTitle(int $code): string;
    public function getMessage(int $code): string;
    public function getImagePath(): string;
    public function getErrorFilePath(): string;
}
