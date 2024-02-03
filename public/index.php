<?php

declare(strict_types=1);

use Clockwork\DataSource\XdebugDataSource;
use Clockwork\Storage\FileStorage;
use Clockwork\Support\Psr\Middleware;
use Clockwork\Support\Vanilla\Clockwork as VanillaClockwork;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Middleware.php';

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
 * create response and stream factories
 *
 * These are used to generate HTTP responses. Normally, those would come from
 * a DI container.
 */
$responseFactory = new ResponseFactory();
$streamFactory = new StreamFactory();

/**
 * Storage Dir For Clockwork Profiles
 *
 * This must be a readable and writable directory. It's not necessary to
 * create it up front though, it is created implicitly.
 */
$clockworkStorageDir = __DIR__ . '/storage/clockwork';

/**
 * Instantiate Clockwork Service Class
 *
 * More specifically, we use the "Vanilla" helper built on top of it. The
 * helper specifically provides configuration handling,
 */
$clockwork = new class extends VanillaClockwork
{
	// TODO: All these methods here need to be integrated into the
	// upstream VanillaClockwork.
	public function isEnabled()
	{
		return $this->config['enable'];
	}

	public function getApiPath()
	{
		return $this->config['api'];
	}

	public function isWebEnabled()
	{
		return $this->config['web']['enable'];
	}

	public function getWebPath()
	{
		// TODO: Clarify what the meaning here is.
		// return $this->config['web']['uri'];
		return '/web';
	}

	public function isAuthenticationEnabled()
	{
		return $this->config['authentication'];
	}
};
$clockwork->getClockwork()->addDataSource(new XdebugDataSource());
$clockwork->getClockwork()->storage(new FileStorage($clockworkStorageDir));

/**
 * Install Clockwork middleware
 *
 * More specifically, we use the "Vanilla" helper built on top of it. The
 * helper specifically provides configuration handling,
 */
$app->add(
    new Middleware(
        $clockwork,
        $responseFactory,
        $streamFactory
    )
);

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
