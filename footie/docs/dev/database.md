# Database Schema

This document describes the database schema, relationships, and data models used in WFCS Football.

## Database Technology

- **Engine**: MySQL (embedded via Galvani)
- **Connection**: Unix socket (`data/mysql.sock`)
- **Charset**: utf8mb4
- **Collation**: utf8mb3_general_ci (tables), utf8mb4 (connection)
- **Storage Engine**: InnoDB
- **Transactions**: Supported via PDO

## Connection Configuration

```php
// config/database.php
[
    'database' => 'wfcs',
    'username' => 'root',
    'password' => '',
    'socket' => dirname(__DIR__) . '/../data/mysql.sock',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true
    ]
]
```

## Entity Relationship Diagram

```
┌─────────────┐
│   seasons   │
└──────┬──────┘
       │
       ├───────┐
       │       │
       ▼       ▼
┌────────┐  ┌─────┐
│ leagues│  │ cups│
└───┬────┘  └──┬──┘
    │          │
    │          ├────────────┐
    │          │            │
    ▼          ▼            ▼
┌────────────┐ ┌────────────┐ ┌────────────┐
│league_teams│ │ cup_teams  │ │ cup_rounds │
└──────┬─────┘ └─────┬──────┘ └──────┬─────┘
       │             │                │
       │             │                │
       ▼             ▼                ▼
┌───────┐     ┌─────────────┐ ┌─────────────┐
│ teams │◄────│cup_fixtures │ │cup_fixtures │
└───┬───┘     └─────────────┘ └─────────────┘
    │
    │         ┌──────────────────┐
    └────────►│league_fixtures   │
              └──────────────────┘
    │
    ▼
┌─────────┐
│ players │
└─────────┘
```

## Tables

### seasons

Stores football seasons (e.g., "2025/26").

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | NO | - | Season name (e.g., "2025/26") |
| slug | VARCHAR(100) | NO | - | URL-safe slug (unique) |
| start_date | DATE | NO | - | Season start date |
| end_date | DATE | NO | - | Season end date |
| is_active | TINYINT(1) | YES | 0 | Whether this is the active season (0 or 1) |
| created_at | DATETIME | NO | - | Creation timestamp |
| updated_at | DATETIME | NO | - | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (slug)

**Relationships**:
- One-to-many with `leagues` (season_id)
- One-to-many with `cups` (season_id)

**Notes**:
- Only one season should have `is_active = 1` at a time
- Slug auto-generated from name
- Deleting a season cascades to leagues and cups

---

### teams

Stores football teams.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | NO | - | Team name |
| slug | VARCHAR(100) | NO | - | URL-safe slug (unique) |
| contact | VARCHAR(100) | YES | NULL | Contact person name |
| phone | VARCHAR(50) | YES | NULL | Contact phone number |
| email | VARCHAR(100) | YES | NULL | Contact email address |
| colour | VARCHAR(7) | YES | #000000 | Team color (hex format) |
| created_at | DATETIME | NO | - | Creation timestamp |
| updated_at | DATETIME | NO | - | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (slug)

**Relationships**:
- One-to-many with `players` (team_id)
- Many-to-many with `leagues` (via league_teams)
- Many-to-many with `cups` (via cup_teams)
- One-to-many with `league_fixtures` (home_team_id, away_team_id)
- One-to-many with `cup_fixtures` (home_team_id, away_team_id)

**Notes**:
- Colour must be valid hex format (#RRGGBB)
- Slug auto-generated from name
- Cannot delete teams that are in competitions

---

### players

Stores player names for teams.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| team_id | INT(11) | NO | - | Foreign key to teams |
| name | VARCHAR(100) | NO | - | Player name |

**Indexes**:
- PRIMARY KEY (id)
- KEY (team_id)

**Foreign Keys**:
- team_id → teams(id) ON DELETE CASCADE

**Relationships**:
- Many-to-one with `teams`

**Notes**:
- Players are simple name strings
- No separate player entity with detailed stats
- Deleting a team cascades to players

---

### leagues

Stores league competitions.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| season_id | INT(11) | YES | NULL | Foreign key to seasons |
| name | VARCHAR(100) | NO | - | League name |
| slug | VARCHAR(100) | NO | - | URL-safe slug (unique) |
| start_date | DATE | YES | NULL | First fixture date |
| frequency | ENUM | YES | weekly | Fixture frequency: weekly, fortnightly, monthly |
| match_time | TIME | YES | 15:00:00 | Default match time |
| created_at | DATETIME | NO | - | Creation timestamp |
| updated_at | DATETIME | NO | - | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (slug)
- KEY (season_id)

**Foreign Keys**:
- season_id → seasons(id) ON DELETE CASCADE

**Relationships**:
- Many-to-one with `seasons`
- Many-to-many with `teams` (via league_teams)
- One-to-many with `league_fixtures`

**Notes**:
- Round-robin format (each team plays each other twice)
- Slug auto-generated from name
- Deleting a league cascades to fixtures and team associations

---

### league_teams

Join table for league-team associations.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| league_id | INT(11) | NO | - | Foreign key to leagues |
| team_id | INT(11) | NO | - | Foreign key to teams |

**Indexes**:
- PRIMARY KEY (league_id, team_id)
- KEY (team_id)

**Foreign Keys**:
- league_id → leagues(id) ON DELETE CASCADE
- team_id → teams(id) ON DELETE CASCADE

**Notes**:
- Composite primary key ensures unique team per league
- Deleting league or team removes association

---

### league_fixtures

Stores league match fixtures and results.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| league_id | INT(11) | NO | - | Foreign key to leagues |
| home_team_id | INT(11) | NO | - | Foreign key to teams (home) |
| away_team_id | INT(11) | NO | - | Foreign key to teams (away) |
| match_date | DATE | YES | NULL | Match date |
| match_time | TIME | YES | NULL | Match time |
| home_score | INT(11) | YES | NULL | Home team score (full time) |
| away_score | INT(11) | YES | NULL | Away team score (full time) |
| home_scorers | TEXT | YES | NULL | Home team scorers (JSON) |
| away_scorers | TEXT | YES | NULL | Away team scorers (JSON) |
| home_cards | TEXT | YES | NULL | Home team cards (JSON) |
| away_cards | TEXT | YES | NULL | Away team cards (JSON) |
| created_at | DATETIME | NO | - | Creation timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (league_id)
- KEY (home_team_id)
- KEY (away_team_id)

**Foreign Keys**:
- league_id → leagues(id) ON DELETE CASCADE
- home_team_id → teams(id)
- away_team_id → teams(id)

**Notes**:
- Scores are NULL until match is played
- Scorers/cards stored as JSON strings
- Ordered by match_date, match_time

---

### cups

Stores cup competitions (knockout tournaments).

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| season_id | INT(11) | YES | NULL | Foreign key to seasons |
| name | VARCHAR(100) | NO | - | Cup name |
| slug | VARCHAR(100) | NO | - | URL-safe slug (unique) |
| start_date | DATE | YES | NULL | First round date |
| frequency | ENUM | YES | weekly | Round frequency: weekly, fortnightly, monthly |
| match_time | TIME | YES | 15:00:00 | Default match time |
| created_at | DATETIME | NO | - | Creation timestamp |
| updated_at | DATETIME | NO | - | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (slug)
- KEY (season_id)

**Foreign Keys**:
- season_id → seasons(id) ON DELETE CASCADE

**Relationships**:
- Many-to-one with `seasons`
- Many-to-many with `teams` (via cup_teams)
- One-to-many with `cup_rounds`
- One-to-many with `cup_fixtures`

**Notes**:
- Single-elimination format
- Slug auto-generated from name
- Deleting a cup cascades to rounds and fixtures

---

### cup_teams

Join table for cup-team associations.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| cup_id | INT(11) | NO | - | Foreign key to cups |
| team_id | INT(11) | NO | - | Foreign key to teams |

**Indexes**:
- PRIMARY KEY (cup_id, team_id)
- KEY (team_id)

**Foreign Keys**:
- cup_id → cups(id) ON DELETE CASCADE
- team_id → teams(id) ON DELETE CASCADE

**Notes**:
- Composite primary key ensures unique team per cup
- Deleting cup or team removes association

---

### cup_rounds

Stores cup tournament rounds (e.g., Semi-Final, Final).

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| cup_id | INT(11) | NO | - | Foreign key to cups |
| name | VARCHAR(50) | NO | - | Round name (e.g., "Final") |
| round_order | INT(11) | NO | - | Order of round (0-indexed) |

**Indexes**:
- PRIMARY KEY (id)
- KEY (cup_id)

**Foreign Keys**:
- cup_id → cups(id) ON DELETE CASCADE

**Relationships**:
- Many-to-one with `cups`
- One-to-many with `cup_fixtures`

**Notes**:
- Round names: "Final", "Semi-Final", "Quarter-Final", "Round of X"
- round_order determines progression
- Deleting a cup cascades to rounds

---

### cup_fixtures

Stores cup match fixtures and results.

**Columns**:

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INT(11) | NO | AUTO_INCREMENT | Primary key |
| cup_id | INT(11) | NO | - | Foreign key to cups |
| round_id | INT(11) | NO | - | Foreign key to cup_rounds |
| home_team_id | INT(11) | YES | NULL | Foreign key to teams (home) |
| away_team_id | INT(11) | YES | NULL | Foreign key to teams (away) |
| match_date | DATE | YES | NULL | Match date |
| match_time | TIME | YES | NULL | Match time |
| home_score | INT(11) | YES | NULL | Home score (90 mins) |
| away_score | INT(11) | YES | NULL | Away score (90 mins) |
| home_scorers | TEXT | YES | NULL | Home scorers (JSON) |
| away_scorers | TEXT | YES | NULL | Away scorers (JSON) |
| home_cards | TEXT | YES | NULL | Home cards (JSON) |
| away_cards | TEXT | YES | NULL | Away cards (JSON) |
| extra_time | TINYINT(1) | YES | 0 | Extra time played (0 or 1) |
| home_score_et | INT(11) | YES | NULL | Home score after ET |
| away_score_et | INT(11) | YES | NULL | Away score after ET |
| penalties | TINYINT(1) | YES | 0 | Penalties taken (0 or 1) |
| home_pens | INT(11) | YES | NULL | Home penalty score |
| away_pens | INT(11) | YES | NULL | Away penalty score |
| winner | ENUM | YES | NULL | Winner: "home" or "away" |
| created_at | DATETIME | NO | - | Creation timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (cup_id)
- KEY (round_id)
- KEY (home_team_id)
- KEY (away_team_id)

**Foreign Keys**:
- cup_id → cups(id) ON DELETE CASCADE
- round_id → cup_rounds(id) ON DELETE CASCADE
- home_team_id → teams(id)
- away_team_id → teams(id)

**Notes**:
- Team IDs can be NULL for TBD fixtures (future rounds)
- Winner determined by: penalties > extra time > 90 mins
- Winner automatically advances to next round
- Scores are NULL until match is played

---

## Data Types

### Key Transformation

Database columns use **snake_case**, but PHP arrays use **camelCase**:

```php
// Database row:
[
  'home_team_id' => 1,
  'match_date' => '2024-01-15',
  'created_at' => '2024-01-01 10:00:00'
]

// Transformed to PHP:
[
  'homeTeamId' => 1,
  'matchDate' => '2024-01-15',
  'createdAt' => '2024-01-01 10:00:00'
]
```

Transformation happens automatically in `Model::transformKeys()`.

### Enum Values

**Frequency** (leagues and cups):
- `weekly` - 7 days between fixtures/rounds
- `fortnightly` - 14 days between fixtures/rounds
- `monthly` - 1 month between fixtures/rounds

**Winner** (cup fixtures):
- `home` - Home team won
- `away` - Away team won
- `NULL` - Match not played or drawn

### JSON Fields

Some fields store JSON strings:

**Scorers** (home_scorers, away_scorers):
```json
"[\"Player 1\", \"Player 2\", \"Player 1\"]"
```

**Cards** (home_cards, away_cards):
```json
"[\"Player 3\", \"Player 4\"]"
```

Format: JSON-encoded array of player names. Empty array stored as `""` or `"[]"`.

### Boolean Fields

Stored as TINYINT(1):
- `0` = false
- `1` = true

Fields:
- `seasons.is_active`
- `cup_fixtures.extra_time`
- `cup_fixtures.penalties`

## Indexes and Performance

### Primary Keys

All tables have AUTO_INCREMENT integer primary keys named `id`.

### Unique Constraints

Slugs are unique across their entity type:
- `seasons.slug`
- `teams.slug`
- `leagues.slug`
- `cups.slug`

### Foreign Key Indexes

All foreign key columns are indexed for join performance:
- `leagues.season_id`
- `cups.season_id`
- `players.team_id`
- `league_fixtures.league_id`
- `league_fixtures.home_team_id`
- `league_fixtures.away_team_id`
- `cup_rounds.cup_id`
- `cup_fixtures.cup_id`
- `cup_fixtures.round_id`
- `cup_fixtures.home_team_id`
- `cup_fixtures.away_team_id`

### Composite Keys

Join tables use composite primary keys:
- `league_teams(league_id, team_id)`
- `cup_teams(cup_id, team_id)`

## Cascading Deletes

### ON DELETE CASCADE

Deleting a parent entity cascades to children:

**Season → Leagues/Cups**:
- Deleting a season deletes all leagues and cups in that season

**League → Fixtures/Teams**:
- Deleting a league deletes all fixtures
- Deleting a league removes team associations

**Cup → Rounds/Fixtures/Teams**:
- Deleting a cup deletes all rounds
- Deleting rounds deletes fixtures in those rounds
- Deleting a cup removes team associations

**Team → Players**:
- Deleting a team deletes all players on that team

### No Cascade (Restrict)

Some foreign keys do NOT cascade:

**Team references in fixtures**:
- `league_fixtures.home_team_id`
- `league_fixtures.away_team_id`
- `cup_fixtures.home_team_id`
- `cup_fixtures.away_team_id`

Deleting a team with fixtures will FAIL. Must remove team from competitions first.

## Queries and Models

### Model Methods

Each model provides common queries:

**CRUD**:
- `all()` - Get all records
- `find($id)` - Find by ID
- `create($data)` - Create record
- `update($id, $data)` - Update record
- `delete($id)` - Delete record

**Queries**:
- `where($field, $value)` - Find all matching
- `findWhere($field, $value)` - Find one matching
- `count()` - Count records
- `exists($id)` - Check if exists

**Slug**:
- `generateUniqueSlug($text, $excludeId)` - Generate unique slug
- `slugify($text)` - Convert text to slug

### Special Model Methods

**Season**:
- `getActive()` - Get active season
- `setActive($id)` - Set active season
- `allSorted()` - Get all seasons sorted by date
- `getLeagues($id)` - Get leagues in season
- `getCups($id)` - Get cups in season

**Team**:
- `getPlayers($teamId)` - Get player names
- `setPlayers($teamId, $players)` - Replace players
- `parsePlayersFromText($text)` - Parse player list
- `playersToText($players)` - Convert to text
- `allSorted()` - Get all teams sorted by name

**League**:
- `getTeamIds($leagueId)` - Get team IDs
- `setTeams($leagueId, $teamIds)` - Replace teams
- `getFixtures($leagueId)` - Get all fixtures
- `generateFixtures(...)` - Generate round-robin schedule
- `calculateStandings($leagueId, $teams)` - Calculate league table
- `updateFixtureResult(...)` - Update match result

**Cup**:
- `getTeamIds($cupId)` - Get team IDs
- `setTeams($cupId, $teamIds)` - Replace teams
- `getRounds($cupId)` - Get rounds with fixtures
- `generateBracket(...)` - Generate tournament bracket
- `updateFixtureResult(...)` - Update result and advance winner
- `advanceWinner(...)` - Move winner to next round

## Sample Queries

### Get Active Season with Competitions

```php
$seasonModel = new Season();
$leagueModel = new League();
$cupModel = new Cup();

$season = $seasonModel->getActive();
$leagues = $leagueModel->getBySeasonId($season['id']);
$cups = $cupModel->getBySeasonId($season['id']);
```

### Get League Standings

```php
$leagueModel = new League();
$teamModel = new Team();

$league = $leagueModel->find($leagueId);
$teams = $teamModel->all();
$standings = $leagueModel->calculateStandings($leagueId, $teams);
```

### Get Upcoming Fixtures

```php
$fixtures = $leagueModel->getFixtures($leagueId);
$upcoming = array_filter($fixtures, function($f) {
    return empty($f['result']) && !empty($f['date']);
});
usort($upcoming, function($a, $b) {
    return strcmp($a['date'], $b['date']);
});
```

### Record Match Result

```php
$leagueModel->updateFixtureResult($leagueId, $fixtureId, [
    'homeScore' => 2,
    'awayScore' => 1,
    'homeScorers' => json_encode(['Player A', 'Player B']),
    'awayScorers' => json_encode(['Player C']),
    'homeCards' => json_encode([]),
    'awayCards' => json_encode(['Player D'])
]);
```

### Get Team's Fixtures

```sql
SELECT * FROM league_fixtures
WHERE home_team_id = ? OR away_team_id = ?
ORDER BY match_date DESC
```

## Database Migrations

The application does not use migrations. Schema is created by:

1. Running `footie-install.php` installer
2. Loading `sample-content.sql` (optional)

To recreate schema:

```bash
# Drop existing database
./galvani -c "DROP DATABASE IF EXISTS wfcs"

# Recreate
./galvani footie-install.php
```

## Backup and Restore

### Backup

```bash
mysqldump -h localhost -u root wfcs > backup.sql
```

### Restore

```bash
mysql -h localhost -u root wfcs < backup.sql
```

### Via Galvani

Use Galvani's MCP tools or query directly via PDO.

## Data Integrity

### Constraints

- Foreign keys enforce referential integrity
- Unique slugs prevent URL collisions
- NOT NULL enforces required fields
- Enum limits valid values
- Composite keys prevent duplicate associations

### Application Logic

Additional validation in models:
- Date ranges (end_date > start_date)
- Minimum teams (2 for leagues/cups)
- Valid hex colors (#RRGGBB)
- Unique slugs (auto-incremented if collision)
- Score validation (non-negative integers)

## Performance Considerations

### Indexing

All foreign keys and slugs are indexed for fast lookups.

### N+1 Prevention

Models load related data eagerly in `all()` and `find()` methods to prevent N+1 queries.

### Query Optimization

- Use WHERE clauses on indexed columns
- JOIN on foreign key columns
- LIMIT for pagination
- ORDER BY on indexed columns when possible

### Connection Pooling

PDO connection is singleton pattern - one connection per request.

## Future Enhancements

Potential database improvements:

1. **Statistics Table**: Separate table for player/team stats
2. **Notifications**: Store user notifications
3. **Audit Log**: Track data changes
4. **Settings**: Store app configuration in DB
5. **Users**: Multiple admin users with permissions
6. **Comments**: Match reports and comments
7. **Media**: Store match photos/videos
8. **Full-text Search**: Search teams, players, competitions
9. **Caching**: Redis/Memcached for standings
10. **Read Replicas**: Separate read/write connections

## Conclusion

The database schema is designed for:
- Clarity: Simple, straightforward relationships
- Integrity: Foreign keys and constraints
- Performance: Proper indexing
- Extensibility: Easy to add new fields/tables
