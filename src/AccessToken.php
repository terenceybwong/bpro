<?php

declare(strict_types=1);

namespace AskNicely\BPro;

require_once __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Yaml\Yaml;

class AccessToken
{
    private const CONFIG_FILE = __DIR__ . '/../data/auth.yaml';

    /** @var string $url */
    private $url;

    /** @var array $secrets */
    private $secrets;

    public function __construct()
    {
        $conf = Yaml::parse(file_get_contents(self::CONFIG_FILE), Yaml::PARSE_OBJECT_FOR_MAP);
        $conf = $conf->auth;
        $this->url = $conf->url;
        $this->secrets = $conf->secrets;
    }

    public function getAccessToken(?string $location = null): AccessTokenInterface
    {
        $location = $location ?: '1';
        $oauth = new GenericProvider(
            [
                'clientId' => $this->secrets->{$location}->clientId,
                'clientSecret' => $this->secrets->{$location}->clientSecret,
                'urlAccessToken' => $this->url,
                'urlAuthorize' => null,
                'urlResourceOwnerDetails' => null,
            ]
        );

        try {
            $accessToken = $oauth->getAccessToken(new ClientCredentials());
        } catch (IdentityProviderException $exception) {
            print $exception->getMessage();
            exit(1);
        }

        return $accessToken;
    }
}
