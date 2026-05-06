<?php

declare(strict_types=1);

namespace Framework;

use Framework\Http\SessionInterface;
use Framework\Utils\Translator;

class BaseDTOFactory
{
    public function __construct(
        private SessionInterface $session,
    ) {
    }

    public function create(): BaseDTO
    {
        $sessionDebugModeOn = ensureBool($this->session->get('debugModeOn') ?? isDevEnvironment());
        $sessionLanguage = Container::resolve(Translator::class)->language();
        $user_id = $this->session->pullInt('id');

        if (! $user_id) {
            return new BaseDTO(
                debugModeOn: $sessionDebugModeOn,
                language: $sessionLanguage,
                errorMessage: $this->session->pullString('errorMessage')
            );
        }

        return new BaseDTO(
            id: $user_id,
            username: $this->session->pullString('username'),
            avatar: $this->session->pullString('avatar', '👤'),
            debugModeOn: $sessionDebugModeOn,
            language: $sessionLanguage,
        );
    }

}
