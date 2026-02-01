<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\League;
use App\Models\Cup;
use Tests\TestCase;

/**
 * Test cases for Fixture handling in League and Cup.
 */
class FixtureTest extends TestCase
{
    private League $league;
    private Cup $cup;

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

        $this->cup = new class($this->testDataPath) extends Cup {
            private string $dataPath;

            public function __construct(string $dataPath)
            {
                $this->dataPath = $dataPath . '/cups';
                if (!is_dir($this->dataPath)) {
                    mkdir($this->dataPath, 0755, true);
                }
                parent::__construct();
            }

            protected function getDataPath(): string
            {
                return $this->dataPath . '/cups.json';
            }
        };
    }

    /**
     * Test fixture has no result initially.
     */
    public function testFixtureHasNoResultInitially(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2', 'team-3'], '2024-08-01');

        foreach ($fixtures as $fixture) {
            $this->assertNull($fixture['result']);
        }
    }

    /**
     * Test fixture has unique IDs.
     */
    public function testFixturesHaveUniqueIds(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2', 'team-3', 'team-4'], '2024-08-01');

        $ids = array_map(fn($f) => $f['id'], $fixtures);
        $uniqueIds = array_unique($ids);

        $this->assertCount(count($fixtures), $uniqueIds);
    }

    /**
     * Test no team plays itself.
     */
    public function testTeamDoesNotPlayItself(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2', 'team-3', 'team-4'], '2024-08-01');

        foreach ($fixtures as $fixture) {
            $this->assertNotEqual($fixture['homeTeamId'], $fixture['awayTeamId']);
        }
    }

    /**
     * Test fixture has both teams specified.
     */
    public function testFixtureBothTeamsSpecified(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2'], '2024-08-01');

        foreach ($fixtures as $fixture) {
            $this->assertNotNull($fixture['homeTeamId']);
            $this->assertNotNull($fixture['awayTeamId']);
        }
    }

    /**
     * Test league fixtures structure.
     */
    public function testLeagueFixturesStructure(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3'];
        $fixtures = $this->league->generateFixtures($teamIds, '2024-08-01');

        $this->assertNotEmpty($fixtures);

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
     * Test cup bracket fixtures structure.
     */
    public function testCupBracketFixturesStructure(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $bracket = $this->cup->generateBracket($teamIds, '2024-08-01');

        foreach ($bracket as $round) {
            foreach ($round['fixtures'] as $fixture) {
                $this->assertArrayHasKey('id', $fixture);
                $this->assertArrayHasKey('homeTeamId', $fixture);
                $this->assertArrayHasKey('awayTeamId', $fixture);
                $this->assertArrayHasKey('date', $fixture);
                $this->assertArrayHasKey('time', $fixture);
                $this->assertArrayHasKey('result', $fixture);
            }
        }
    }

    /**
     * Test fixture ID format.
     */
    public function testFixtureIdFormat(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2'], '2024-08-01');

        foreach ($fixtures as $fixture) {
            // ID should be non-empty string
            $this->assertNotEmpty($fixture['id']);
            $this->assertTrue(is_string($fixture['id']));
        }
    }

    /**
     * Test fixture date format.
     */
    public function testFixtureDateFormat(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2', 'team-3'], '2024-08-01');

        foreach ($fixtures as $fixture) {
            // Date should match YYYY-MM-DD format
            $this->assertTrue(preg_match('/^\d{4}-\d{2}-\d{2}$/', $fixture['date']) === 1);
        }
    }

    /**
     * Test fixture time format.
     */
    public function testFixtureTimeFormat(): void
    {
        $fixtures = $this->league->generateFixtures(['team-1', 'team-2'], '2024-08-01', 'weekly', '15:30');

        foreach ($fixtures as $fixture) {
            // Time should match HH:MM format
            $this->assertTrue(preg_match('/^\d{2}:\d{2}$/', $fixture['time']) === 1);
        }
    }
}
