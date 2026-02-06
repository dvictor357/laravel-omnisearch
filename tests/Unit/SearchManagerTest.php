<?php

namespace OmniSearch\Tests\Unit;

use Illuminate\Support\Collection;
use OmniSearch\Contracts\SearchResult;
use OmniSearch\Data\Result;
use OmniSearch\Services\SearchManager;
use PHPUnit\Framework\TestCase;
use Mockery;

class SearchManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_search_returns_empty_for_empty_query(): void
    {
        $manager = new SearchManager(Mockery::mock(\Illuminate\Contracts\Foundation\Application::class));

        $result = $manager->search('');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_search_returns_results_from_sources(): void
    {
        $app = Mockery::mock(\Illuminate\Contracts\Foundation\Application::class);
        $manager = new SearchManager($app);

        $source = new class implements \OmniSearch\Contracts\SearchSource {
            public function getKey(): string { return 'test'; }
            public function getLabel(): string { return 'Test'; }
            public function getIcon(): string { return 'test'; }
            public function authorize(): bool { return true; }
            public function getSynonyms(): array { return []; }
            public function getDependencies(): array { return []; }

            public function search(string $query): Collection
            {
                return collect([
                    Result::navigate(
                        id: '1',
                        title: 'Result 1',
                        description: 'Desc 1',
                        url: '/url1',
                        icon: 'user',
                        group: 'Test',
                    ),
                ]);
            }
        };

        $manager->registerSource($source);

        $result = $manager->search('test');

        $this->assertCount(1, $result);
        $this->assertEquals('Result 1', $result[0]['title']);
    }

    public function test_search_groups_results(): void
    {
        $app = Mockery::mock(\Illuminate\Contracts\Foundation\Application::class);
        $manager = new SearchManager($app);

        $source = new class implements \OmniSearch\Contracts\SearchSource {
            public function getKey(): string { return 'test'; }
            public function getLabel(): string { return 'Test Group'; }
            public function getIcon(): string { return 'test'; }
            public function authorize(): bool { return true; }
            public function getSynonyms(): array { return []; }
            public function getDependencies(): array { return []; }

            public function search(string $query): Collection
            {
                return collect([
                    Result::navigate('1', 'Result 1', 'Desc 1', '/url1', 'user', 'Group A'),
                    Result::navigate('2', 'Result 2', 'Desc 2', '/url2', 'link', 'Group B'),
                ]);
            }
        };

        $manager->registerSource($source);

        $grouped = $manager->searchGrouped('test');

        $this->assertTrue($grouped->has('Group A'));
        $this->assertTrue($grouped->has('Group B'));
    }
}
