<?php

declare(strict_types=1);

namespace ClockworkSlimDemo;

/**
 * Test access to the favicon
 */
class FaviconApiTest extends ApiTestCase
{
    public function testFavicon(): void
    {
        $response = $this->httpClient->get('favicon.ico');

        static::assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        static::assertNotEmpty($body);
    }
}
