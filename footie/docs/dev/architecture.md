# Architecture

This document describes the system architecture, design patterns, and key design decisions in the WFCS Football application.

## Overview

WFCS Football is built using a custom lightweight MVC framework designed specifically for Galvani. The architecture emphasizes simplicity, maintainability, and type safety.

## Design Principles

### 1. Separation of Concerns

The application follows strict MVC separation:

- **Models**: Data access, business logic, validation
- **Views**: Presentation logic only (no business logic)
- **Controllers**: Request handling, input validation, orchestration

### 2. Type Safety

- All code uses `declare(strict_types=1)`
- Type hints on all method parameters and return types
- Union types where appropriate (e.g., `int|string` for IDs)

### 3. Security First

- Password hashing with bcrypt
- CSRF protection on all forms
- Session security (HttpOnly, SameSite=Strict)
- Rate limiting on login attempts
- Input validation and sanitization
- SQL injection prevention via prepared statements

### 4. Convention Over Configuration

- Automatic slug generation from names
- Predictable URL patterns
- Standard CRUD operations
- Consistent naming conventions

## Architecture Layers

### 1. Entry Point (index.php)

```php
index.php
├── Autoloader registration
├── Environment loading (.env)
├── Configuration loading
├── Session initialization
├── Router creation and configuration
└── Request dispatch
```

**Responsibilities**:
- Bootstrap the application
- Register autoloader for namespaced classes
- Load environment variables from `.env`
- Configure error reporting based on debug mode
- Initialize secure sessions
- Load routes and dispatch requests

### 2. Core Framework

Located in `core/` directory. Custom lightweight framework components:

#### Router (Core\Router)

```php
Router
├── Route registration (get/post)
├── URL pattern matching with parameters
├── Authentication checks
├── Controller dispatch
└── 404 handling
```

**Features**:
- Pattern-based routing with `{param}` syntax
- Protected route support (authentication required)
- Subdirectory installation support
- Clean URL generation

**Example**:
```php
$router->get('/teams/{slug}', 'TeamsController', 'show');
// Matches: /teams/blue-stars
// Calls: TeamsController->show($slug='blue-stars')
```

#### Controller (Core\Controller)

Base controller providing common functionality:

```php
Controller
├── View rendering (with layouts)
├── Redirects (with subdirectory support)
├── Flash messages
├── JSON responses
├── Request helpers (post/get data)
├── Validation helpers
├── CSRF protection
└── Data enrichment helpers
```

**Key Methods**:
- `render($template, $data, $layout)` - Render view with layout
- `redirect($url)` - Redirect with subdirectory awareness
- `flash($type, $message)` - Set flash message for next request
- `json($data, $status)` - Return JSON response
- `validateRequired($fields)` - Check required POST fields
- `validateCsrf()` - Validate CSRF token

#### Model (Core\Model)

Abstract base model with CRUD operations:

```php
Model
├── CRUD operations (create/read/update/delete)
├── Query helpers (where/findWhere/count)
├── Slug generation (unique slugs)
├── Key transformation (snake_case ↔ camelCase)
└── Database access (PDO)
```

**Key Features**:
- Automatic timestamps (created_at, updated_at)
- Snake case to camel case key transformation
- Unique slug generation with collision handling
- Type-safe operations

**Transformation Example**:
```php
// Database: snake_case columns
// PHP: camelCase keys
[
  'team_id' => 1,
  'start_date' => '2024-01-01',
  'created_at' => '2024-01-01 10:00:00'
]
// ↓ Transformed to:
[
  'teamId' => 1,
  'startDate' => '2024-01-01',
  'createdAt' => '2024-01-01 10:00:00'
]
```

#### View (Core\View)

Template rendering engine:

```php
View
├── Template loading
├── Layout wrapping
├── Data extraction
├── Output buffering
└── XSS escaping helpers
```

**Features**:
- Layout support (main, public, auth layouts)
- Data passing via PHP arrays
- Automatic escaping helpers (`e()` function)
- Shared data (e.g., base path for all views)

#### Auth (Core\Auth)

Authentication and session management:

```php
Auth
├── Login attempt (with rate limiting)
├── Session management
├── CSRF token generation/validation
├── Login blocking (failed attempts)
└── Logout
```

**Security Features**:
- Bcrypt password hashing
- Session regeneration on login
- 5 failed attempts = 15-minute lockout
- 2-hour session timeout
- CSRF tokens on all forms

#### Database (Core\Database)

PDO connection singleton:

```php
Database
├── Singleton pattern
├── MySQL socket connection
├── PDO configuration
└── Connection management
```

**Configuration**:
- Connects via Unix socket to Galvani's embedded MySQL
- Charset: utf8mb4
- Error mode: Exceptions
- Fetch mode: Associative arrays

### 3. Application Layer

#### Models (App\Models)

Domain models with business logic:

##### Season
```php
Season
├── Active season management
├── League/Cup associations
├── Sorted retrieval (by date)
└── Unique slug generation
```

**Key Methods**:
- `getActive()` - Get currently active season
- `setActive($id)` - Set a season as active (deactivates others)
- `allSorted()` - Get all seasons with league/cup IDs

##### Team
```php
Team
├── Team CRUD
├── Player management (nested table)
├── Player parsing (text ↔ array)
└── Search functionality
```

**Key Features**:
- Players stored in separate `players` table
- Player management via `getPlayers()` and `setPlayers()`
- Text parsing for player entry (one per line)

##### League
```php
League
├── Team associations (via HasTeams trait)
├── Fixture generation (round-robin)
├── Fixture management
├── Standings calculation
└── Result tracking
```

**Key Features**:
- Round-robin fixture generation with bye handling
- Configurable frequency (weekly/fortnightly/monthly)
- Home/away fixture balancing
- Automatic standings calculation
- Points: 3 for win, 1 for draw, 0 for loss

**Fixture Generation Algorithm**:
1. Handle odd teams by adding BYE
2. Calculate rounds: (n-1) for first half
3. Generate first half fixtures (each team plays each other once)
4. Add gap between halves
5. Generate second half fixtures (reverse home/away)
6. Use round-robin rotation algorithm with fixed first team

##### Cup
```php
Cup
├── Team associations (via HasTeams trait)
├── Bracket generation (single-elimination)
├── Round management
├── Winner advancement
└── Result tracking (with extra time/penalties)
```

**Key Features**:
- Single-elimination tournament brackets
- Automatic round names (Final, Semi-Final, Quarter-Final, etc.)
- Handles non-power-of-2 teams with byes
- Extra time and penalty shoot-out support
- Automatic winner advancement to next round

**Bracket Generation Algorithm**:
1. Shuffle teams for random seeding
2. Calculate rounds needed: ceil(log2(n))
3. Calculate byes: 2^rounds - n
4. Teams with byes skip to round 2
5. Create rounds and placeholder fixtures
6. Schedule matches with configurable frequency

#### HasTeams Trait

Shared functionality for competitions (leagues and cups):

```php
HasTeams Trait
├── getTeamIds($id)
├── setTeams($id, $teamIds)
├── addTeam($id, $teamId)
├── removeTeam($id, $teamId)
├── hasTeam($id, $teamId)
└── getTeamsCount($id)
```

**Join Tables**:
- Leagues: `league_teams` (league_id, team_id)
- Cups: `cup_teams` (cup_id, team_id)

#### Controllers (App\Controllers)

Request handlers organized by feature:

##### AuthController
- Login form display
- Login processing with rate limiting
- Logout

##### DashboardController
- Admin dashboard with active season summary
- AJAX endpoint for upcoming fixtures

##### PublicController
- Homepage with leagues, cups, recent/upcoming fixtures
- League standings and fixtures
- Cup brackets and results
- Team details and fixtures
- Data enrichment for public display

##### SeasonsController
- CRUD operations for seasons
- Active season management
- League/Cup associations

##### TeamsController
- CRUD operations for teams
- Player roster management
- Bulk delete support

##### LeaguesController
- CRUD operations for leagues
- Fixture generation and management
- Result recording
- Standings display

##### CupsController
- CRUD operations for cups
- Bracket generation and management
- Result recording with ET/penalties
- Winner advancement

#### Views (App\Views)

PHP templates organized by feature:

##### Layouts
- `main.php` - Admin layout with navigation
- `public.php` - Public layout with minimal navigation
- `auth.php` - Login page layout

##### Partials
Reusable components:
- `head.php` - HTML head with meta tags
- `footer.php` - Footer content
- `page_header.php` - Page headers
- `standings_table.php` - League standings table
- `public_fixture.php` - Fixture display
- `team_selector.php` - Team multi-select
- `colour_picker.php` - Color picker input
- `regenerate_modal.php` - Fixture regeneration modal
- etc.

## Data Flow

### Request Lifecycle

```
1. Browser Request
   ↓
2. index.php (Entry Point)
   ↓
3. Router (Route Matching)
   ↓
4. Authentication Check (if protected route)
   ↓
5. Controller (Action Method)
   ├→ Model (Data Operations)
   │   └→ Database (SQL Queries)
   └→ View (Template Rendering)
      └→ Layout (Wraps Content)
   ↓
6. Response to Browser
```

### Example: Viewing League Standings

```php
// 1. Request: GET /league/premier-league
// 2. Router matches: PublicController::league($slug='premier-league')
// 3. Controller retrieves data:
$league = $leagueModel->findWhere('slug', $slug);
$teams = $teamModel->all();
$standings = $leagueModel->calculateStandings($league['id'], $teams);

// 4. Controller renders view:
$this->render('public/league', [
  'league' => $league,
  'standings' => $standings,
  'fixtures' => $fixtures
], 'public');

// 5. View renders within public layout
// 6. HTML response sent to browser
```

## Database Design

### Relationships

```
seasons
├─→ leagues (season_id)
│   ├─→ league_teams (league_id, team_id)
│   └─→ league_fixtures (league_id, home_team_id, away_team_id)
└─→ cups (season_id)
    ├─→ cup_teams (cup_id, team_id)
    ├─→ cup_rounds (cup_id)
    └─→ cup_fixtures (cup_id, round_id, home_team_id, away_team_id)

teams
└─→ players (team_id)
```

### Key Design Decisions

#### Why MySQL instead of JSON files?

The original README mentions JSON storage, but the current implementation uses MySQL because:

1. **Relational data**: Teams, fixtures, and results have complex relationships
2. **Integrity**: Foreign keys ensure data consistency
3. **Queries**: Efficient filtering, sorting, and aggregation
4. **Galvani integration**: Embedded MySQL is simple to use
5. **Scalability**: Better performance for larger datasets

#### Fixture Storage

Fixtures are stored as individual rows (not nested JSON) because:

1. **Easy updates**: Change individual fixture dates/results
2. **Queries**: Filter by date, team, result status
3. **Relationships**: Foreign keys to teams and competitions
4. **Integrity**: Constraints prevent invalid data

#### Slug-based URLs

All public URLs use slugs instead of IDs:
- Better SEO
- Human-readable URLs
- Stable URLs even if IDs change
- Unique constraint ensures no collisions

## Security Architecture

### Authentication Flow

```
1. User submits login form
   ↓
2. CSRF token validation
   ↓
3. Rate limit check (failed attempts)
   ↓
4. Password verification (bcrypt)
   ↓
5. Session regeneration (prevent fixation)
   ↓
6. Set authenticated flag
   ↓
7. Redirect to dashboard
```

### Protected Routes

Routes are protected by default. Public routes explicitly set `protected: false`:

```php
// Protected (default)
$router->get('/admin/teams', 'TeamsController', 'index');

// Public
$router->get('/teams', 'PublicController', 'teams', false);
```

### CSRF Protection

All forms include CSRF token:

```php
// Controller generates token
$data['csrfToken'] = $this->csrfToken();

// View includes hidden field
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

// Controller validates on POST
if (!$this->validateCsrf()) {
    $this->flash('error', 'Invalid form submission');
    return;
}
```

## Performance Considerations

### N+1 Query Prevention

Models load related data eagerly:

```php
// Team model includes players in all() and find()
public function all(): array {
    $teams = parent::all();
    foreach ($teams as &$team) {
        $team['players'] = $this->getPlayers($team['id']);
    }
    return $teams;
}
```

### Slug Uniqueness

Slug generation checks database once and increments if needed:

```php
// Algorithm:
// 1. Try "blue-stars"
// 2. If exists, try "blue-stars-1"
// 3. If exists, try "blue-stars-2"
// ... until unique slug found
```

### Session Performance

- Sessions stored in default PHP handler (file-based)
- 2-hour timeout automatically cleans old sessions
- Session regeneration only on login (not every request)

## Extension Points

### Adding a New Entity

1. Create model in `app/Models/` extending `Core\Model`
2. Implement `getTableName()` method
3. Add CRUD methods as needed
4. Create controller in `app/Controllers/` extending `Core\Controller`
5. Add routes in `config/routes.php`
6. Create views in `app/Views/[entity]/`

### Adding Validation Rules

Add methods to `Core\Controller`:

```php
protected function validateCustomRule($value): bool {
    // Validation logic
    return true/false;
}
```

### Custom Fixtures

Override `generateFixtures()` in League or Cup models to implement custom scheduling algorithms.

## Testing Architecture

See [testing.md](testing.md) for detailed information about the test suite.

## Future Improvements

Potential architectural enhancements:

1. **Dependency Injection**: Pass models to controllers instead of creating instances
2. **Repository Pattern**: Separate data access from business logic
3. **Events**: Emit events for fixture generation, result recording, etc.
4. **Caching**: Cache standings, upcoming fixtures, etc.
5. **WebSocket**: Real-time updates for live results
6. **API**: RESTful JSON API for mobile apps
7. **Background Jobs**: Process fixture generation asynchronously with Workers

## Conclusion

The architecture balances simplicity with functionality. It's designed for:
- Easy understanding by new developers
- Straightforward testing
- Simple deployment
- Type safety and security
- Future extensibility
