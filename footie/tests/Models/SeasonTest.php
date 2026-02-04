<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Season;
use Tests\TestCase;

/**
 * Comprehensive test cases for Season model.
 * Tests all CRUD operations and season-specific functionality.
 */
class SeasonTest extends TestCase
{
    private Season $season;

    protected function setup(): void
    {
        parent::setup();
        $this->season = new Season();
    }

    /**
     * Test creating a season.
     */
    public function testCreate(): void
    {
        $result = $this->season->create([
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
        ]);

        $this->assertEquals('2024-25', $result['id']);
        $this->assertEquals('2024-25 Season', $result['name']);
        $this->assertEquals('2024-25-season', $result['slug']);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);
        $this->assertEquals('2024-08-01', $result['startDate']);
        $this->assertEquals('2025-05-31', $result['endDate']);
    }

    /**
     * Test creating a season with custom ID using createWithId.
     */
    public function testCreateWithId(): void
    {
        $result = $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
        ]);

        $this->assertEquals('2024-25', $result['id']);
        $this->assertEquals('2024-25 Season', $result['name']);
        $this->assertEquals('2024-25-season', $result['slug']);
    }

    /**
     * Test slug auto-generation.
     */
    public function testSlugAutoGeneration(): void
    {
        $result = $this->season->create([
            'id' => '2024-25',
            'name' => 'Premier League 2024-25',
        ]);

        $this->assertEquals('premier-league-2024-25', $result['slug']);
    }

    /**
     * Test finding a season by ID.
     */
    public function testFind(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ]);

        $found = $this->season->find('2024-25');

        $this->assertNotNull($found);
        $this->assertEquals('2024-25', $found['id']);
        $this->assertEquals('2024-25 Season', $found['name']);
    }

    /**
     * Test finding non-existent season returns null.
     */
    public function testFindNonExistent(): void
    {
        $result = $this->season->find('9999-00');
        $this->assertNull($result);
    }

    /**
     * Test updating a season.
     */
    public function testUpdate(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ]);

        $result = $this->season->update('2024-25', [
            'name' => '2024-25 Premier League',
            'start_date' => '2024-09-01',
        ]);

        $this->assertTrue($result);

        $updated = $this->season->find('2024-25');
        $this->assertEquals('2024-25 Premier League', $updated['name']);
        $this->assertEquals('2024-25-premier-league', $updated['slug']);
        $this->assertEquals('2024-09-01', $updated['startDate']);
    }

    /**
     * Test updating non-existent season returns false.
     */
    public function testUpdateNonExistent(): void
    {
        $result = $this->season->update('9999-00', ['name' => 'Test']);
        $this->assertFalse($result);
    }

    /**
     * Test deleting a season.
     */
    public function testDelete(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ]);

        $result = $this->season->delete('2024-25');

        $this->assertTrue($result);
        $this->assertNull($this->season->find('2024-25'));
    }

    /**
     * Test deleting non-existent season returns false.
     */
    public function testDeleteNonExistent(): void
    {
        $result = $this->season->delete('9999-00');
        $this->assertFalse($result);
    }

    /**
     * Test getting active season.
     */
    public function testGetActive(): void
    {
        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24 Season',
            'is_active' => 0,
        ]);

        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'is_active' => 1,
        ]);

        $active = $this->season->getActive();

        $this->assertNotNull($active);
        $this->assertEquals('2024-25', $active['id']);
        $this->assertEquals(1, $active['isActive']);
    }

    /**
     * Test no active season returns null.
     */
    public function testGetActiveReturnsNullWhenNone(): void
    {
        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24 Season',
            'is_active' => 0,
        ]);

        $active = $this->season->getActive();

        $this->assertNull($active);
    }

    /**
     * Test setting a season as active deactivates others.
     */
    public function testSetActive(): void
    {
        $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24', 'is_active' => 1]);
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25', 'is_active' => 0]);

        $result = $this->season->setActive('2024-25');
        $this->assertTrue($result);

        $active = $this->season->getActive();
        $this->assertEquals('2024-25', $active['id']);

        $first = $this->season->find('2023-24');
        $this->assertEquals(0, $first['isActive']);
    }

    /**
     * Test setting non-existent season as active returns false.
     */
    public function testSetActiveNonExistent(): void
    {
        $result = $this->season->setActive('9999-00');
        $this->assertFalse($result);
    }

    /**
     * Test all seasons sorted by start date.
     */
    public function testAllSorted(): void
    {
        $this->season->createWithId([
            'id' => '2022-23',
            'name' => '2022-23',
            'start_date' => '2022-08-01',
        ]);

        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25',
            'start_date' => '2024-08-01',
        ]);

        $this->season->createWithId([
            'id' => '2023-24',
            'name' => '2023-24',
            'start_date' => '2023-08-01',
        ]);

        $sorted = $this->season->allSorted();

        $this->assertEquals('2024-25', $sorted[0]['id']);
        $this->assertEquals('2023-24', $sorted[1]['id']);
        $this->assertEquals('2022-23', $sorted[2]['id']);
    }

    /**
     * Test allSorted includes league and cup IDs.
     */
    public function testAllSortedIncludesCompetitions(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25',
        ]);

        $sorted = $this->season->allSorted();

        $this->assertArrayHasKey('leagueIds', $sorted[0]);
        $this->assertArrayHasKey('cupIds', $sorted[0]);
        $this->assertEquals([], $sorted[0]['leagueIds']);
        $this->assertEquals([], $sorted[0]['cupIds']);
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

    /**
     * Test exists method.
     */
    public function testExists(): void
    {
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);

        $this->assertTrue($this->season->exists('2024-25'));
        $this->assertFalse($this->season->exists('2025-26'));
    }

    /**
     * Test getting all seasons.
     */
    public function testAll(): void
    {
        $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24']);
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);

        $all = $this->season->all();

        $this->assertCount(2, $all);
    }

    /**
     * Test counting seasons.
     */
    public function testCount(): void
    {
        $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24']);
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25']);
        $this->season->createWithId(['id' => '2025-26', 'name' => '2025-26']);

        $count = $this->season->count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test finding by slug.
     */
    public function testFindWhere(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => 'Premier League 2024-25',
        ]);

        $found = $this->season->findWhere('slug', 'premier-league-2024-25');

        $this->assertNotNull($found);
        $this->assertEquals('2024-25', $found['id']);
    }

    /**
     * Test unique slug generation.
     */
    public function testUniqueSlugGeneration(): void
    {
        $season1 = $this->season->createWithId([
            'id' => '2024-25',
            'name' => 'Main Season',
        ]);

        $season2 = $this->season->createWithId([
            'id' => '2025-26',
            'name' => 'Main Season',
        ]);

        $this->assertEquals('main-season', $season1['slug']);
        $this->assertEquals('main-season-1', $season2['slug']);
    }

    /**
     * Test update preserves slug when name doesn't change.
     */
    public function testUpdatePreservesSlugWhenNameUnchanged(): void
    {
        $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ]);

        $original = $this->season->find('2024-25');
        $originalSlug = $original['slug'];

        $this->season->update('2024-25', ['start_date' => '2024-09-01']);

        $updated = $this->season->find('2024-25');
        $this->assertEquals($originalSlug, $updated['slug']);
    }

    /**
     * Test getLeagues method.
     */
    public function testGetLeagues(): void
    {
        $seasonId = $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ])['id'];

        $leagues = $this->season->getLeagues($seasonId);

        $this->assertIsArray($leagues);
        $this->assertCount(0, $leagues);
    }

    /**
     * Test getCups method.
     */
    public function testGetCups(): void
    {
        $seasonId = $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ])['id'];

        $cups = $this->season->getCups($seasonId);

        $this->assertIsArray($cups);
        $this->assertCount(0, $cups);
    }

    /**
     * Test creating season with all fields.
     */
    public function testCreateWithAllFields(): void
    {
        $result = $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
            'slug' => 'custom-slug',
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
            'is_active' => 1,
        ]);

        $this->assertEquals('2024-25', $result['id']);
        $this->assertEquals('2024-25 Season', $result['name']);
        $this->assertEquals('custom-slug', $result['slug']);
        $this->assertEquals('2024-08-01', $result['startDate']);
        $this->assertEquals('2025-05-31', $result['endDate']);
        $this->assertEquals(1, $result['isActive']);
    }

    /**
     * Test default is_active is 0.
     */
    public function testDefaultIsActiveFalse(): void
    {
        $result = $this->season->createWithId([
            'id' => '2024-25',
            'name' => '2024-25 Season',
        ]);

        $this->assertEquals(0, $result['isActive']);
    }

    /**
     * Test only one season can be active at a time.
     */
    public function testOnlyOneSeasonActive(): void
    {
        $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24', 'is_active' => 0]);
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25', 'is_active' => 0]);

        $this->season->setActive('2023-24');
        $active1 = $this->season->getActive();
        $this->assertEquals('2023-24', $active1['id']);

        $this->season->setActive('2024-25');
        $active2 = $this->season->getActive();
        $this->assertEquals('2024-25', $active2['id']);

        $season2023 = $this->season->find('2023-24');
        $this->assertEquals(0, $season2023['isActive']);
    }

    /**
     * Test where method.
     */
    public function testWhere(): void
    {
        $this->season->createWithId(['id' => '2023-24', 'name' => '2023-24', 'is_active' => 1]);
        $this->season->createWithId(['id' => '2024-25', 'name' => '2024-25', 'is_active' => 0]);

        $active = $this->season->where('is_active', 1);

        $this->assertCount(1, $active);
        $this->assertEquals('2023-24', $active[0]['id']);
    }
}
