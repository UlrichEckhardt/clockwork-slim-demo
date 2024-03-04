<?php

declare(strict_types=1);

namespace ClockworkSlimDemo;

/**
 * Test access to the Clockwork web UI
 */
class ClockworkWebUiTest extends ApiTestCase
{
    public function testIndexHtml(): void
    {
        $response = $this->httpClient->get('clockwork');

        static::assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeader('Content-Type')[0] ?? null;
        $this->assertEquals('text/html; charset=UTF-8', $contentType);

        $body = $response->getBody()->getContents();
        static::assertNotEmpty($body);

        static::assertFalse($response->hasHeader('X-Clockwork-Id'), 'no Clockwork request ID expected');
    }
}
