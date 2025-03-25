<?php

declare(strict_types=1);

namespace App\tests;

use App\RecruitisApi\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use function in_array;
use function reset;
use function str_contains;
use function unserialize;

class ProxyingTest extends WebTestCase
{
    private static KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = static::createClient();
    }

    public function testProxyingWithCache(): void
    {
        $httpClientMock = new MockHttpClient([
            function (string $method, string $url, array $options = []) {
                self::assertSame($method, Request::METHOD_GET);
                self::assertTrue(str_contains($url, '/api2/me'));
                self::assertTrue(in_array('accept: application/json', $options['headers']));

                return new MockResponse(self::meEndpointDummyData());
            }
        ]);
        self::getContainer()->set('recruitis.http_client', $httpClientMock);

        /** @var ArrayAdapter $cache */
        $cache = self::getContainer()->get('cache.app');
        $cache->clear();

        self::$client->request(
            method: Request::METHOD_GET,
            uri: '/api/me',
            server: [
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $cachedValues = $cache->getValues();
        /** @var Response $cachedResponse */
        $cachedResponse = unserialize(reset($cachedValues));

        self::assertEquals(self::meEndpointDummyData(), $cachedResponse->content);
        self::assertSame(SfResponse::HTTP_OK, $cachedResponse->statusCode);
    }

    public function testProxyingWithSkippedCache(): void
    {
        $httpClientMock = new MockHttpClient([new MockResponse(self::meEndpointDummyData())]);
        self::getContainer()->set('recruitis.http_client', $httpClientMock);

        /** @var ArrayAdapter $cache */
        $cache = self::getContainer()->get('cache.app');
        $cache->clear();

        self::$client->request(
            method: Request::METHOD_POST,
            uri: '/api/me',
            server: [
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: ''
        );

        self::assertEmpty($cache->getValues());
    }

    private static function meEndpointDummyData(): string
    {
        return <<<RESPONSE
{
    "payload": {
        "token_id": 28224,
        "employee_id": 11169,
        "company_id": 8636,
        "device_id": null,
        "device_name": "api",
        "device_capability": null,
        "fullname": "Andrej Novák",
        "initials": "AN",
        "email": "testovaciucet@pracevcr.cz",
        "company_name": "Testovací účet pro API",
        "avatar": "https://app.recruitis.io/upload/initials_avatars/d9681d05860552e9c3113da381f916fc.png",
        "token_type": "limited_access",
        "token_permissions": [
            "api.device.read",
            "api.position.read",
            "api.candidates.write"
        ],
        "account_role": "admin",
        "token_date_expiration": null,
        "quotas": {
            "daily_quota_candidates_delete": 0
        },
        "extra": []
    },
    "meta": {
        "code": "api.ok",
        "duration": 40,
        "message": "OK"
    }
}
RESPONSE;
    }
}
