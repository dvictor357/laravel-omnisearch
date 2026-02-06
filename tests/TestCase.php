<?php

namespace OmniSearch\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            \OmniSearch\OmniSearchServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'OmniSearch' => \OmniSearch\Facades\OmniSearch::class,
        ];
    }
}
