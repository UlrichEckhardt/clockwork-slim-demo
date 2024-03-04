<?php

declare(strict_types=1);

namespace ClockworkSlimDemo;

/**
 * Test access to the Clockwork API
 */
class ClockworkApiTest extends ApiTestCase
{
    public function testGreet(): string
    {
        $response = $this->httpClient->get('hello/world');

        static::assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();
        static::assertEquals('Hello, world', $body);

        $id = $response->getHeader('X-Clockwork-Id');
        static::assertCount(1, $id);
        static::assertNotEmpty($id[0]);

        return $id[0];
    }

    /**
     * @depends testGreet
     */
    public function testGetMetrics(string $id): void
    {
        $response = $this->httpClient->get('__clockwork/' . $id);

        static::assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeader('Content-Type')[0] ?? null;
        $this->assertEquals('application/json', $contentType);

        static::assertFalse($response->hasHeader('X-Clockwork-Id'), 'no ID expected for Clockwork requests');

        $body = $response->getBody()->getContents();
        static::assertJson($body);

        $data = json_decode($body, true, 5, JSON_THROW_ON_ERROR);
        static::assertEquals($id, $data['id'] ?? null);
        static::assertEquals('request', $data['type'] ?? null);
        static::assertEquals('GET', $data['method'] ?? null);
        static::assertEquals('/hello/world', $data['uri'] ?? null);
    }

    /**
     * @depends testGreet
     */
    public function testGetLatest(string $id): void
    {
        $response = $this->httpClient->get('__clockwork/latest');

        $contentType = $response->getHeader('Content-Type')[0] ?? null;
        $this->assertEquals('application/json', $contentType);

        static::assertFalse($response->hasHeader('X-Clockwork-Id'), 'no ID expected for Clockwork requests');

        $body = $response->getBody()->getContents();
        static::assertJson($body);

        $data = json_decode($body, true, 5, JSON_THROW_ON_ERROR);
        static::assertEquals($id, $data['id'] ?? null);
    }
}
