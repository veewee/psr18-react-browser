<?php
declare(strict_types=1);

namespace Veewee\Psr18ReactBrowser;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use function React\Async\await;

final class Psr18ReactBrowserClient implements ClientInterface
{
    public function __construct(
        private readonly Browser $browser
    ) {
    }

    public static function default(): self
    {
        return new self(new Browser());
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = await(
            $this->browser->request(
                $request->getMethod(),
                (string) $request->getUri(),
                $request->getHeaders(),
                (string) $request->getBody()
            )
        );

        assert($response instanceof ResponseInterface);

        return $response;
    }
}
