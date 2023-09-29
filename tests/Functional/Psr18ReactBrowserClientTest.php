<?php
declare(strict_types=1);

namespace Functional;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;
use Veewee\Psr18ReactBrowser\Psr18ReactBrowserClient;

final class Psr18ReactBrowserClientTest extends TestCase
{
    
    public function test_it_can_send_request_over_react_browser(): void
    {
        $client = Psr18ReactBrowserClient::default();
        $factory = new Psr17Factory();

        $this->onServer(function () use ($client, $factory) {
            $response = $client->sendRequest(
                $factory->createRequest('GET', 'http://127.0.0.1:8888/success.json')
            );

            self::assertSame(200, $response->getStatusCode());
            self::assertStringEqualsFile($this->serverLocation('/success.json'), (string) $response->getBody());
        });
    }

    /**
     * @param callable(): void $run
     */
    private function onServer(callable $run)
    {
        $process = Process::fromShellCommandline('php -S 127.0.0.1:8888 -t '.$this->serverLocation());
        $process->start();
        $started = $process->waitUntil(static function ($type, $output) {
            return str_contains($output, 'started');
        });

        if (!$started) {
            throw new RuntimeException('Unable to start HTTP server');
        }

        try {
            $run();
        } finally {
            $process->stop();
        }
    }

    private function serverLocation(string $path = ''): string
    {
        return FIXTURE_DIR.'/functional/server'.$path;
    }
}
