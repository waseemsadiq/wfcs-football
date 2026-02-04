<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\League;
use Tests\TestCase;

/**
 * Test cases for League model.
 */
class LeagueTest extends TestCase
{
    private League $league;

    protected function setup(): void
    {
        parent::setup();

        $this->league = new class($this->testDataPath) extends League {
            private string $dataPath;

            public function __construct(string $dataPath)
            {
                $this->dataPath = $dataPath . '/leagues';
                if (!is_dir($this->dataPath)) {
                    mkdir($this->dataPath, 0755, true);
                }
                parent::__construct();
            }

            protected function getDataPath(): string
            {
                return $this->dataPath . '/leagues.json';
            }
        };
    }

    /**
     * Test generating round-robin fixtures for a league.
     */
    public function testGenerateFixtures(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $startDate = '2024-08-01';

        $fixtures = $this->league->generateFixtures($teamIds, $startDate);

        // Each team plays each other team home and away
        // With 4 teams: 4 * 3 = 12 fixtures
        $this->assertCount(12, $fixtures);
    }

    /**
     * Test generated fixtures have required fields.
     */
    public function testGeneratedFixturesHaveRequiredFields(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3'];
        $fixtures = $this->league->generateFixtures($teamIds, '2024-08-01');

        foreach ($fixtures as $fixture) {
            $this->assertArrayHasKey('id', $fixture);
            $this->assertArrayHasKey('homeTeamId', $fixture);
            $this->assertArrayHasKey('awayTeamId', $fixture);
            $this->assertArrayHasKey('date', $fixture);
            $this->assertArrayHasKey('time', $fixture);
            $this->assertArrayHasKey('result', $fixture);
        }
    }

    /**
     * Test fixtures with odd number of teams.
     */
    public function testGenerateFixturesWithOddNumberOfTeams(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3'];
        $fixtures = $this->league->generateFixtures($teamIds, '2024-08-01');

        // With 3 teams: (3 * 2) = 6 fixtures (plus byes handled)
        $this->assertCount(6, $fixtures);

        // No fixture should have null as a team
        foreach ($fixtures as $fixture) {
            $this->assertNotNull($fixture['homeTeamId']);
            $this->assertNotNull($fixture['awayTeamId']);
        }
    }

    /**
     * Test fixtures start date is respected.
     */
    public function testGenerateFixturesStartDate(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $startDate = '2024-08-15';

        $fixtures = $this->league->generateFixtures($teamIds, $startDate);

        $this->assertEquals('2024-08-15', $fixtures[0]['date']);
    }

    /**
     * Test weekly frequency increments dates by 7 days.
     */
    public function testGenerateFixturesWeeklyFrequency(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $startDate = '2024-08-01';

        $fixtures = $this->league->generateFixtures($teamIds, $startDate, 'weekly');

        // Dates should increment by 7 days for weekly
        $this->assertEquals('2024-08-01', $fixtures[0]['date']);
        $this->assertEquals('2024-08-08', $fixtures[2]['date']);
    }

    /**
     * Test fortnightly frequency increments dates by 14 days.
     */
    public function testGenerateFixturesFortightlyFrequency(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $startDate = '2024-08-01';

        $fixtures = $this->league->generateFixtures($teamIds, $startDate, 'fortnightly');

        // Dates should increment by 14 days
        $this->assertEquals('2024-08-01', $fixtures[0]['date']);
        $this->assertEquals('2024-08-15', $fixtures[2]['date']);
    }

    /**
     * Test match time is set on fixtures.
     */
    public function testGenerateFixturesMatchTime(): void
    {
        $teamIds = ['team-1', 'team-2'];
        $fixtures = $this->league->generateFixtures($teamIds, '2024-08-01', 'weekly', '19:45');

        foreach ($fixtures as $fixture) {
            $this->assertEquals('19:45', $fixture['time']);
        }
    }

    /**
     * Test no fixtures for less than 2 teams.
     */
    public function testGenerateFixturesNeedsAtLeastTwoTeams(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1'], '2024-08-01');

        $this->assertCount(0, $fixtures);
    }

    /**
     * Test each team plays each other team once in each half.
     */
    public function testEachTeamPlaysEachOtherTwice(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3'];
        $fixtures = $this->league->generateFixtures($teamIds, '2024-08-01');

        // Count matches for team-1
        $team1Matches = 0;
        foreach ($fixtures as $fixture) {
            if ($fixture['homeTeamId'] === 'team-1' || $fixture['awayTeamId'] === 'team-1') {
                $team1Matches++;
            }
        }

        // With 3 teams, each plays 2 * (3-1) = 4 matches
        $this->assertEquals(4, $team1Matches);
    }

    /**
     * Test creating a league.
     */
    public function testCreate(): void
    {
        $result = $this->league->create([
            'name' => 'Premier League',
            'seasonId' => '2024-25',
            'teamIds' => ['team-1', 'team-2'],
        ]);

        $this->assertNotNull($result['id']);
        $this->assertEquals('Premier League', $result['name']);
        $this->assertEquals('premier-league', $result['slug']);
        $this->assertEquals('2024-25', $result['seasonId']);
    }

    /**
     * Test updating a league.
     */
    public function testUpdate(): void
    {
        $league = $this->league->create([
            'name' => 'Premier League',
            'seasonId' => '2024-25',
        ]);

        $result = $this->league->update($league['id'], ['name' => 'Championship']);

        $this->assertTrue($result);

        $updated = $this->league->find($league['id']);
        $this->assertEquals('Championship', $updated['name']);
    }

    /**
     * Test finding a league.
     */
    public function testFind(): void
    {
        $created = $this->league->create([
            'name' => 'Premier League',
            'seasonId' => '2024-25',
        ]);

        $found = $this->league->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('Premier League', $found['name']);
    }

    /**
     * Test deleting a league.
     */
    public function testDelete(): void
    {
        $league = $this->league->create([
            'name' => 'Premier League',
            'seasonId' => '2024-25',
        ]);

        $result = $this->league->delete($league['id']);

        $this->assertTrue($result);
        $this->assertNull($this->league->find($league['id']));
    }
}
