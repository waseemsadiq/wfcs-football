<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Team;
use Tests\TestCase;

/**
 * Test cases for Team model.
 */
class TeamTest extends TestCase
{
    private Team $team;

    protected function setup(): void
    {
        parent::setup();

        $this->team = new class($this->testDataPath) extends Team {
            private string $dataPath;

            public function __construct(string $dataPath)
            {
                $this->dataPath = $dataPath . '/teams';
                if (!is_dir($this->dataPath)) {
                    mkdir($this->dataPath, 0755, true);
                }
                parent::__construct();
            }

            protected function getDataPath(): string
            {
                return $this->dataPath . '/teams.json';
            }
        };
    }

    /**
     * Test creating a team.
     */
    public function testCreate(): void
    {
        $result = $this->team->create([
            'name' => 'Manchester United',
            'colour' => '#FF0000',
        ]);

        $this->assertNotNull($result['id']);
        $this->assertEquals('Manchester United', $result['name']);
        $this->assertEquals('manchester-united', $result['slug']);
        $this->assertEquals('#FF0000', $result['colour']);
        $this->assertEquals([], $result['players']);
        $this->assertArrayHasKey('created_at', $result);
    }

    /**
     * Test creating a team with players.
     */
    public function testCreateWithPlayers(): void
    {
        $players = ['John Doe', 'Jane Smith', 'Bob Johnson'];

        $result = $this->team->create([
            'name' => 'Manchester United',
            'colour' => '#FF0000',
            'players' => $players,
        ]);

        $this->assertCount(3, $result['players']);
        $this->assertEquals('John Doe', $result['players'][0]);
    }

    /**
     * Test updating a team.
     */
    public function testUpdate(): void
    {
        $team = $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);
        $teamId = $team['id'];

        $result = $this->team->update($teamId, [
            'name' => 'Manchester City',
            'colour' => '#0066FF',
        ]);

        $this->assertTrue($result);

        $updated = $this->team->find($teamId);
        $this->assertEquals('Manchester City', $updated['name']);
        $this->assertEquals('manchester-city', $updated['slug']);
        $this->assertEquals('#0066FF', $updated['colour']);
    }

    /**
     * Test parsing players from text.
     */
    public function testParsePlayersFromText(): void
    {
        $text = "John Doe\nJane Smith\nBob Johnson";

        $players = $this->team->parsePlayersFromText($text);

        $this->assertCount(3, $players);
        $this->assertEquals('John Doe', $players[0]);
        $this->assertEquals('Jane Smith', $players[1]);
        $this->assertEquals('Bob Johnson', $players[2]);
    }

    /**
     * Test parsing players handles whitespace.
     */
    public function testParsePlayersFromTextHandlesWhitespace(): void
    {
        $text = "  John Doe  \n\nJane Smith  \n\n\nBob Johnson";

        $players = $this->team->parsePlayersFromText($text);

        $this->assertCount(3, $players);
        $this->assertEquals('John Doe', $players[0]);
    }

    /**
     * Test converting players to text.
     */
    public function testPlayersToText(): void
    {
        $players = ['John Doe', 'Jane Smith', 'Bob Johnson'];

        $text = $this->team->playersToText($players);

        $this->assertStringContains('John Doe', $text);
        $this->assertStringContains('Jane Smith', $text);
        $this->assertStringContains('Bob Johnson', $text);
    }

    /**
     * Test getting all teams sorted by name.
     */
    public function testAllSorted(): void
    {
        $this->team->create(['name' => 'Zebra United']);
        $this->team->create(['name' => 'Apple City']);
        $this->team->create(['name' => 'Manchester United']);

        $sorted = $this->team->allSorted();

        $this->assertEquals('Apple City', $sorted[0]['name']);
        $this->assertEquals('Manchester United', $sorted[1]['name']);
        $this->assertEquals('Zebra United', $sorted[2]['name']);
    }

    /**
     * Test searching teams by name.
     */
    public function testSearch(): void
    {
        $this->team->create(['name' => 'Manchester United']);
        $this->team->create(['name' => 'Manchester City']);
        $this->team->create(['name' => 'Liverpool FC']);

        $results = $this->team->search('manchester');

        $this->assertCount(2, $results);
    }

    /**
     * Test search is case insensitive.
     */
    public function testSearchIsCaseInsensitive(): void
    {
        $this->team->create(['name' => 'Manchester United']);
        $this->team->create(['name' => 'Liverpool FC']);

        $results = $this->team->search('MANCHESTER');

        $this->assertCount(1, $results);
        $this->assertEquals('Manchester United', $results[0]['name'] ?? null);
    }

    /**
     * Test getting player count for a team.
     */
    public function testPlayerCount(): void
    {
        $team = $this->team->create([
            'name' => 'Manchester United',
            'players' => ['John', 'Jane', 'Bob'],
        ]);

        $count = $this->team->playerCount($team);

        $this->assertEquals(3, $count);
    }

    /**
     * Test player count for team with no players.
     */
    public function testPlayerCountEmpty(): void
    {
        $team = $this->team->create(['name' => 'Manchester United']);

        $count = $this->team->playerCount($team);

        $this->assertEquals(0, $count);
    }

    /**
     * Test finding a team.
     */
    public function testFind(): void
    {
        $created = $this->team->create(['name' => 'Manchester United']);

        $found = $this->team->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('Manchester United', $found['name']);
    }

    /**
     * Test deleting a team.
     */
    public function testDelete(): void
    {
        $team = $this->team->create(['name' => 'Manchester United']);
        $teamId = $team['id'];

        $result = $this->team->delete($teamId);

        $this->assertTrue($result);
        $this->assertNull($this->team->find($teamId));
    }
}
