<?php
declare(strict_types=1);

namespace zullusa\GigaChat;

use zullusa\GigaChat\Type\AccessToken;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class GigaChatOAuth
{
    private string $clientId;
    private string $clientSecret;
    private string $scope;
    private ?ClientInterface $client = null;

    public static function withApiKey(
        string           $apiKey,
                         $cert,
        string           $scope = 'GIGACHAT_API_PERS',
        ?ClientInterface $client = null
    )
    {
        $authInfo = explode(':', base64_decode($apiKey));
        return new GigaChatOAuth($authInfo[0], $authInfo[1], $cert, $scope, $client);
    }

    public function __construct(
        string           $clientId,
        string           $clientSecret,
                         $cert,
        string           $scope = 'GIGACHAT_API_PERS',
        ?ClientInterface $client = null
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;

        if ($client === null) {
            $this->client = new Client([
                'base_uri' => Url::OAUTH_API_URL,
                RequestOptions::VERIFY => $cert,
            ]);
        } else {
            $this->client = $client;
        }
    }

    public function getAccessToken(?string $rqUID = null): AccessToken
    {
        if ($rqUID === null) {
            $rqUID = Uuid::uuid4();
        }

        $response = $this->client->sendAsync(
            new Request(
                'POST',
                'oauth',
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'RqUID' => $rqUID,
                ],
                http_build_query(
                    [
                        'scope' => $this->scope,
                    ],
                    '',
                    '&'
                ),
            )
        )->wait();

        return AccessToken::createFromArray($this->json($response));
    }

    private function json(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}
