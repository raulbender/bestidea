<?php

declare(strict_types=1);

namespace Framework\Utils;

use Framework\Http\Request;

class Translator
{
    /** @var array<string, array<string, string>> $messages */
    private array $messages = [];
    private string $locale;

    public function __construct(Request $request)
    {
        $this->locale = $this->detectBrowserLanguage($request) ?: 'en';

        $path = __DIR__ . "/../../App/I18n/{$this->locale}.php";
        $this->messages = file_exists($path) ? include $path : [];
    }

    public function language(): string
    {
        return $this->locale;
    }

    private function detectBrowserLanguage(Request $request): ?string
    {
        $acceptLanguage = $request->getHeader('Accept-Language');

        if (! $acceptLanguage) {
            return null;
        }

        $lang = strtolower(substr($acceptLanguage, 0, 2));
        $availableLocales = ['en', 'pt'];

        return in_array($lang, $availableLocales) ? $lang : null;
    }

    public function get(string $key): string
    {
        $keys = explode('.', $key);
        $result = $this->messages;

        foreach ($keys as $k) {
            if (! isset($result[$k]) || ! is_array($result)) {
                return $key;
            }
            $result = $result[$k];
        }

        return is_string($result) ? $result : $key;
    }
}
