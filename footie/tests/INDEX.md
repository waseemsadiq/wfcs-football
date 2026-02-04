# Test Suite Index

Quick reference for all test files and their coverage.

## Quick Stats

- **Total Test Files**: 6
- **Total Tests**: 144
- **Pass Rate**: 100%
- **Execution Time**: ~40ms

## Test Files

### Core Tests (30 tests)

#### `Core/DatabaseTest.php` (8 tests)
Tests database connection, singleton pattern, and basic operations.

**Coverage:**
- PDO instance creation and singleton pattern
- Database connection and table queries
- PDO attribute configuration
- CRUD operations via prepared statements
- Transaction support (commit/rollback)

**Functions Tested:**
- `Database::getInstance()`
- `Database::disconnect()`
- PDO query execution
- Prepared statements
- Transactions

---

#### `Core/ModelTest.php` (22 tests)
Tests base Model class functionality shared by all models.

**Coverage:**
- Slug generation and uniqueness
- Snake case to camel case transformation
- Timestamp management (created_at, updated_at)
- CRUD operations (create, find, update, delete)
- Query methods (all, where, findWhere, count, exists)

**Functions Tested:**
- `Model::slugify()`
- `Model::generateUniqueSlug()`
- `Model::transformKeys()`
- `Model::transformRows()`
- `Model::create()`
- `Model::find()`
- `Model::update()`
- `Model::delete()`
- `Model::all()`
- `Model::where()`
- `Model::findWhere()`
- `Model::count()`
- `Model::exists()`

---

### Model Tests (114 tests)

#### `Models/SeasonTest.php` (28 tests)
Tests Season model for managing football seasons.

**Coverage:**
- Basic CRUD operations
- Custom ID creation (createWithId)
- Active season management (only one active)
- League and cup associations
- Sorting by start date
- Unique slug generation

**Functions Tested:**
- `Season::create()`
- `Season::createWithId()`
- `Season::find()`
- `Season::update()`
- `Season::delete()`
- `Season::getActive()`
- `Season::setActive()`
- `Season::allSorted()`
- `Season::getLeagues()`
- `Season::getCups()`
- `Season::idExists()`
- `Season::exists()`
- `Season::all()`
- `Season::count()`
- `Season::where()`
- `Season::findWhere()`

---

#### `Models/TeamTest.php` (30 tests)
Tests Team model for managing teams and players.

**Coverage:**
- Basic CRUD operations
- Player management (add, update, remove)
- Player text parsing (one per line)
- Team search (case-insensitive)
- Alphabetical sorting
- Unique slug generation

**Functions Tested:**
- `Team::create()`
- `Team::find()`
- `Team::update()`
- `Team::delete()`
- `Team::all()`
- `Team::allSorted()`
- `Team::search()`
- `Team::getPlayers()`
- `Team::setPlayers()`
- `Team::parsePlayersFromText()`
- `Team::playersToText()`
- `Team::playerCount()`
- `Team::count()`
- `Team::exists()`
- `Team::findWhere()`

---

#### `Models/LeagueTest.php` (27 tests)
Tests League model for managing league competitions.

**Coverage:**
- Basic CRUD operations
- Team associations (HasTeams trait)
- Round-robin fixture generation
- Unplayed fixture deletion
- Standings calculation (points, goal diff, form)
- Fixture result updates
- Frequency options (weekly/fortnightly/monthly)

**Functions Tested:**
- `League::create()`
- `League::find()`
- `League::update()`
- `League::delete()`
- `League::all()`
- `League::getTeamIds()`
- `League::setTeams()`
- `League::addTeam()`
- `League::removeTeam()`
- `League::hasTeam()`
- `League::getTeamsCount()`
- `League::generateFixtures()`
- `League::getFixtures()`
- `League::getFixturesCount()`
- `League::deleteUnplayedFixtures()`
- `League::calculateStandings()`
- `League::updateFixtureResult()`
- `League::updateFixtureDateTime()`
- `League::getFixture()`
- `League::getBySeasonId()`
- `League::findWhere()`

---

#### `Models/CupTest.php` (29 tests)
Tests Cup model for managing knockout cup competitions.

**Coverage:**
- Basic CRUD operations
- Team associations (HasTeams trait)
- Single-elimination bracket generation
- Round name generation (Final, Semi, Quarter, etc.)
- Winner advancement to next round
- Extra time and penalty support
- Fixture rescheduling

**Functions Tested:**
- `Cup::create()`
- `Cup::find()`
- `Cup::update()`
- `Cup::delete()`
- `Cup::all()`
- `Cup::getTeamIds()`
- `Cup::setTeams()`
- `Cup::addTeam()`
- `Cup::removeTeam()`
- `Cup::hasTeam()`
- `Cup::getTeamsCount()`
- `Cup::generateBracket()`
- `Cup::getRounds()`
- `Cup::updateFixtureResult()`
- `Cup::updateFixtureDateTime()`
- `Cup::rescheduleUnplayed()`
- `Cup::getFixture()`
- `Cup::getBySeasonId()`
- `Cup::findWhere()`

---

## Running Tests

### All Tests
```bash
./galvani footie/tests/run_tests.php
```

### By Category
Tests are organized by namespace, making it easy to run specific groups:

- **Core Tests**: `Tests\Core\*`
- **Model Tests**: `Tests\Models\*`

## Test Coverage Matrix

| Class | Functions | Tests | Coverage |
|-------|-----------|-------|----------|
| Database | 3 | 8 | 100% |
| Model (base) | 13 | 22 | 100% |
| Season | 16 | 28 | 100% |
| Team | 15 | 30 | 100% |
| League | 21 | 27 | 100% |
| Cup | 19 | 29 | 100% |
| **TOTAL** | **87** | **144** | **100%** |

## Test Types

### Unit Tests (144)
All tests are unit tests that test individual functions in isolation.

### Integration Points Tested
While these are unit tests, they do test integration between:
- Models and Database
- Models and PDO
- Parent and child classes
- Traits and classes

## Assertions Used

Each test file uses these assertion methods from `TestCase`:

- `assertTrue/assertFalse` - Boolean validation
- `assertEquals/assertNotEqual` - Value comparison
- `assertNull/assertNotNull` - Null checks
- `assertEmpty/assertNotEmpty` - Empty checks
- `assertCount` - Array/collection size
- `assertArrayHasKey/assertArrayNotHasKey` - Key existence
- `assertStringContains` - Substring matching
- `assertGreaterThan/assertLessThan` - Numeric comparison
- `assertArrayEquals` - Order-independent array comparison
- `assertInstanceOf` - Type checking
- `assertSame` - Identity checking
- `assertContains` - Array membership
- `assertIsArray` - Type validation

## Files

```
tests/
├── INDEX.md                    # This file
├── README.md                   # Full documentation
├── TEST_SUMMARY.md            # Test results and analysis
├── bootstrap.php              # Autoloader
├── TestCase.php               # Base test class
├── run_tests.php             # Test runner
├── Core/
│   ├── DatabaseTest.php      # 8 tests
│   └── ModelTest.php         # 22 tests
└── Models/
    ├── SeasonTest.php        # 28 tests
    ├── TeamTest.php          # 30 tests
    ├── LeagueTest.php        # 27 tests
    └── CupTest.php           # 29 tests
```

## Documentation

- **README.md** - Detailed test documentation, setup instructions
- **TEST_SUMMARY.md** - Test results, coverage breakdown, metrics
- **INDEX.md** - This file, quick reference

## Related Files

### Application Code Tested
```
footie/
├── core/
│   ├── Database.php           # Tested by Core/DatabaseTest.php
│   └── Model.php              # Tested by Core/ModelTest.php
└── app/
    └── Models/
        ├── Season.php         # Tested by Models/SeasonTest.php
        ├── Team.php           # Tested by Models/TeamTest.php
        ├── League.php         # Tested by Models/LeagueTest.php
        ├── Cup.php            # Tested by Models/CupTest.php
        └── Traits/
            └── HasTeams.php   # Tested via League/Cup tests
```

### Database Schema
```
footie/data/mysql/           # MySQL data directory
../sample-content.sql        # Schema and sample data
```

## Quick Links

- [Run Tests](#running-tests)
- [Core Tests](#core-tests-30-tests)
- [Model Tests](#model-tests-114-tests)
- [Coverage Matrix](#test-coverage-matrix)
- [Documentation](#documentation)
