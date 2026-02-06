<?php

namespace OmniSearch\Tests\Unit;

use OmniSearch\Data\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function test_create_navigate_result(): void
    {
        $result = Result::navigate(
            id: 'test-1',
            title: 'Test Title',
            description: 'Test Description',
            url: '/test-url',
            icon: 'user',
            group: 'Test Group',
        );

        $this->assertEquals('test-1', $result->getId());
        $this->assertEquals('Test Title', $result->getTitle());
        $this->assertEquals('Test Description', $result->getDescription());
        $this->assertEquals('/test-url', $result->getUrl());
        $this->assertEquals('user', $result->getIcon());
        $this->assertEquals('Test Group', $result->getGroup());
        $this->assertEquals('navigate', $result->getActionType());
        $this->assertNull($result->getScore());
        $this->assertNull($result->getActionPayload());
    }

    public function test_create_copy_result(): void
    {
        $result = Result::copy(
            id: 'test-copy',
            title: 'Copy This',
            description: 'Click to copy',
            textToCopy: 'text to copy',
            icon: 'copy',
            group: 'Actions',
        );

        $this->assertEquals('copy', $result->getActionType());
        $this->assertEquals('text to copy', $result->getActionPayload());
    }

    public function test_create_modal_result(): void
    {
        $result = Result::modal(
            id: 'test-modal',
            title: 'Open Modal',
            description: 'Opens a modal',
            modalName: 'user-modal',
            icon: 'expand',
            group: 'Actions',
        );

        $this->assertEquals('modal', $result->getActionType());
        $this->assertEquals('user-modal', $result->getActionPayload());
    }

    public function test_create_scored_result(): void
    {
        $result = Result::scored(
            id: 'test-scored',
            title: 'Scored Result',
            description: 'Has a score',
            url: null,
            icon: 'star',
            group: 'Results',
            actionType: 'navigate',
            score: 0.85,
            payload: null,
        );

        $this->assertEquals(0.85, $result->getScore());
    }

    public function test_to_array(): void
    {
        $result = Result::navigate(
            id: 'test-array',
            title: 'Array Test',
            description: 'Array Description',
            url: '/array-url',
            icon: 'link',
            group: 'Array Group',
        );

        $array = $result->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('test-array', $array['id']);
        $this->assertEquals('Array Test', $array['title']);
        $this->assertEquals('Array Description', $array['description']);
        $this->assertEquals('/array-url', $array['url']);
        $this->assertEquals('link', $array['icon']);
        $this->assertEquals('Array Group', $array['group']);
        $this->assertEquals('navigate', $array['actionType']);
    }
}
