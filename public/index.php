<?php

declare(strict_types=1);

use Clockwork\DataSource\XdebugDataSource;
use Clockwork\Support\Vanilla\Clockwork;
use Clockwork\Support\Vanilla\ClockworkMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

/**
 * Add error middleware
 *
 * This gives readable error messages in the browser.
 */
$app->addErrorMiddleware(false, false, true);

/**
 * Create Clockwork helper
 */
$clockwork = Clockwork::init([
    'enable' => true,
]);
$clockwork->getClockwork()->addDataSource(new XdebugDataSource);

/**
 * Create Clockwork middleware
 *
 * This middleware handles requests to the Clockwork API and web frontend.
 */
$clockworkMiddleware = new ClockworkMiddleware($clockwork);
$clockworkMiddleware->withResponseFactory($app->getResponseFactory());

$app->add($clockworkMiddleware);

$app->add(static function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $uri = $request->getUri();
    error_log($uri->getPath());
    $response = $handler->handle($request);
    error_log('res = ' . $response->getStatusCode());
    return $response;
});

$app->get('/favicon.ico', function (Request $request, Response $response, array $args) {
    $streamFactory = new StreamFactory();
    return $response->withBody($streamFactory->createStreamFromFile(__DIR__ . '/favicon.ico'));
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->run();
