<?php

/*
 * Nytris Turbo - Turbocharged HTTP/FastCGI for PHP.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/turbo/
 *
 * Released under the MIT license.
 * https://github.com/nytris/turbo/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Nytris\Turbo\Tests\Functional\Simple;

use Asmblah\FastCgi\FastCgi;
use Asmblah\FastCgi\Launcher\LauncherInterface;
use Asmblah\FastCgi\Process\ProcessInterface;
use Asmblah\FastCgi\Session\SessionInterface;
use Nytris\Turbo\Tests\AbstractTestCase;
use Nytris\Turbo\Turbo;
use React\ChildProcess\Process;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SimpleFastCgiRequestTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SimpleFastCgiRequestTest extends AbstractTestCase
{
    private Process $process;
    private SessionInterface $session;

    public function setUp(): void
    {
        $baseDir = dirname(__DIR__, 3);
        $wwwDir = 'tests/Functional/Harness/Fixtures/Simple/www'; // Relative to $baseDir.

        $dataDir = $baseDir . '/var/test';
        (new Filesystem())->remove($dataDir);
        @mkdir($dataDir, 0700, true);
        $socketPath = $dataDir . '/ignition.test.sock';

        $this->process = new Process(implode(' ', [
            PHP_BINARY,
            '-dxdebug.mode=debug', // FIXME
            $baseDir . '/bin/turbo',
            'ignition',
            '-c', escapeshellarg($baseDir . '/tests/Functional/Harness/Fixtures/Simple/nytris.config.php'),
            '-l', escapeshellarg('unix://' . $socketPath),
        ]));
        $this->process->start();

        $fakeProcess = mock(ProcessInterface::class, [
            'quit' => null,
            'waitUntilReady' => null,
        ]);
        $fakeProcess->allows('waitUntilReady')
            ->andReturnUsing(function () {
                sleep(2); // FIXME
            })
            ->byDefault();

        $fastCgi = new FastCgi(
            baseDir: $baseDir,
            wwwDir: $wwwDir,
            socketPath: $socketPath,
            launcher: mock(LauncherInterface::class, [
                'launch' => $fakeProcess,
            ])
        );
        $this->session = $fastCgi->start();
    }

    public function tearDown(): void
    {
        $this->session->quit();
        $this->process->terminate();

        Turbo::uninstall();
    }

    public function testCanHandleSimpleHttpGetRequest(): void
    {
        $response = $this->session->sendGetRequest(
            'my_script.php',
            '/path/to/my-page',
            [
                'greeting' => 'Hello',
            ]
        );

        static::assertSame(
            'Hello world from Nytris Turbo!',
            $response->getBody()
        );
        static::assertSame(
            'Status: 200 OK' . "\r\n" .
            'Content-Type: text/plain' . "\r\n" .
            'X-My-Custom-Response-Header: Hello world!' . "\r\n" .
            "\r\n" .
            "Hello world from Nytris Turbo!",
            $response->getOutput()
        );
        static::assertEquals(
            [
                'Status' => ['200 OK'],
                'Content-Type' => ['text/plain'],
                'X-My-Custom-Response-Header' => ['Hello world!'],
            ],
            $response->getHeaders()
        );
    }
}
