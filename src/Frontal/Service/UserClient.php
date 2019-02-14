<?php

namespace App\Frontal\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class UserClient
 * @package App\Frontal\Service
 */
class UserClient
{
    /** @var Client */
    private $client;

    /** @var ParameterBagInterface  */
    private $params;

    /**
     * UserClient constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(
        ParameterBagInterface $params
    ) {
        $this->client = new Client();
        $this->params = $params;
    }

    /**
     * @param string $user
     * @return array|null
     */
    public function loadUserByUsername(string $user)
    {
        $url = $this->buildApiUrl(['api/v1/user', $user]);

        try {
            $result = (string) $this->client->get($url)->getBody();
        } catch (ClientException $exception) {
            return null;
        }

        return \json_decode($result, true);
    }

    /**
     * @param array $credentials
     * @return array|null
     */
    public function authenticateUser(array $credentials)
    {
        $url = $this->buildApiUrl(['api/v1/auth']);

        try {
            $authResult = (string) $this->client->post($url, [
                "form_params" => $credentials
            ])->getBody();
        } catch (ClientException $exception) {
            return null;
        }


        return \json_decode($authResult, true);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $role
     * @return array|null
     */
    public function addUser(string $username, string $password, string $role)
    {
        if (!$username || !$password || !$role) {
            return null;
        }

        $url = $this->buildApiUrl(['api/v1/user']);

        try {
            $result = (string) $this->client->post($url, [
                "form_params" => [
                    "username" => $username,
                    "password" => $password,
                    "role" => $role
                ]
            ])->getBody();
        } catch (ClientException $exception) {
            return null;
        }

        return \json_decode($result, true);
    }

    /**
     * @param array $paths
     * @param array $query
     * @return string
     */
    private function buildApiUrl(array $paths, array $query = []): string
    {
        $endpoint = $this->params->get('apiUrl');
        $urlPath = '/' . \implode('/', $paths);
        $urlQuery = '';

        if (\count($query) > 0) {
            $mapQuery = \array_map(function ($key, $value) {
                return $key . '=' . $value;
            }, $query);

            $urlQuery = '?' . \implode('&', $mapQuery);
        }

        return $endpoint . $urlPath . $urlQuery;
    }
}
