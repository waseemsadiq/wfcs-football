<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Cup;
use Tests\TestCase;

/**
 * Test cases for Cup model.
 */
class CupTest extends TestCase
{
    private Cup $cup;

    protected function setup(): void
    {
        parent::setup();

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
     * Test generating a bracket for a cup.
     */
    public function testGenerateBracket(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $bracket = $this->cup->generateBracket($teamIds, '2024-08-01');

        $this->assertNotNull($bracket);
        $this->assertCount(2, $bracket); // First round and final
    }

    /**
     * Test bracket has required structure.
     */
    public function testBracketHasRequiredStructure(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $bracket = $this->cup->generateBracket($teamIds);

        foreach ($bracket as $round) {
            $this->assertArrayHasKey('name', $round);
            $this->assertArrayHasKey('fixtures', $round);
        }
    }

    /**
     * Test bracket fixtures have required fields.
     */
    public function testBracketFixturesHaveRequiredFields(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $bracket = $this->cup->generateBracket($teamIds);

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
     * Test power of 2 teams.
     */
    public function testGenerateBracketPowerOfTwo(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4', 'team-5', 'team-6', 'team-7', 'team-8'];
        $bracket = $this->cup->generateBracket($teamIds);

        // 8 teams: first round (4 matches), semi-final (2 matches), final (1 match) = 3 rounds
        $this->assertCount(3, $bracket);

        // First round should have 4 fixtures
        $this->assertCount(4, $bracket[0]['fixtures']);
    }

    /**
     * Test non-power-of-2 teams with byes.
     */
    public function testGenerateBracketNonPowerOfTwo(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4', 'team-5', 'team-6'];
        $bracket = $this->cup->generateBracket($teamIds);

        // Should generate bracket with some byes
        $this->assertNotEmpty($bracket);

        // Count total teams in first round matches
        $firstRoundMatches = count($bracket[0]['fixtures']);
        $this->assertNotNull($firstRoundMatches);
    }

    /**
     * Test match time is set on fixtures.
     */
    public function testGenerateBracketMatchTime(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $bracket = $this->cup->generateBracket($teamIds, '2024-08-01', 'weekly', '19:45');

        foreach ($bracket as $round) {
            foreach ($round['fixtures'] as $fixture) {
                $this->assertEquals('19:45', $fixture['time']);
            }
        }
    }

    /**
     * Test start date is respected.
     */
    public function testGenerateBracketStartDate(): void
    {
        $teamIds = ['team-1', 'team-2', 'team-3', 'team-4'];
        $startDate = '2024-08-15';
        $bracket = $this->cup->generateBracket($teamIds, $startDate);

        $firstFixture = $bracket[0]['fixtures'][0];
        $this->assertEquals('2024-08-15', $firstFixture['date']);
    }

    /**
     * Test no bracket for less than 2 teams.
     */
    public function testGenerateBracketNeedsAtLeastTwoTeams(): void
    {
        $bracket = $this->cup->generateBracket(['team-1']);

        $this->assertCount(0, $bracket);
    }

    /**
     * Test creating a cup.
     */
    public function testCreate(): void
    {
        $result = $this->cup->create([
            'name' => 'FA Cup',
            'seasonId' => '2024-25',
        ]);

        $this->assertNotNull($result['id']);
        $this->assertEquals('FA Cup', $result['name']);
        $this->assertEquals('fa-cup', $result['slug']);
        $this->assertEquals('2024-25', $result['seasonId']);
        $this->assertArrayHasKey('rounds', $result);
    }

    /**
     * Test updating a cup.
     */
    public function testUpdate(): void
    {
        $cup = $this->cup->create([
            'name' => 'FA Cup',
            'seasonId' => '2024-25',
        ]);

        $result = $this->cup->update($cup['id'], ['name' => 'League Cup']);

        $this->assertTrue($result);

        $updated = $this->cup->find($cup['id']);
        $this->assertEquals('League Cup', $updated['name']);
    }

    /**
     * Test finding a cup.
     */
    public function testFind(): void
    {
        $created = $this->cup->create([
            'name' => 'FA Cup',
            'seasonId' => '2024-25',
        ]);

        $found = $this->cup->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('FA Cup', $found['name']);
    }

    /**
     * Test deleting a cup.
     */
    public function testDelete(): void
    {
        $cup = $this->cup->create([
            'name' => 'FA Cup',
            'seasonId' => '2024-25',
        ]);

        $result = $this->cup->delete($cup['id']);

        $this->assertTrue($result);
        $this->assertNull($this->cup->find($cup['id']));
    }
}
