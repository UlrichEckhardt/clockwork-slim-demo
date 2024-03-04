<?php

declare(strict_types=1);

namespace ClockworkSlimDemo;

/**
 * Test access to the greeting API
 */
class GreetApiTest extends ApiTestCase
{
    public function testGreet(): void
    {
        $response = $this->httpClient->get('hello/world');

        static::assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        static::assertEquals('Hello, world', $body);
    }
}
