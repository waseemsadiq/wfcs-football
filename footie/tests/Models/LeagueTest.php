<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\League;
use App\Models\Team;
use Tests\TestCase;

/**
 * Comprehensive test cases for League model.
 * Tests all CRUD operations, fixtures, and standings calculation.
 */
class LeagueTest extends TestCase
{
    private League $league;
    private Team $team;

    protected function setup(): void
    {
        parent::setup();
        $this->league = new League();
        $this->team = new Team();
    }

    /**
     * Test creating a league.
     */
    public function testCreate(): void
    {
        $seasonId = $this->createTestSeason();

        $result = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'frequency' => 'weekly',
            'match_time' => '15:00',
        ]);

        $this->assertNotNull($result['id']);
        $this->assertEquals('Premier League', $result['name']);
        $this->assertEquals('premier-league', $result['slug']);
        $this->assertEquals('weekly', $result['frequency']);
        $this->assertEquals('15:00:00', $result['matchTime']);
        $this->assertArrayHasKey('createdAt', $result);
    }

    /**
     * Test creating a league with teams.
     */
    public function testCreateWithTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $result = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->assertNotNull($result['id']);
        $teamIdsFromLeague = $this->league->getTeamIds($result['id']);
        $this->assertCount(4, $teamIdsFromLeague);
    }

    /**
     * Test finding a league.
     */
    public function testFind(): void
    {
        $seasonId = $this->createTestSeason();
        $created = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
        ]);

        $found = $this->league->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('Premier League', $found['name']);
        $this->assertArrayHasKey('teamIds', $found);
        $this->assertArrayHasKey('fixtures', $found);
    }

    /**
     * Test updating a league.
     */
    public function testUpdate(): void
    {
        $seasonId = $this->createTestSeason();
        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
        ]);

        $result = $this->league->update($league['id'], [
            'name' => 'Championship',
            'frequency' => 'fortnightly',
        ]);

        $this->assertTrue($result);

        $updated = $this->league->find($league['id']);
        $this->assertEquals('Championship', $updated['name']);
        $this->assertEquals('championship', $updated['slug']);
        $this->assertEquals('fortnightly', $updated['frequency']);
    }

    /**
     * Test deleting a league.
     */
    public function testDelete(): void
    {
        $seasonId = $this->createTestSeason();
        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
        ]);

        $result = $this->league->delete($league['id']);

        $this->assertTrue($result);
        $this->assertNull($this->league->find($league['id']));
    }

    /**
     * Test getting team IDs.
     */
    public function testGetTeamIds(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $retrievedIds = $this->league->getTeamIds($league['id']);

        $this->assertCount(4, $retrievedIds);
        $this->assertArrayEquals($teamIds, $retrievedIds);
    }

    /**
     * Test setting teams.
     */
    public function testSetTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
        ]);

        $teamIds = $this->createTestTeams(4);
        $this->league->setTeams($league['id'], $teamIds);

        $retrievedIds = $this->league->getTeamIds($league['id']);
        $this->assertCount(4, $retrievedIds);
    }

    /**
     * Test adding a single team.
     */
    public function testAddTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => [$teamIds[0]],
        ]);

        $this->league->addTeam($league['id'], $teamIds[1]);

        $retrievedIds = $this->league->getTeamIds($league['id']);
        $this->assertCount(2, $retrievedIds);
    }

    /**
     * Test removing a team.
     */
    public function testRemoveTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(3);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->removeTeam($league['id'], $teamIds[1]);

        $retrievedIds = $this->league->getTeamIds($league['id']);
        $this->assertCount(2, $retrievedIds);
    }

    /**
     * Test checking if league has a team.
     */
    public function testHasTeam(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => [$teamIds[0]],
        ]);

        $this->assertTrue($this->league->hasTeam($league['id'], $teamIds[0]));
        $this->assertFalse($this->league->hasTeam($league['id'], $teamIds[1]));
    }

    /**
     * Test getting teams count.
     */
    public function testGetTeamsCount(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(5);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $count = $this->league->getTeamsCount($league['id']);
        $this->assertEquals(5, $count);
    }

    /**
     * Test generating fixtures.
     */
    public function testGenerateFixtures(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $result = $this->league->generateFixtures(
            $league['id'],
            $teamIds,
            '2025-08-01',
            'weekly',
            '15:00'
        );

        $this->assertTrue($result);

        $fixtures = $this->league->getFixtures($league['id']);
        $this->assertGreaterThan(0, count($fixtures));

        // 4 teams = 6 fixtures per half (round-robin) = 12 total
        $this->assertEquals(12, count($fixtures));
    }

    /**
     * Test generating fixtures with odd number of teams.
     */
    public function testGenerateFixturesOddTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(5);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $result = $this->league->generateFixtures(
            $league['id'],
            $teamIds,
            '2025-08-01',
            'weekly',
            '15:00'
        );

        $this->assertTrue($result);

        $fixtures = $this->league->getFixtures($league['id']);
        // 5 teams with bye = 10 fixtures per half = 20 total
        $this->assertEquals(20, count($fixtures));
    }

    /**
     * Test generating fixtures with insufficient teams.
     */
    public function testGenerateFixturesInsufficientTeams(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(1);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
        ]);

        $result = $this->league->generateFixtures(
            $league['id'],
            $teamIds,
            '2025-08-01',
            'weekly',
            '15:00'
        );

        $this->assertFalse($result);
    }

    /**
     * Test getting fixtures count.
     */
    public function testGetFixturesCount(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');

        $count = $this->league->getFixturesCount($league['id']);
        $this->assertEquals(12, $count);
    }

    /**
     * Test deleting unplayed fixtures.
     */
    public function testDeleteUnplayedFixtures(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');

        $result = $this->league->deleteUnplayedFixtures($league['id']);
        $this->assertTrue($result);

        $count = $this->league->getFixturesCount($league['id']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test calculating standings.
     */
    public function testCalculateStandings(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(4);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $teams = $this->team->all();
        $standings = $this->league->calculateStandings($league['id'], $teams);

        $this->assertCount(4, $standings);
        foreach ($standings as $row) {
            $this->assertArrayHasKey('teamId', $row);
            $this->assertArrayHasKey('teamName', $row);
            $this->assertArrayHasKey('played', $row);
            $this->assertArrayHasKey('won', $row);
            $this->assertArrayHasKey('drawn', $row);
            $this->assertArrayHasKey('lost', $row);
            $this->assertArrayHasKey('goalsFor', $row);
            $this->assertArrayHasKey('goalsAgainst', $row);
            $this->assertArrayHasKey('goalDifference', $row);
            $this->assertArrayHasKey('points', $row);
            $this->assertArrayHasKey('form', $row);
        }
    }

    /**
     * Test updating fixture result.
     */
    public function testUpdateFixtureResult(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');
        $fixtures = $this->league->getFixtures($league['id']);
        $fixtureId = $fixtures[0]['id'];

        $result = $this->league->updateFixtureResult($league['id'], $fixtureId, [
            'homeScore' => 2,
            'awayScore' => 1,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
        ]);

        $this->assertTrue($result);

        $updated = $this->league->getFixture($league['id'], $fixtureId);
        $this->assertNotNull($updated['result']);
        $this->assertEquals(2, $updated['result']['homeScore']);
        $this->assertEquals(1, $updated['result']['awayScore']);
    }

    /**
     * Test updating fixture date and time.
     */
    public function testUpdateFixtureDateTime(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');
        $fixtures = $this->league->getFixtures($league['id']);
        $fixtureId = $fixtures[0]['id'];

        $result = $this->league->updateFixtureDateTime(
            $league['id'],
            $fixtureId,
            '2025-09-01',
            '19:00'
        );

        $this->assertTrue($result);

        $updated = $this->league->getFixture($league['id'], $fixtureId);
        $this->assertEquals('2025-09-01', $updated['date']);
        $this->assertEquals('19:00:00', $updated['time']);
    }

    /**
     * Test getting leagues by season ID.
     */
    public function testGetBySeasonId(): void
    {
        $seasonId = $this->createTestSeason();

        $this->league->create(['season_id' => $seasonId, 'name' => 'League 1']);
        $this->league->create(['season_id' => $seasonId, 'name' => 'League 2']);

        $leagues = $this->league->getBySeasonId($seasonId);

        $this->assertCount(2, $leagues);
    }

    /**
     * Test unique slug generation.
     */
    public function testUniqueSlugGeneration(): void
    {
        $seasonId = $this->createTestSeason();

        $league1 = $this->league->create(['season_id' => $seasonId, 'name' => 'Premier League']);
        $league2 = $this->league->create(['season_id' => $seasonId, 'name' => 'Premier League']);

        $this->assertEquals('premier-league', $league1['slug']);
        $this->assertEquals('premier-league-1', $league2['slug']);
    }

    /**
     * Test finding by slug.
     */
    public function testFindWhere(): void
    {
        $seasonId = $this->createTestSeason();
        $this->league->create(['season_id' => $seasonId, 'name' => 'Premier League']);

        $found = $this->league->findWhere('slug', 'premier-league');

        $this->assertNotNull($found);
        $this->assertEquals('Premier League', $found['name']);
        $this->assertArrayHasKey('teamIds', $found);
        $this->assertArrayHasKey('fixtures', $found);
    }

    /**
     * Test standings calculation with results.
     */
    public function testStandingsWithResults(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');
        $fixtures = $this->league->getFixtures($league['id']);

        // Add a result
        $this->league->updateFixtureResult($league['id'], $fixtures[0]['id'], [
            'homeScore' => 3,
            'awayScore' => 1,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
        ]);

        $teams = $this->team->all();
        $standings = $this->league->calculateStandings($league['id'], $teams);

        $this->assertEquals(1, $standings[0]['played']);
        $this->assertEquals(3, $standings[0]['points']);
        $this->assertEquals(1, $standings[0]['won']);
    }

    /**
     * Test standings are sorted correctly.
     */
    public function testStandingsSorting(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(3);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');
        $fixtures = $this->league->getFixtures($league['id']);

        // Team 0 wins
        $this->league->updateFixtureResult($league['id'], $fixtures[0]['id'], [
            'homeScore' => 3,
            'awayScore' => 0,
            'homeScorers' => '',
            'awayScorers' => '',
            'homeCards' => '',
            'awayCards' => '',
        ]);

        $teams = $this->team->all();
        $standings = $this->league->calculateStandings($league['id'], $teams);

        // Winner should be first
        $this->assertGreaterThan(0, $standings[0]['points']);
    }

    /**
     * Test getting fixture by ID.
     */
    public function testGetFixture(): void
    {
        $seasonId = $this->createTestSeason();
        $teamIds = $this->createTestTeams(2);

        $league = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Premier League',
            'team_ids' => $teamIds,
        ]);

        $this->league->generateFixtures($league['id'], $teamIds, '2025-08-01');
        $fixtures = $this->league->getFixtures($league['id']);
        $fixtureId = $fixtures[0]['id'];

        $fixture = $this->league->getFixture($league['id'], $fixtureId);

        $this->assertNotNull($fixture);
        $this->assertEquals($fixtureId, $fixture['id']);
        $this->assertArrayHasKey('homeTeamId', $fixture);
        $this->assertArrayHasKey('awayTeamId', $fixture);
    }

    /**
     * Test all method includes teams and fixtures.
     */
    public function testAllIncludesRelations(): void
    {
        $seasonId = $this->createTestSeason();
        $this->league->create(['season_id' => $seasonId, 'name' => 'League 1']);

        $all = $this->league->all();

        $this->assertCount(1, $all);
        $this->assertArrayHasKey('teamIds', $all[0]);
        $this->assertArrayHasKey('fixtures', $all[0]);
    }

    /**
     * Test frequency options.
     */
    public function testFrequencyOptions(): void
    {
        $seasonId = $this->createTestSeason();

        $weekly = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Weekly League',
            'frequency' => 'weekly',
        ]);

        $fortnightly = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Fortnightly League',
            'frequency' => 'fortnightly',
        ]);

        $monthly = $this->league->create([
            'season_id' => $seasonId,
            'name' => 'Monthly League',
            'frequency' => 'monthly',
        ]);

        $this->assertEquals('weekly', $weekly['frequency']);
        $this->assertEquals('fortnightly', $fortnightly['frequency']);
        $this->assertEquals('monthly', $monthly['frequency']);
    }
}
