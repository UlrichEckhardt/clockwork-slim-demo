<?php

declare(strict_types=1);

namespace ClockworkSlimDemo;

use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;

/**
 * Baseclass for API test cases
 *
 * This sets up a Guzzle HTTP client, which you can use for making requests.
 */
abstract class ApiTestCase extends TestCase
{
    protected ?HttpClient $httpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = new HttpClient([
            // set base URL
            // The default value assumes you're serving this with
            // `php -S localhost:8080 -t public public/index.php`.
            'base_uri' => getenv('BASE_URL') ?: 'http://localhost:8080/',
            // return failure responses instead of throwing
            // This makes it more convenient to check status codes in tests.
            'http_errors' => false,
        ]);
    }

    public function tearDown(): void
    {
        $this->httpClient = null;

        parent::tearDown();
    }
}
