<?php
declare(strict_types=1);

namespace Functional;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Veewee\Psr18ReactBrowser\Psr18ReactBrowserClient;

class Psr18ReactBrowserClientTest extends TestCase
{
    /** @test */
    public function it_can_send_request_over_react_browser(): void
    {
        $client = Psr18ReactBrowserClient::default();
        $factory = new Psr17Factory();

        $this->onServer(static function () use ($client, $factory) {
            $response = $client->sendRequest(
                $factory->createRequest('GET', 'http://127.0.0.1:8000/success.json')
            );

            self::assertSame(200, $response->getStatusCode());
            self::assertSame('{"success": true}', (string) $response->getBody());
        });
    }

    /**
     * @param callable(): void $run
     */
    private function onServer(callable $run)
    {
        $process = Process::fromShellCommandline('composer functional-testserver');
        $process->start();
        $process->waitUntil(static fn () => true);

        try {
            $run();
        } finally {
            $process->stop();
        }
    }
}
