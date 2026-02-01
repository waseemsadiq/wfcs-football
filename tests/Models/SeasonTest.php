<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Season;
use Tests\TestCase;
use ReflectionClass;

/**
 * Test cases for Season model.
 */
class SeasonTest extends TestCase
{
    private Season $season;

    protected function setup(): void
    {
        parent::setup();

        // Create a mock Season class that uses test data path
        $this->season = new class($this->testDataPath) extends Season {
            private string $dataPath;

            public function __construct(string $dataPath)
            {
                $this->dataPath = $dataPath . '/seasons';
                if (!is_dir($this->dataPath)) {
                    mkdir($this->dataPath, 0755, true);
                }
                parent::__construct();
            }

            protected function getDataPath(): string
            {
                return $this->dataPath . '/seasons.json';
            }
        };
    }

    /**
     * Test creating a season with custom ID.
     */
    public function testCreateWithId(): void
    {
        $data = [
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'startDate' => '2024-08-01',
            'endDate' => '2025-05-31',
        ];

        $result = $this->season->createWithId($data);

        $this->assertEquals('2024-25', $result['id']);
        $this->assertEquals('2024-25 Season', $result['name']);
        $this->assertEquals('2024-25-season', $result['slug']);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('leagueIds', $result);
        $this->assertArrayHasKey('cupIds', $result);
        $this->assertEquals([], $result['leagueIds']);
        $this->assertEquals([], $result['cupIds']);
        $this->assertFalse($result['isActive']);
    }

    /**
     * Test getting active season.
     */
    public function testGetActive(): void
    {
        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24 Season',
            'isActive' => false,
        ]);

        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'isActive' => true,
        ]);

        $active = $this->season->getActive();

        $this->assertNotNull($active);
        $this->assertEquals('2024-25', $active['id']);
        $this->assertTrue($active['isActive']);
    }

    /**
     * Test no active season returns null.
     */
    public function testGetActiveReturnsNullWhenNone(): void
    {
        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24 Season',
            'isActive' => false,
        ]);

        $active = $this->season->getActive();

        $this->assertNull($active);
    }

    /**
     * Test setting a season as active deactivates others.
     */
    public function testSetActive(): void
    {
        $id1 = $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24'])['id'];
        $id2 = $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25'])['id'];

        // Set second as active
        $result = $this->season->setActive($id2);
        $this->assertTrue($result);

        // Verify second is active
        $active = $this->season->getActive();
        $this->assertEquals($id2, $active['id']);

        // Verify first is no longer active
        $first = $this->season->find($id1);
        $this->assertFalse($first['isActive']);
    }

    /**
     * Test adding a league to a season.
     */
    public function testAddLeague(): void
    {
        $season = $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);
        $seasonId = $season['id'];

        $result = $this->season->addLeague($seasonId, 'league-1');

        $this->assertTrue($result);

        $updated = $this->season->find($seasonId);
        $this->assertArrayHasKey('leagueIds', $updated);
        $this->assertCount(1, $updated['leagueIds']);
        $this->assertEquals('league-1', $updated['leagueIds'][0]);
    }

    /**
     * Test adding duplicate league doesn't duplicate.
     */
    public function testAddLeagueDoesNotDuplicate(): void
    {
        $season = $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);
        $seasonId = $season['id'];

        $this->season->addLeague($seasonId, 'league-1');
        $result = $this->season->addLeague($seasonId, 'league-1');

        $this->assertTrue($result);

        $updated = $this->season->find($seasonId);
        $this->assertCount(1, $updated['leagueIds']);
    }

    /**
     * Test removing a league from a season.
     */
    public function testRemoveLeague(): void
    {
        $season = $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);
        $seasonId = $season['id'];

        $this->season->addLeague($seasonId, 'league-1');
        $this->season->addLeague($seasonId, 'league-2');

        $result = $this->season->removeLeague($seasonId, 'league-1');

        $this->assertTrue($result);

        $updated = $this->season->find($seasonId);
        $this->assertCount(1, $updated['leagueIds']);
        $this->assertEquals('league-2', $updated['leagueIds'][0]);
    }

    /**
     * Test adding and removing a cup.
     */
    public function testAddAndRemoveCup(): void
    {
        $season = $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);
        $seasonId = $season['id'];

        $this->season->addCup($seasonId, 'cup-1');

        $updated = $this->season->find($seasonId);
        $this->assertCount(1, $updated['cupIds']);

        $this->season->removeCup($seasonId, 'cup-1');

        $updated = $this->season->find($seasonId);
        $this->assertCount(0, $updated['cupIds']);
    }

    /**
     * Test season slug is generated from name.
     */
    public function testSlugGeneration(): void
    {
        $result = $this->season->createWithId([
            'id' => '2024-25',
            'name' => 'Premier League 2024-25',
        ]);

        $this->assertEquals('premier-league-2024-25', $result['slug']);
    }

    /**
     * Test all seasons sorted by start date.
     */
    public function testAllSorted(): void
    {
        $this->season->createWithId([
            'id' => '2022-23',
            'name' => '2022-23',
            'startDate' => '2022-08-01',
        ]);

        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25',
            'startDate' => '2024-08-01',
        ]);

        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24',
            'startDate' => '2023-08-01',
        ]);

        $sorted = $this->season->allSorted();

        // Should be sorted with newest first
        $this->assertEquals('2024-25', $sorted[0]['id']);
        $this->assertEquals('2023-24', $sorted[1]['id']);
        $this->assertEquals('2022-23', $sorted[2]['id']);
    }

    /**
     * Test checking if season ID exists.
     */
    public function testIdExists(): void
    {
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);

        $this->assertTrue($this->season->idExists('2024-25'));
        $this->assertFalse($this->season->idExists('2025-26'));
    }
}
