<?php

namespace OmniSearch\Tests\Unit;

use OmniSearch\Contracts\SearchResult;
use OmniSearch\Contracts\SearchSource;
use OmniSearch\Data\Result;
use PHPUnit\Framework\TestCase;

class ContractsTest extends TestCase
{
    public function test_result_implements_search_result(): void
    {
        $result = Result::navigate(
            id: 'test',
            title: 'Test',
            description: null,
            url: '/test',
            icon: 'test',
            group: 'Test',
        );

        $this->assertInstanceOf(SearchResult::class, $result);
    }

    public function test_result_has_all_required_methods(): void
    {
        $result = Result::navigate(
            id: 'test',
            title: 'Test',
            description: 'Description',
            url: '/test',
            icon: 'test',
            group: 'Test',
        );

        $this->assertTrue(method_exists($result, 'getId'));
        $this->assertTrue(method_exists($result, 'getTitle'));
        $this->assertTrue(method_exists($result, 'getDescription'));
        $this->assertTrue(method_exists($result, 'getUrl'));
        $this->assertTrue(method_exists($result, 'getIcon'));
        $this->assertTrue(method_exists($result, 'getGroup'));
        $this->assertTrue(method_exists($result, 'getActionType'));
        $this->assertTrue(method_exists($result, 'getScore'));
        $this->assertTrue(method_exists($result, 'getActionPayload'));
        $this->assertTrue(method_exists($result, 'toArray'));
    }
}
