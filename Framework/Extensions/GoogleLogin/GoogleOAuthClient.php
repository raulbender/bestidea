<?php

declare(strict_types=1);

namespace Framework\Extensions\GoogleLogin;

use Framework\BaseUser\BaseUser;
use Framework\Container;
use Framework\Utils\Logger\Logger;
use Google\Client;
use Google\Service\Oauth2;

class GoogleOAuthClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(Container::$config->googleClientId);
        $this->client->setClientSecret(Container::$config->googleClientSecret);
        $this->client->setRedirectUri(Container::$config->googleRedirectUri);
        $this->client->addScope("email");
        $this->client->addScope("profile");
    }


    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }


    public function fetchGoogleUser(string $code): ?BaseUser
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                throw new \Exception("Error in Token: " . $token['error']);
            }

            $this->client->setAccessToken($token);
            $oauth2 = new Oauth2($this->client);
            $userInfo = $oauth2->userinfo->get();

            return new class (
                username: $userInfo->name,
                email: $userInfo->email,
                google_id: $userInfo->id,
                avatar_url: $userInfo->picture
            ) extends BaseUser {
            };
        } catch (\Exception $e) {
            Logger::error("Communication failure with Google: " . $e->getMessage());

            return null;
        }
    }
}
