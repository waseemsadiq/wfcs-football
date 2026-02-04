<?php

declare(strict_types=1);

namespace Tests\Core;

use Tests\TestCase;
use Core\Model;

/**
 * Test cases for the base Model class.
 * Tests core CRUD operations and transformations.
 */
class ModelTest extends TestCase
{
    private Model $model;

    protected function setup(): void
    {
        parent::setup();

        // Create an anonymous class extending Model for testing
        $this->model = new class extends Model {
            protected function getTableName(): string
            {
                return 'teams';
            }
        };
    }

    /**
     * Test slugify method.
     */
    public function testSlugify(): void
    {
        $slug = Model::slugify('Manchester United FC');
        $this->assertEquals('manchester-united-fc', $slug);
    }

    /**
     * Test slugify handles special characters.
     */
    public function testSlugifySpecialCharacters(): void
    {
        $slug = Model::slugify('FC Barcelona (2024)!');
        $this->assertEquals('fc-barcelona-2024', $slug);
    }

    /**
     * Test slugify handles multiple spaces.
     */
    public function testSlugifyMultipleSpaces(): void
    {
        $slug = Model::slugify('Manchester   United');
        $this->assertEquals('manchester-united', $slug);
    }

    /**
     * Test slugify handles leading/trailing spaces.
     */
    public function testSlugifyTrimming(): void
    {
        $slug = Model::slugify('  Arsenal FC  ');
        $this->assertEquals('arsenal-fc', $slug);
    }

    /**
     * Test generateUniqueSlug creates unique slugs.
     */
    public function testGenerateUniqueSlug(): void
    {
        $team1 = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $team2 = $this->model->create(['name' => 'Arsenal', 'colour' => '#00FF00']);
        $team3 = $this->model->create(['name' => 'Arsenal', 'colour' => '#0000FF']);

        $this->assertEquals('arsenal', $team1['slug']);
        $this->assertEquals('arsenal-1', $team2['slug']);
        $this->assertEquals('arsenal-2', $team3['slug']);
    }

    /**
     * Test generateUniqueSlug with excludeId on update.
     */
    public function testGenerateUniqueSlugWithExclude(): void
    {
        $team1 = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        // Updating same team with same name should keep slug
        $slug = $this->model->generateUniqueSlug('Arsenal', $team1['id']);
        $this->assertEquals('arsenal', $slug);
    }

    /**
     * Test transformKeys converts snake_case to camelCase.
     */
    public function testTransformKeys(): void
    {
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('transformKeys');
        $method->setAccessible(true);

        $input = [
            'team_name' => 'Arsenal',
            'created_at' => '2025-01-01',
            'is_active' => 1,
            'home_score' => 3,
        ];

        $result = $method->invoke($this->model, $input);

        $this->assertArrayHasKey('teamName', $result);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('isActive', $result);
        $this->assertArrayHasKey('homeScore', $result);
    }

    /**
     * Test create adds timestamps.
     */
    public function testCreateAddsTimestamps(): void
    {
        $team = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $this->assertArrayHasKey('createdAt', $team);
        $this->assertArrayHasKey('updatedAt', $team);
        $this->assertNotNull($team['createdAt']);
        $this->assertNotNull($team['updatedAt']);
    }

    /**
     * Test update adds updated timestamp.
     */
    public function testUpdateAddsTimestamp(): void
    {
        $team = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $originalUpdated = $team['updatedAt'];

        // Small delay to ensure timestamp difference
        usleep(100000); // 0.1 seconds

        $this->model->update($team['id'], ['colour' => '#0000FF']);

        $updated = $this->model->find($team['id']);
        $this->assertNotEqual($originalUpdated, $updated['updatedAt']);
    }

    /**
     * Test find returns null for non-existent record.
     */
    public function testFindNonExistent(): void
    {
        $result = $this->model->find(99999);
        $this->assertNull($result);
    }

    /**
     * Test update returns false for non-existent record.
     */
    public function testUpdateNonExistent(): void
    {
        $result = $this->model->update(99999, ['name' => 'Test']);
        $this->assertFalse($result);
    }

    /**
     * Test delete returns false for non-existent record.
     */
    public function testDeleteNonExistent(): void
    {
        $result = $this->model->delete(99999);
        $this->assertFalse($result);
    }

    /**
     * Test all returns all records.
     */
    public function testAll(): void
    {
        $this->model->create(['name' => 'Team 1', 'colour' => '#FF0000']);
        $this->model->create(['name' => 'Team 2', 'colour' => '#00FF00']);
        $this->model->create(['name' => 'Team 3', 'colour' => '#0000FF']);

        $all = $this->model->all();

        $this->assertCount(3, $all);
    }

    /**
     * Test count returns correct count.
     */
    public function testCount(): void
    {
        $this->model->create(['name' => 'Team 1', 'colour' => '#FF0000']);
        $this->model->create(['name' => 'Team 2', 'colour' => '#00FF00']);

        $count = $this->model->count();

        $this->assertEquals(2, $count);
    }

    /**
     * Test where filters correctly.
     */
    public function testWhere(): void
    {
        $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $this->model->create(['name' => 'Chelsea', 'colour' => '#0000FF']);

        $results = $this->model->where('colour', '#FF0000');

        $this->assertCount(1, $results);
        $this->assertEquals('Arsenal', $results[0]['name']);
    }

    /**
     * Test findWhere returns first match.
     */
    public function testFindWhere(): void
    {
        $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $this->model->create(['name' => 'Manchester United', 'colour' => '#FF0000']);

        $result = $this->model->findWhere('colour', '#FF0000');

        $this->assertNotNull($result);
        $this->assertEquals('#FF0000', $result['colour']);
    }

    /**
     * Test findWhere returns null when no match.
     */
    public function testFindWhereNoMatch(): void
    {
        $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $result = $this->model->findWhere('colour', '#00FF00');

        $this->assertNull($result);
    }

    /**
     * Test findWhere handles camelCase field names.
     */
    public function testFindWhereCamelCase(): void
    {
        $team = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $result = $this->model->findWhere('createdAt', $team['createdAt']);

        $this->assertNotNull($result);
    }

    /**
     * Test exists method.
     */
    public function testExists(): void
    {
        $team = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $this->assertTrue($this->model->exists($team['id']));
        $this->assertFalse($this->model->exists(99999));
    }

    /**
     * Test create returns complete record.
     */
    public function testCreateReturnsCompleteRecord(): void
    {
        $result = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('colour', $result);
        $this->assertArrayHasKey('slug', $result);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);
    }

    /**
     * Test update only modifies specified fields.
     */
    public function testUpdateOnlyModifiesSpecifiedFields(): void
    {
        $team = $this->model->create(['name' => 'Arsenal', 'colour' => '#FF0000']);
        $originalName = $team['name'];

        $this->model->update($team['id'], ['colour' => '#0000FF']);

        $updated = $this->model->find($team['id']);
        $this->assertEquals($originalName, $updated['name']);
        $this->assertEquals('#0000FF', $updated['colour']);
    }

    /**
     * Test transformRows transforms multiple records.
     */
    public function testTransformRows(): void
    {
        $this->model->create(['name' => 'Team 1', 'colour' => '#FF0000']);
        $this->model->create(['name' => 'Team 2', 'colour' => '#00FF00']);

        $all = $this->model->all();

        foreach ($all as $record) {
            $this->assertArrayHasKey('createdAt', $record);
            $this->assertArrayNotHasKey('created_at', $record);
        }
    }
}
