# Footie CI Test Suite

Comprehensive test suite for the Footie football management application. Tests cover all major components: Seasons, Leagues, Cups, Teams, and Fixtures.

## Overview

- **58 total tests**
- **5 test classes** covering all core models
- **100% pass rate**
- Built using custom PHP test framework (no external dependencies)
- Includes setup/teardown with isolated test data
- Clear, descriptive assertion helpers
- Console output with color formatting and progress tracking

## Test Coverage

### Seasons (11 tests)
- Creating seasons with custom ID format (e.g., "2024-25")
- Active season management
- League and cup associations
- Season slug generation
- Sorting by start date
- ID existence checks

### Teams (13 tests)
- Team creation with optional player rosters
- Team updates with slug regeneration
- Player text parsing and conversion
- Sorting teams alphabetically
- Case-insensitive search
- Player counting
- Basic CRUD operations

### Leagues (13 tests)
- Round-robin fixture generation
- Fixture scheduling with configurable frequency (weekly, fortnightly, monthly)
- Handling odd number of teams with byes
- Match time configuration
- Each team plays each other twice (home and away)
- Fixture date and time management
- Basic CRUD operations

### Cups (12 tests)
- Single-elimination bracket generation
- Handling power-of-2 and non-power-of-2 team counts
- Automatic bye distribution
- Round names (Final, Semi-Final, etc.)
- Match scheduling
- Basic CRUD operations

### Fixtures (9 tests)
- Fixture structure validation
- No team plays itself
- Unique fixture IDs
- Date and time format validation
- League vs cup fixture differences
- Result initialization (null initially)

## Running Tests

### Via npm
```bash
npm test
```

### Direct PHP
```bash
php tests/run_tests.php
```

## Test Structure

Each test class follows this pattern:

```
Tests/Models/
├── SeasonTest.php       # Season model tests
├── TeamTest.php         # Team model tests
├── LeagueTest.php       # League model tests
├── CupTest.php          # Cup model tests
└── FixtureTest.php      # Fixture handling tests
```

## Test Framework

Custom lightweight PHP test framework with:
- **TestCase base class** - Provides setup/teardown and assertion helpers
- **Assertion methods**:
  - `assertTrue()`, `assertFalse()`
  - `assertEquals()`, `assertNotEqual()`
  - `assertNull()`, `assertNotNull()`
  - `assertArrayHasKey()`, `assertArrayNotHasKey()`
  - `assertCount()`
  - `assertStringContains()`
  - `assertNotEmpty()`
- **Test runner** - Orchestrates test execution and reporting
- **Color output** - Visual feedback for pass/fail

## Design Principles

### Test Isolation
- Each test uses isolated temporary data directories
- No shared state between tests
- Automatic cleanup after each test class

### Real Code Testing
- Tests use actual model implementations
- Mock objects avoided unless necessary
- Tests verify actual behavior, not implementation details

### Descriptive Names
- Test names describe the behavior being tested
- Clear assertion messages on failure
- Easy to understand at a glance what failed

### Comprehensive Coverage
- Happy path and edge cases
- Data validation
- Relationships between entities
- Error conditions

## CI/CD Integration

The test suite is designed for CI/CD pipelines:

```bash
npm test
# Exit code 0 on success
# Exit code 1 on failure
```

Tests complete in milliseconds and require no external services or database.

## Development Workflow

When adding new functionality:

1. Write failing test first (RED)
2. Implement minimal code to pass test (GREEN)
3. Refactor if needed (REFACTOR)
4. Run full suite: `npm test`

Example:
```bash
# 1. RED - Test fails
npm test  # ✗ Test failed

# 2. GREEN - Implement feature
# ... add code ...
npm test  # ✓ All tests passed

# 3. REFACTOR - Clean up if needed
npm test  # ✓ All tests passed
```

## Test Data Management

Tests use temporary directories for isolation:
```
/tmp/footie-tests-{unique-id}/
├── seasons/
├── leagues/
├── cups/
└── teams/
```

Data is automatically cleaned up after each test class completes.

## Assertions Quick Reference

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEqual($unexpected, $actual);

// Nullability
$this->assertNull($value);
$this->assertNotNull($value);

// Arrays
$this->assertArrayHasKey('key', $array);
$this->assertArrayNotHasKey('key', $array);
$this->assertCount(5, $array);

// Truthiness
$this->assertTrue($condition);
$this->assertFalse($condition);

// Strings
$this->assertStringContains('substring', $string);

// Emptiness
$this->assertNotEmpty($value);

// Custom failure
$this->fail('Custom message');
```

## Performance

- Full test suite runs in < 1 second
- No database or external service dependencies
- Minimal I/O (temp files)
- Suitable for pre-commit hooks

## Future Enhancements

Potential additions for expanded coverage:
- Controller endpoint tests
- View rendering tests
- Integration tests
- Performance benchmarks
- Code coverage reporting
