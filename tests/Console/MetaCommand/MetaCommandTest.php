<?php

declare(strict_types=1);

namespace Barryvdh\LaravelIdeHelper\Tests\Console\MetaCommand;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Barryvdh\LaravelIdeHelper\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

class MetaCommandTest extends TestCase
{
    public function testCommand(): void
    {
        $this->mockFilesystem();

        /** @var Filesystem|MockInterface $mockFileSystem */
        $mockFileSystem = $this->app->make(Filesystem::class);
        $this->instance('files', $mockFileSystem);

        $mockFileSystem
            ->shouldReceive('getRequire')
            ->andReturnUsing(function ($__path, $__data) {
                return (static function () use ($__path, $__data) {
                    extract($__data, EXTR_SKIP);

                    return require $__path;
                })();
            });

        $this->artisan('ide-helper:meta');

        // We're not testing the whole file, just some basic structure elements
        self::assertStringContainsString("namespace PHPSTORM_META {\n", $this->mockFilesystemOutput);
        self::assertStringContainsString("PhpStorm Meta file, to provide autocomplete information for PhpStorm\n", $this->mockFilesystemOutput);
        self::assertStringContainsString('override(', $this->mockFilesystemOutput);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [IdeHelperServiceProvider::class];
    }
}
