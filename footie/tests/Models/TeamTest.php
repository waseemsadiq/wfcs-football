<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Team;
use Tests\TestCase;

/**
 * Comprehensive test cases for Team model.
 * Tests all CRUD operations and team-specific functionality.
 */
class TeamTest extends TestCase
{
    private Team $team;

    protected function setup(): void
    {
        parent::setup();
        $this->team = new Team();
    }

    /**
     * Test creating a team without players.
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
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);
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
        $this->assertEquals('Jane Smith', $result['players'][1]);
        $this->assertEquals('Bob Johnson', $result['players'][2]);
    }

    /**
     * Test slug auto-generation from name.
     */
    public function testSlugAutoGeneration(): void
    {
        $result = $this->team->create([
            'name' => 'Arsenal FC',
            'colour' => '#FF0000',
        ]);

        $this->assertEquals('arsenal-fc', $result['slug']);
    }

    /**
     * Test unique slug generation.
     */
    public function testUniqueSlugGeneration(): void
    {
        $this->team->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $result = $this->team->create(['name' => 'Arsenal', 'colour' => '#0000FF']);

        $this->assertEquals('arsenal-1', $result['slug']);
    }

    /**
     * Test finding a team by ID.
     */
    public function testFind(): void
    {
        $created = $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);

        $found = $this->team->find($created['id']);

        $this->assertNotNull($found);
        $this->assertEquals('Manchester United', $found['name']);
        $this->assertEquals([], $found['players']);
    }

    /**
     * Test finding a non-existent team returns null.
     */
    public function testFindNonExistent(): void
    {
        $result = $this->team->find(99999);
        $this->assertNull($result);
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
     * Test updating a non-existent team returns false.
     */
    public function testUpdateNonExistent(): void
    {
        $result = $this->team->update(99999, ['name' => 'Test']);
        $this->assertFalse($result);
    }

    /**
     * Test updating a team with players.
     */
    public function testUpdateWithPlayers(): void
    {
        $team = $this->team->create([
            'name' => 'Arsenal',
            'colour' => '#FF0000',
            'players' => ['Player 1', 'Player 2']
        ]);

        $result = $this->team->update($team['id'], [
            'players' => ['New Player 1', 'New Player 2', 'New Player 3']
        ]);

        $this->assertTrue($result);

        $updated = $this->team->find($team['id']);
        $this->assertCount(3, $updated['players']);
        $this->assertEquals('New Player 1', $updated['players'][0]);
    }

    /**
     * Test deleting a team.
     */
    public function testDelete(): void
    {
        $team = $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);
        $teamId = $team['id'];

        $result = $this->team->delete($teamId);

        $this->assertTrue($result);
        $this->assertNull($this->team->find($teamId));
    }

    /**
     * Test deleting a non-existent team returns false.
     */
    public function testDeleteNonExistent(): void
    {
        $result = $this->team->delete(99999);
        $this->assertFalse($result);
    }

    /**
     * Test getting all teams.
     */
    public function testAll(): void
    {
        $this->team->create(['name' => 'Team 1', 'colour' => '#FF0000']);
        $this->team->create(['name' => 'Team 2', 'colour' => '#00FF00']);
        $this->team->create(['name' => 'Team 3', 'colour' => '#0000FF']);

        $teams = $this->team->all();

        $this->assertCount(3, $teams);
        $this->assertArrayHasKey('players', $teams[0]);
    }

    /**
     * Test getting all teams sorted by name.
     */
    public function testAllSorted(): void
    {
        $this->team->create(['name' => 'Zebra United', 'colour' => '#FF0000']);
        $this->team->create(['name' => 'Apple City', 'colour' => '#00FF00']);
        $this->team->create(['name' => 'Manchester United', 'colour' => '#0000FF']);

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
        $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);
        $this->team->create(['name' => 'Manchester City', 'colour' => '#00FF00']);
        $this->team->create(['name' => 'Liverpool FC', 'colour' => '#0000FF']);

        $results = $this->team->search('manchester');

        $this->assertCount(2, $results);
    }

    /**
     * Test search is case insensitive.
     */
    public function testSearchIsCaseInsensitive(): void
    {
        $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);
        $this->team->create(['name' => 'Liverpool FC', 'colour' => '#00FF00']);

        $results = $this->team->search('MANCHESTER');

        $this->assertCount(1, $results);
        $this->assertEquals('Manchester United', $results[0]['name']);
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
        $this->assertEquals('Jane Smith', $players[1]);
        $this->assertEquals('Bob Johnson', $players[2]);
    }

    /**
     * Test parsing empty text returns empty array.
     */
    public function testParsePlayersFromEmptyText(): void
    {
        $players = $this->team->parsePlayersFromText("");
        $this->assertEmpty($players);
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
     * Test getting player count for a team.
     */
    public function testPlayerCount(): void
    {
        $team = $this->team->create([
            'name' => 'Manchester United',
            'colour' => '#FF0000',
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
        $team = $this->team->create(['name' => 'Manchester United', 'colour' => '#FF0000']);

        $count = $this->team->playerCount($team);

        $this->assertEquals(0, $count);
    }

    /**
     * Test counting all teams.
     */
    public function testCount(): void
    {
        $this->team->create(['name' => 'Team 1', 'colour' => '#FF0000']);
        $this->team->create(['name' => 'Team 2', 'colour' => '#00FF00']);
        $this->team->create(['name' => 'Team 3', 'colour' => '#0000FF']);

        $count = $this->team->count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test checking if a team exists.
     */
    public function testExists(): void
    {
        $team = $this->team->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $this->assertTrue($this->team->exists($team['id']));
        $this->assertFalse($this->team->exists(99999));
    }

    /**
     * Test finding by slug.
     */
    public function testFindWhere(): void
    {
        $this->team->create(['name' => 'Arsenal FC', 'colour' => '#FF0000']);

        $found = $this->team->findWhere('slug', 'arsenal-fc');

        $this->assertNotNull($found);
        $this->assertEquals('Arsenal FC', $found['name']);
        $this->assertArrayHasKey('players', $found);
    }

    /**
     * Test setting players replaces existing players.
     */
    public function testSetPlayersReplacesExisting(): void
    {
        $team = $this->team->create([
            'name' => 'Arsenal',
            'colour' => '#FF0000',
            'players' => ['Player 1', 'Player 2']
        ]);

        $this->team->setPlayers($team['id'], ['New Player 1']);

        $updated = $this->team->find($team['id']);
        $this->assertCount(1, $updated['players']);
        $this->assertEquals('New Player 1', $updated['players'][0]);
    }

    /**
     * Test getting players for a team.
     */
    public function testGetPlayers(): void
    {
        $team = $this->team->create([
            'name' => 'Arsenal',
            'colour' => '#FF0000',
            'players' => ['Player 1', 'Player 2', 'Player 3']
        ]);

        $players = $this->team->getPlayers($team['id']);

        $this->assertCount(3, $players);
        $this->assertEquals('Player 1', $players[0]);
    }

    /**
     * Test players are sorted by name.
     */
    public function testPlayersSortedByName(): void
    {
        $team = $this->team->create([
            'name' => 'Arsenal',
            'colour' => '#FF0000',
            'players' => ['Zoe', 'Alice', 'Bob']
        ]);

        $players = $this->team->getPlayers($team['id']);

        $this->assertEquals('Alice', $players[0]);
        $this->assertEquals('Bob', $players[1]);
        $this->assertEquals('Zoe', $players[2]);
    }

    /**
     * Test creating team with empty players array.
     */
    public function testCreateWithEmptyPlayers(): void
    {
        $team = $this->team->create([
            'name' => 'Arsenal',
            'colour' => '#FF0000',
            'players' => []
        ]);

        $this->assertCount(0, $team['players']);
    }

    /**
     * Test slug is unique across different teams.
     */
    public function testSlugUniqueness(): void
    {
        $team1 = $this->team->create(['name' => 'FC Barcelona', 'colour' => '#FF0000']);
        $team2 = $this->team->create(['name' => 'FC Barcelona', 'colour' => '#00FF00']);
        $team3 = $this->team->create(['name' => 'FC Barcelona', 'colour' => '#0000FF']);

        $this->assertEquals('fc-barcelona', $team1['slug']);
        $this->assertEquals('fc-barcelona-1', $team2['slug']);
        $this->assertEquals('fc-barcelona-2', $team3['slug']);
    }

    /**
     * Test update preserves existing slug if name doesn't change.
     */
    public function testUpdatePreservesSlugWhenNameUnchanged(): void
    {
        $team = $this->team->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $originalSlug = $team['slug'];

        $this->team->update($team['id'], ['colour' => '#0000FF']);

        $updated = $this->team->find($team['id']);
        $this->assertEquals($originalSlug, $updated['slug']);
    }
}
