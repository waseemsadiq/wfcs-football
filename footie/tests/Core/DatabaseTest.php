<?php

declare(strict_types=1);

namespace Tests\Core;

use Tests\TestCase;
use Core\Database;
use PDO;

/**
 * Test cases for the Database class.
 * Tests connection and singleton pattern.
 */
class DatabaseTest extends TestCase
{
    /**
     * Test getInstance returns a PDO instance.
     */
    public function testGetInstanceReturnsPDO(): void
    {
        $db = Database::getInstance();
        $this->assertInstanceOf(PDO::class, $db);
    }

    /**
     * Test singleton pattern - same instance returned.
     */
    public function testSingletonPattern(): void
    {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();

        $this->assertSame($db1, $db2);
    }

    /**
     * Test connection works - can execute queries.
     */
    public function testConnectionWorks(): void
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT 1 as test');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, $result['test']);
    }

    /**
     * Test can query database tables.
     */
    public function testCanQueryTables(): void
    {
        $db = Database::getInstance();
        $stmt = $db->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->assertContains('teams', $tables);
        $this->assertContains('seasons', $tables);
        $this->assertContains('leagues', $tables);
        $this->assertContains('cups', $tables);
    }

    /**
     * Test PDO attributes are set correctly.
     */
    public function testPDOAttributes(): void
    {
        $db = Database::getInstance();

        $this->assertEquals(
            PDO::ERRMODE_EXCEPTION,
            $db->getAttribute(PDO::ATTR_ERRMODE)
        );

        $this->assertEquals(
            PDO::FETCH_ASSOC,
            $db->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE)
        );
    }

    /**
     * Test can perform CRUD operations.
     */
    public function testCRUDOperations(): void
    {
        $db = Database::getInstance();

        // Create
        $stmt = $db->prepare('INSERT INTO teams (name, slug, colour, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $stmt->execute(['Test Team', 'test-team', '#FF0000']);
        $id = $db->lastInsertId();
        $this->assertGreaterThan(0, $id);

        // Read
        $stmt = $db->prepare('SELECT * FROM teams WHERE id = ?');
        $stmt->execute([$id]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('Test Team', $team['name']);

        // Update
        $stmt = $db->prepare('UPDATE teams SET colour = ? WHERE id = ?');
        $stmt->execute(['#0000FF', $id]);
        $stmt = $db->prepare('SELECT colour FROM teams WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('#0000FF', $result['colour']);

        // Delete
        $stmt = $db->prepare('DELETE FROM teams WHERE id = ?');
        $stmt->execute([$id]);
        $stmt = $db->prepare('SELECT * FROM teams WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);
    }

    /**
     * Test transactions work.
     */
    public function testTransactions(): void
    {
        $db = Database::getInstance();

        $db->beginTransaction();

        $stmt = $db->prepare('INSERT INTO teams (name, slug, colour, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $stmt->execute(['Transaction Test', 'transaction-test', '#FF0000']);
        $id = $db->lastInsertId();

        $db->rollBack();

        // Should not exist after rollback
        $stmt = $db->prepare('SELECT * FROM teams WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);
    }

    /**
     * Test prepared statements work correctly.
     */
    public function testPreparedStatements(): void
    {
        $db = Database::getInstance();

        $stmt = $db->prepare('INSERT INTO teams (name, slug, colour, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $stmt->execute(['Team 1', 'team-1', '#FF0000']);
        $id1 = $db->lastInsertId();

        $stmt->execute(['Team 2', 'team-2', '#00FF00']);
        $id2 = $db->lastInsertId();

        $this->assertNotEqual($id1, $id2);

        // Cleanup
        $db->prepare('DELETE FROM teams WHERE id IN (?, ?)')->execute([$id1, $id2]);
    }
}
