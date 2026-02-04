<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Cup;
use App\Models\Team;
use Tests\TestCase;

/**
 * Comprehensive test cases for Cup model.
 * Tests all CRUD operations, bracket generation, and winner advancement.
 */
class CupTest extends TestCase
{
    private Cup $cup;
    private Team $team;

    protected function setup(): void
    {
        parent::setup();
        $this->cup = new Cup();
        $this->team = new Team();
    }

    /**
     * Test creating a cup.
     */
    public function testCreate(): void
    {
        $seasonId = $this->createTestSeason();

        $result = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'frequency' => 'weekly',
            'match_time' => '15:00',
        ]);

        $this->assertNotNull($result['id']);
        $this->assertEquals('FA Cup', $result['name']);
        $this->assertEquals('fa-cup', $result['slug']);
        $this->assertEquals('weekly', $result['frequency']);
        $this->assertEquals('15:00:00', $result['matchTime']);
        $this->assertArrayHasKey('createdAt', $result);
    }

    /**
     * Test creating a cup with teams auto-generates bracket.
     */
    public function testCreateWithTeamsGeneratesBracket(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $result = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'frequency' => 'weekly',
            'match_time' => '15:00',
            'team_ids' => $teamIds,
        ]);

        $this->assertNotNull($result['id']);
        $rounds = $this->cup->getRounds($result['id']);
        $this->assertGreaterThan(0, count($rounds));
    }

    /**
     * Test finding a cup.
     */
    public function testFind(): void
    {
        $seasonId = $this->createTestSeason();
        $created = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $found = $this->cup->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('FA Cup', $found['name']);
        $this->assertArrayHasKey('teamIds', $found);
        $this->assertArrayHasKey('rounds', $found);
    }

    /**
     * Test updating a cup.
     */
    public function testUpdate(): void
    {
        $seasonId = $this->createTestSeason();
        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $result = $this->cup->update($cup['id'], [
            'name' => 'League Cup',
            'frequency' => 'fortnightly',
        ]);

        $this->assertTrue($result);

        $updated = $this->cup->find($cup['id']);
        $this->assertEquals('League Cup', $updated['name']);
        $this->assertEquals('league-cup', $updated['slug']);
        $this->assertEquals('fortnightly', $updated['frequency']);
    }

    /**
     * Test deleting a cup.
     */
    public function testDelete(): void
    {
        $seasonId = $this->createTestSeason();
        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $result = $this->cup->delete($cup['id']);

        $this->assertTrue($result);
        $this->assertNull($this->cup->find($cup['id']));
    }

    /**
     * Test getting team IDs.
     */
    public function testGetTeamIds(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $retrievedIds = $this->cup->getTeamIds($cup['id']);

        $this->assertCount(4, $retrievedIds);
        $this->assertArrayEquals($teamIds, $retrievedIds);
    }

    /**
     * Test setting teams.
     */
    public function testSetTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $teamIds = $this->createTestTeams(4);
        $this->cup->setTeams($cup['id'], $teamIds);

        $retrievedIds = $this->cup->getTeamIds($cup['id']);
        $this->assertCount(4, $retrievedIds);
    }

    /**
     * Test adding a single team.
     */
    public function testAddTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => [$teamIds[0]],
        ]);

        $this->cup->addTeam($cup['id'], $teamIds[1]);

        $retrievedIds = $this->cup->getTeamIds($cup['id']);
        $this->assertCount(2, $retrievedIds);
    }

    /**
     * Test removing a team.
     */
    public function testRemoveTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(3);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $this->cup->removeTeam($cup['id'], $teamIds[1]);

        $retrievedIds = $this->cup->getTeamIds($cup['id']);
        $this->assertCount(2, $retrievedIds);
    }

    /**
     * Test checking if cup has a team.
     */
    public function testHasTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => [$teamIds[0]],
        ]);

        $this->assertTrue($this->cup->hasTeam($cup['id'], $teamIds[0]));
        $this->assertFalse($this->cup->hasTeam($cup['id'], $teamIds[1]));
    }

    /**
     * Test getting teams count.
     */
    public function testGetTeamsCount(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(5);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $count = $this->cup->getTeamsCount($cup['id']);
        $this->assertEquals(5, $count);
    }

    /**
     * Test generating bracket with power of 2 teams.
     */
    public function testGenerateBracketPowerOfTwo(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $result = $this->cup->generateBracket(
            $cup['id'],
            $teamIds,
            '2025-08-01',
            'weekly',
            '15:00'
        );

        $this->assertTrue($result);

        $rounds = $this->cup->getRounds($cup['id']);
        $this->assertCount(2, $rounds); // Semi + Final
        $this->assertEquals('Semi-Final', $rounds[0]['name']);
        $this->assertEquals('Final', $rounds[1]['name']);
    }

    /**
     * Test generating bracket with 8 teams.
     */
    public function testGenerateBracketEightTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(8);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $this->cup->generateBracket($cup['id'], $teamIds, '2025-08-01');

        $rounds = $this->cup->getRounds($cup['id']);
        $this->assertCount(3, $rounds); // Quarter + Semi + Final
        $this->assertEquals('Quarter-Final', $rounds[0]['name']);
        $this->assertEquals('Semi-Final', $rounds[1]['name']);
        $this->assertEquals('Final', $rounds[2]['name']);
    }

    /**
     * Test generating bracket with non-power-of-2 teams.
     */
    public function testGenerateBracketNonPowerOfTwo(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(5);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $result = $this->cup->generateBracket($cup['id'], $teamIds, '2025-08-01');

        $this->assertTrue($result);

        $rounds = $this->cup->getRounds($cup['id']);
        $this->assertGreaterThan(0, count($rounds));
    }

    /**
     * Test generating bracket with insufficient teams.
     */
    public function testGenerateBracketInsufficientTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(1);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
        ]);

        $result = $this->cup->generateBracket($cup['id'], $teamIds, '2025-08-01');

        $this->assertFalse($result);
    }

    /**
     * Test getting rounds.
     */
    public function testGetRounds(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);

        $this->assertGreaterThan(0, count($rounds));
        foreach ($rounds as $round) {
            $this->assertArrayHasKey('id', $round);
            $this->assertArrayHasKey('name', $round);
            $this->assertArrayHasKey('fixtures', $round);
        }
    }

    /**
     * Test updating fixture result.
     */
    public function testUpdateFixtureResult(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $fixtureId = $rounds[0]['fixtures'][0]['id'];

        $result = $this->cup->updateFixtureResult($cup['id'], $fixtureId, [
            'homeScore' => 2,
            'awayScore' => 1,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
            'extraTime' => false,
            'penalties' => false,
        ]);

        $this->assertTrue($result);

        $fixture = $this->cup->getFixture($cup['id'], $fixtureId);
        $this->assertNotNull($fixture['result']);
        $this->assertEquals(2, $fixture['result']['homeScore']);
        $this->assertEquals(1, $fixture['result']['awayScore']);
    }

    /**
     * Test updating fixture result with extra time.
     */
    public function testUpdateFixtureResultWithExtraTime(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $fixtureId = $rounds[0]['fixtures'][0]['id'];

        $result = $this->cup->updateFixtureResult($cup['id'], $fixtureId, [
            'homeScore' => 1,
            'awayScore' => 1,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
            'extraTime' => true,
            'homeScoreET' => 2,
            'awayScoreET' => 1,
            'penalties' => false,
        ]);

        $this->assertTrue($result);

        $fixture = $this->cup->getFixture($cup['id'], $fixtureId);
        $this->assertTrue($fixture['result']['extraTime']);
        $this->assertEquals(2, $fixture['result']['homeScoreET']);
    }

    /**
     * Test updating fixture result with penalties.
     */
    public function testUpdateFixtureResultWithPenalties(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $fixtureId = $rounds[0]['fixtures'][0]['id'];

        $result = $this->cup->updateFixtureResult($cup['id'], $fixtureId, [
            'homeScore' => 1,
            'awayScore' => 1,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
            'extraTime' => true,
            'homeScoreET' => 1,
            'awayScoreET' => 1,
            'penalties' => true,
            'homePens' => 5,
            'awayPens' => 4,
        ]);

        $this->assertTrue($result);

        $fixture = $this->cup->getFixture($cup['id'], $fixtureId);
        $this->assertTrue($fixture['result']['penalties']);
        $this->assertEquals(5, $fixture['result']['homePens']);
        $this->assertEquals(4, $fixture['result']['awayPens']);
    }

    /**
     * Test updating fixture date and time.
     */
    public function testUpdateFixtureDateTime(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $fixtureId = $rounds[0]['fixtures'][0]['id'];

        $result = $this->cup->updateFixtureDateTime(
            $cup['id'],
            $fixtureId,
            '2025-09-01',
            '19:00'
        );

        $this->assertTrue($result);

        $fixture = $this->cup->getFixture($cup['id'], $fixtureId);
        $this->assertEquals('2025-09-01', $fixture['date']);
        $this->assertEquals('19:00:00', $fixture['time']);
    }

    /**
     * Test rescheduling unplayed fixtures.
     */
    public function testRescheduleUnplayed(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $result = $this->cup->rescheduleUnplayed(
            $cup['id'],
            '2025-09-01',
            'fortnightly',
            '19:00'
        );

        $this->assertTrue($result);
    }

    /**
     * Test getting cups by season ID.
     */
    public function testGetBySeasonId(): void
    {
        $seasonId = $this->createTestSeason();

        $this->cup->create(['season_id' => $seasonId, 'name' => 'Cup 1']);
        $this->cup->create(['season_id' => $seasonId, 'name' => 'Cup 2']);

        $cups = $this->cup->getBySeasonId($seasonId);

        $this->assertCount(2, $cups);
    }

    /**
     * Test unique slug generation.
     */
    public function testUniqueSlugGeneration(): void
    {
        $seasonId = $this->createTestSeason();

        $cup1 = $this->cup->create(['season_id' => $seasonId, 'name' => 'FA Cup']);
        $cup2 = $this->cup->create(['season_id' => $seasonId, 'name' => 'FA Cup']);

        $this->assertEquals('fa-cup', $cup1['slug']);
        $this->assertEquals('fa-cup-1', $cup2['slug']);
    }

    /**
     * Test finding by slug.
     */
    public function testFindWhere(): void
    {
        $seasonId = $this->createTestSeason();
        $this->cup->create(['season_id' => $seasonId, 'name' => 'FA Cup']);

        $found = $this->cup->findWhere('slug', 'fa-cup');

        $this->assertNotNull($found);
        $this->assertEquals('FA Cup', $found['name']);
        $this->assertArrayHasKey('teamIds', $found);
        $this->assertArrayHasKey('rounds', $found);
    }

    /**
     * Test getting fixture by ID.
     */
    public function testGetFixture(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $fixtureId = $rounds[0]['fixtures'][0]['id'];

        $fixture = $this->cup->getFixture($cup['id'], $fixtureId);

        $this->assertNotNull($fixture);
        $this->assertEquals($fixtureId, $fixture['id']);
        $this->assertArrayHasKey('roundName', $fixture);
    }

    /**
     * Test all method includes teams and rounds.
     */
    public function testAllIncludesRelations(): void
    {
        $seasonId = $this->createTestSeason();
        $this->cup->create(['season_id' => $seasonId, 'name' => 'Cup 1']);

        $all = $this->cup->all();

        $this->assertCount(1, $all);
        $this->assertArrayHasKey('teamIds', $all[0]);
        $this->assertArrayHasKey('rounds', $all[0]);
    }

    /**
     * Test frequency options.
     */
    public function testFrequencyOptions(): void
    {
        $seasonId = $this->createTestSeason();

        $weekly = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'Weekly Cup',
            'frequency' => 'weekly',
        ]);

        $fortnightly = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'Fortnightly Cup',
            'frequency' => 'fortnightly',
        ]);

        $monthly = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'Monthly Cup',
            'frequency' => 'monthly',
        ]);

        $this->assertEquals('weekly', $weekly['frequency']);
        $this->assertEquals('fortnightly', $fortnightly['frequency']);
        $this->assertEquals('monthly', $monthly['frequency']);
    }

    /**
     * Test bracket structure for 2 teams.
     */
    public function testBracketStructureTwoTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $cup = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'FA Cup',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds,
        ]);

        $rounds = $this->cup->getRounds($cup['id']);
        $this->assertCount(1, $rounds); // Only Final
        $this->assertEquals('Final', $rounds[0]['name']);
        $this->assertCount(1, $rounds[0]['fixtures']);
    }

    /**
     * Test round names are correct for different team sizes.
     */
    public function testRoundNames(): void
    {
        $seasonId = $this->createTestSeason();

        // 4 teams -> Semi + Final
        $teamIds4 = $this->createTestTeams(4);
        $cup4 = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'Cup 4',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds4,
        ]);
        $rounds4 = $this->cup->getRounds($cup4['id']);
        $this->assertEquals('Semi-Final', $rounds4[0]['name']);
        $this->assertEquals('Final', $rounds4[1]['name']);

        // Clean for next test
        $this->cleanDatabase();
        $seasonId = $this->createTestSeason();

        // 8 teams -> Quarter + Semi + Final
        $teamIds8 = $this->createTestTeams(8);
        $cup8 = $this->cup->create([
            'season_id' => $seasonId,
            'name' => 'Cup 8',
            'start_date' => '2025-08-01',
            'team_ids' => $teamIds8,
        ]);
        $rounds8 = $this->cup->getRounds($cup8['id']);
        $this->assertEquals('Quarter-Final', $rounds8[0]['name']);
        $this->assertEquals('Semi-Final', $rounds8[1]['name']);
        $this->assertEquals('Final', $rounds8[2]['name']);
    }
}
