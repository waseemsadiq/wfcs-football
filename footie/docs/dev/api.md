# API Documentation

This document describes all HTTP endpoints (routes) available in the WFCS Football application.

## Route Format

Routes are defined as:
```php
$router->method('/path/{param}', 'Controller', 'action', protected);
```

- `method`: GET or POST
- `path`: URL pattern with optional `{param}` placeholders
- `Controller`: Controller class name (without namespace)
- `action`: Method name to call
- `protected`: Whether authentication is required (default: true)

## Public Routes

No authentication required. Accessible to all users.

### Homepage

**GET /**

Display homepage with active season leagues, cups, recent results, and upcoming fixtures.

**Controller**: `PublicController::index()`

**Response**: HTML page

**Data**:
- Active season name
- List of leagues with team counts
- List of cups with team counts
- Recent results (last 5 completed fixtures)
- Upcoming fixtures (next 5 unplayed fixtures)

---

### Leagues List

**GET /leagues**

Display all leagues in the active season.

**Controller**: `PublicController::leagues()`

**Response**: HTML page

---

### League Details

**GET /league/{slug}**

Display specific league with standings and fixtures.

**Controller**: `PublicController::league($slug)`

**URL Parameters**:
- `slug` - League slug (e.g., "premier-league")

**Response**: HTML page

**Data**:
- League details (name, season, teams)
- Current standings table
- All fixtures (past and upcoming)

---

### League Data (AJAX)

**GET /leagues/{slug}/data**

Get league data as JSON for AJAX requests.

**Controller**: `PublicController::leagueData($slug)`

**URL Parameters**:
- `slug` - League slug

**Response**: JSON

```json
{
  "league": {
    "id": 1,
    "name": "Premier League",
    "slug": "premier-league",
    "seasonId": 1
  },
  "standings": [...],
  "fixtures": [...]
}
```

---

### Cups List

**GET /cups**

Display all cups in the active season.

**Controller**: `PublicController::cups()`

**Response**: HTML page

---

### Cup Details

**GET /cup/{slug}**

Display specific cup with bracket and fixtures.

**Controller**: `PublicController::cup($slug)`

**URL Parameters**:
- `slug` - Cup slug (e.g., "fa-cup")

**Response**: HTML page

**Data**:
- Cup details
- Rounds with fixtures
- Results and winners

---

### Cup Data (AJAX)

**GET /cups/{slug}/data**

Get cup data as JSON for AJAX requests.

**Controller**: `PublicController::cupData($slug)`

**URL Parameters**:
- `slug` - Cup slug

**Response**: JSON

```json
{
  "cup": {
    "id": 1,
    "name": "FA Cup",
    "slug": "fa-cup"
  },
  "rounds": [...]
}
```

---

### Teams List

**GET /teams**

Display all teams.

**Controller**: `PublicController::teams()`

**Response**: HTML page

---

### Team Details

**GET /team/{slug}**

Display specific team with fixtures and stats.

**Controller**: `PublicController::team($slug)`

**URL Parameters**:
- `slug` - Team slug (e.g., "blue-stars")

**Response**: HTML page

**Data**:
- Team details (name, color, players)
- Upcoming fixtures
- Recent results
- League and cup participations

---

### Team Data (AJAX)

**GET /teams/{slug}/data**

Get team data as JSON for AJAX requests.

**Controller**: `PublicController::teamData($slug)`

**URL Parameters**:
- `slug` - Team slug

**Response**: JSON

```json
{
  "team": {
    "id": 1,
    "name": "Blue Stars",
    "slug": "blue-stars",
    "colour": "#0000FF",
    "players": ["Player 1", "Player 2"]
  },
  "upcomingFixtures": [...],
  "recentResults": [...]
}
```

---

## Authentication Routes

No authentication required for these routes (they handle login/logout).

### Login Page

**GET /login**

Display login form.

**Controller**: `AuthController::showLogin()`

**Response**: HTML page

---

### Login Processing

**POST /login**

Process login attempt.

**Controller**: `AuthController::login()`

**POST Parameters**:
- `password` - Admin password
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin`
- Failure: Redirect to `/login` with error message

**Security**:
- Rate limiting: 5 failed attempts = 15-minute lockout
- Session regeneration on success
- CSRF protection

---

### Logout

**POST /logout**

Log out current user.

**Controller**: `AuthController::logout()`

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**: Redirect to `/`

---

## Admin Routes

All admin routes require authentication. Unauthenticated requests redirect to `/login`.

### Dashboard

**GET /admin**

Display admin dashboard with active season summary.

**Controller**: `DashboardController::index()`

**Response**: HTML page

**Data**:
- Active season details
- Team count
- Leagues in active season
- Cups in active season
- Quick stats

---

### Dashboard Upcoming Fixtures (AJAX)

**GET /admin/dashboard/upcoming-fixtures**

Get upcoming fixtures for a specific competition.

**Controller**: `DashboardController::getUpcomingFixtures()`

**Query Parameters**:
- `type` - Competition type ("league" or "cup")
- `id` - Competition ID

**Response**: JSON

```json
{
  "fixtures": [
    {
      "id": 1,
      "date": "2024-02-15",
      "time": "15:00:00",
      "homeTeam": {...},
      "awayTeam": {...},
      "roundName": "Semi-Final" // Only for cups
    }
  ]
}
```

---

## Teams Routes

### Teams Index

**GET /admin/teams**

List all teams.

**Controller**: `TeamsController::index()`

**Response**: HTML page

---

### Create Team Form

**GET /admin/teams/create**

Display form to create new team.

**Controller**: `TeamsController::create()`

**Response**: HTML page

---

### Store Team

**POST /admin/teams/store**

Create a new team.

**Controller**: `TeamsController::store()`

**POST Parameters**:
- `name` - Team name (required)
- `colour` - Hex colour code (required, format: #RRGGBB)
- `players` - Player names (one per line, optional)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/teams` with success message
- Error: Redirect to `/admin/teams/create` with error message

**Validation**:
- Name required and max 100 chars
- Colour must be valid hex format
- Slug auto-generated and made unique

---

### Show Team

**GET /admin/teams/{slug}**

Display team details.

**Controller**: `TeamsController::show($slug)`

**URL Parameters**:
- `slug` - Team slug

**Response**: HTML page

---

### Edit Team Form

**GET /admin/teams/{slug}/edit**

Display form to edit team.

**Controller**: `TeamsController::edit($slug)`

**URL Parameters**:
- `slug` - Team slug

**Response**: HTML page

---

### Update Team

**POST /admin/teams/{slug}/update**

Update an existing team.

**Controller**: `TeamsController::update($slug)`

**URL Parameters**:
- `slug` - Team slug

**POST Parameters**:
- `name` - Team name (required)
- `colour` - Hex colour code (required)
- `players` - Player names (one per line, optional)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/teams/{slug}` with success message
- Error: Redirect to `/admin/teams/{slug}/edit` with error message

---

### Delete Team

**POST /admin/teams/{slug}/delete**

Delete a team.

**Controller**: `TeamsController::delete($slug)`

**URL Parameters**:
- `slug` - Team slug

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/teams` with success message
- Error: Redirect to `/admin/teams` with error message

**Note**: Cannot delete teams that are in competitions.

---

### Delete Multiple Teams

**POST /admin/teams/delete-multiple**

Delete multiple teams at once.

**Controller**: `TeamsController::deleteMultiple()`

**POST Parameters**:
- `team_ids[]` - Array of team IDs to delete
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/teams` with count message
- Error: Redirect to `/admin/teams` with error message

---

## Seasons Routes

### Seasons Index

**GET /admin/seasons**

List all seasons.

**Controller**: `SeasonsController::index()`

**Response**: HTML page with seasons sorted by start date (newest first)

---

### Create Season Form

**GET /admin/seasons/create**

Display form to create new season.

**Controller**: `SeasonsController::create()`

**Response**: HTML page

---

### Store Season

**POST /admin/seasons/store**

Create a new season.

**Controller**: `SeasonsController::store()`

**POST Parameters**:
- `name` - Season name (required, max 100 chars)
- `start_date` - Start date (required, format: YYYY-MM-DD)
- `end_date` - End date (required, format: YYYY-MM-DD)
- `is_active` - Whether this is the active season (0 or 1)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/seasons` with success message
- Error: Redirect to `/admin/seasons/create` with error message

**Validation**:
- Name required
- Dates required and valid format
- End date must be after start date
- If is_active=1, deactivates other seasons

---

### Show Season

**GET /admin/seasons/{slug}**

Display season details with leagues and cups.

**Controller**: `SeasonsController::show($slug)`

**URL Parameters**:
- `slug` - Season slug

**Response**: HTML page

---

### Edit Season Form

**GET /admin/seasons/{slug}/edit**

Display form to edit season.

**Controller**: `SeasonsController::edit($slug)`

**URL Parameters**:
- `slug` - Season slug

**Response**: HTML page

---

### Update Season

**POST /admin/seasons/{slug}/update**

Update an existing season.

**Controller**: `SeasonsController::update($slug)`

**URL Parameters**:
- `slug` - Season slug

**POST Parameters**:
- `name` - Season name (required)
- `start_date` - Start date (required)
- `end_date` - End date (required)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/seasons/{slug}` with success message
- Error: Redirect to `/admin/seasons/{slug}/edit` with error message

---

### Delete Season

**POST /admin/seasons/{slug}/delete**

Delete a season.

**Controller**: `SeasonsController::delete($slug)`

**URL Parameters**:
- `slug` - Season slug

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/seasons` with success message
- Error: Redirect to `/admin/seasons` with error message

**Note**: Deletes all leagues and cups in the season (CASCADE).

---

### Set Active Season

**POST /admin/seasons/{slug}/set-active**

Set a season as the active season.

**Controller**: `SeasonsController::setActive($slug)`

**URL Parameters**:
- `slug` - Season slug

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/seasons` with success message
- Error: Redirect to `/admin/seasons` with error message

**Effect**: Deactivates all other seasons and activates this one.

---

## Leagues Routes

### Leagues Index

**GET /admin/leagues**

List all leagues grouped by season.

**Controller**: `LeaguesController::index()`

**Response**: HTML page

---

### Create League Form

**GET /admin/leagues/create**

Display form to create new league.

**Controller**: `LeaguesController::create()`

**Response**: HTML page with season and team selection

---

### Store League

**POST /admin/leagues/store**

Create a new league with automatic fixture generation.

**Controller**: `LeaguesController::store()`

**POST Parameters**:
- `name` - League name (required)
- `season_id` - Season ID (required)
- `team_ids[]` - Array of team IDs (required, min 2)
- `start_date` - Start date for fixtures (required)
- `match_time` - Default match time (required, format: HH:MM)
- `frequency` - Fixture frequency: "weekly", "fortnightly", or "monthly" (required)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/leagues/{slug}` with success message
- Error: Redirect to `/admin/leagues/create` with error message

**Effect**:
- Creates league
- Associates teams
- Generates all fixtures (round-robin, home and away)

---

### Show League

**GET /admin/leagues/{slug}**

Display league details with standings and fixtures.

**Controller**: `LeaguesController::show($slug)`

**URL Parameters**:
- `slug` - League slug

**Response**: HTML page

---

### Edit League Form

**GET /admin/leagues/{slug}/edit**

Display form to edit league.

**Controller**: `LeaguesController::edit($slug)`

**URL Parameters**:
- `slug` - League slug

**Response**: HTML page

---

### Update League

**POST /admin/leagues/{slug}/update**

Update league details.

**Controller**: `LeaguesController::update($slug)`

**URL Parameters**:
- `slug` - League slug

**POST Parameters**:
- `name` - League name (required)
- `season_id` - Season ID (required)
- `team_ids[]` - Array of team IDs
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/leagues/{slug}` with success message
- Error: Redirect to `/admin/leagues/{slug}/edit` with error message

**Note**: Does NOT regenerate fixtures. Use regenerate endpoint for that.

---

### Delete League

**POST /admin/leagues/{slug}/delete**

Delete a league.

**Controller**: `LeaguesController::delete($slug)`

**URL Parameters**:
- `slug` - League slug

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/leagues` with success message
- Error: Redirect to `/admin/leagues` with error message

**Effect**: Deletes league, fixtures, and team associations (CASCADE).

---

### League Fixtures

**GET /admin/leagues/{slug}/fixtures**

Display and edit league fixtures.

**Controller**: `LeaguesController::fixtures($slug)`

**URL Parameters**:
- `slug` - League slug

**Response**: HTML page with editable fixture list

---

### Update League Fixtures

**POST /admin/leagues/{slug}/fixtures**

Update fixture dates, times, and results.

**Controller**: `LeaguesController::updateFixtures($slug)`

**URL Parameters**:
- `slug` - League slug

**POST Parameters** (arrays indexed by fixture ID):
- `dates[id]` - Fixture date (format: YYYY-MM-DD)
- `times[id]` - Fixture time (format: HH:MM)
- `home_scores[id]` - Home score (integer or empty)
- `away_scores[id]` - Away score (integer or empty)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/leagues/{slug}/fixtures` with success message
- Error: Redirect to `/admin/leagues/{slug}/fixtures` with error message

**Note**: Results are only saved if both scores are provided.

---

### Regenerate League Fixtures

**POST /admin/leagues/{slug}/regenerate-fixtures**

Regenerate all fixtures (deletes existing unplayed fixtures).

**Controller**: `LeaguesController::regenerateFixtures($slug)`

**URL Parameters**:
- `slug` - League slug

**POST Parameters**:
- `start_date` - New start date (required)
- `match_time` - Default match time (required)
- `frequency` - Fixture frequency (required)
- `delete_existing` - Whether to delete ALL fixtures or only unplayed (1 or 0)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/leagues/{slug}/fixtures` with success message
- Error: Redirect to `/admin/leagues/{slug}/fixtures` with error message

**Warning**: If `delete_existing=1`, ALL fixtures including results are deleted.

---

## Cups Routes

### Cups Index

**GET /admin/cups**

List all cups grouped by season.

**Controller**: `CupsController::index()`

**Response**: HTML page

---

### Create Cup Form

**GET /admin/cups/create**

Display form to create new cup.

**Controller**: `CupsController::create()`

**Response**: HTML page

---

### Store Cup

**POST /admin/cups/store**

Create a new cup with automatic bracket generation.

**Controller**: `CupsController::store()`

**POST Parameters**:
- `name` - Cup name (required)
- `season_id` - Season ID (required)
- `team_ids[]` - Array of team IDs (required, min 2)
- `start_date` - Start date for first round (required)
- `match_time` - Default match time (required)
- `frequency` - Round frequency: "weekly", "fortnightly", or "monthly" (required)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/cups/{slug}` with success message
- Error: Redirect to `/admin/cups/create` with error message

**Effect**:
- Creates cup
- Associates teams
- Generates single-elimination bracket
- Creates rounds and fixtures

---

### Show Cup

**GET /admin/cups/{slug}**

Display cup details with bracket.

**Controller**: `CupsController::show($slug)`

**URL Parameters**:
- `slug` - Cup slug

**Response**: HTML page

---

### Edit Cup Form

**GET /admin/cups/{slug}/edit**

Display form to edit cup.

**Controller**: `CupsController::edit($slug)`

**URL Parameters**:
- `slug` - Cup slug

**Response**: HTML page

---

### Update Cup

**POST /admin/cups/{slug}/update**

Update cup details.

**Controller**: `CupsController::update($slug)`

**URL Parameters**:
- `slug` - Cup slug

**POST Parameters**:
- `name` - Cup name (required)
- `season_id` - Season ID (required)
- `team_ids[]` - Array of team IDs
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/cups/{slug}` with success message
- Error: Redirect to `/admin/cups/{slug}/edit` with error message

**Note**: Does NOT regenerate bracket. Use regenerate endpoint for that.

---

### Delete Cup

**POST /admin/cups/{slug}/delete**

Delete a cup.

**Controller**: `CupsController::delete($slug)`

**URL Parameters**:
- `slug` - Cup slug

**POST Parameters**:
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/cups` with success message
- Error: Redirect to `/admin/cups` with error message

**Effect**: Deletes cup, rounds, fixtures, and team associations (CASCADE).

---

### Cup Fixtures

**GET /admin/cups/{slug}/fixtures**

Display and edit cup fixtures.

**Controller**: `CupsController::fixtures($slug)`

**URL Parameters**:
- `slug` - Cup slug

**Response**: HTML page with editable fixture list grouped by round

---

### Update Cup Fixtures

**POST /admin/cups/{slug}/fixtures**

Update fixture dates, times, and results.

**Controller**: `CupsController::updateFixtures($slug)`

**URL Parameters**:
- `slug` - Cup slug

**POST Parameters** (arrays indexed by fixture ID):
- `dates[id]` - Fixture date
- `times[id]` - Fixture time
- `home_scores[id]` - Home score (90 mins)
- `away_scores[id]` - Away score (90 mins)
- `extra_time[id]` - Extra time played (1 or 0)
- `home_scores_et[id]` - Home score after extra time
- `away_scores_et[id]` - Away score after extra time
- `penalties[id]` - Penalties taken (1 or 0)
- `home_pens[id]` - Home penalty score
- `away_pens[id]` - Away penalty score
- `winner[id]` - Winner team ID (if draw after 90 mins)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/cups/{slug}/fixtures` with success message
- Error: Redirect to `/admin/cups/{slug}/fixtures` with error message

**Effect**:
- Updates fixture details
- Records result
- Determines winner
- Advances winner to next round automatically

---

### Regenerate Cup Fixtures

**POST /admin/cups/{slug}/regenerate-fixtures**

Regenerate cup bracket (deletes all fixtures and results).

**Controller**: `CupsController::regenerateFixtures($slug)`

**URL Parameters**:
- `slug` - Cup slug

**POST Parameters**:
- `start_date` - New start date (required)
- `match_time` - Default match time (required)
- `frequency` - Round frequency (required)
- `csrf_token` - CSRF token

**Response**:
- Success: Redirect to `/admin/cups/{slug}/fixtures` with success message
- Error: Redirect to `/admin/cups/{slug}/fixtures` with error message

**Warning**: Deletes ALL rounds, fixtures, and results. Cannot be undone.

---

## Error Responses

### 404 Not Found

When a route doesn't match any defined pattern:

**Response**: HTML page with 404 message and navigation links

**Debug Mode**: Shows request URI, normalized URI, and script path

---

### 403 Forbidden (Authentication Required)

When accessing protected route without authentication:

**Response**: Redirect to `/login`

---

## Response Formats

### HTML Responses

Default response format. Uses layouts and view templates.

### JSON Responses

Used for AJAX endpoints. Format:

```json
{
  "data": {...},
  "error": "error message" // Only on error
}
```

### Redirects

HTTP 302 redirects with flash messages stored in session.

## Rate Limiting

Login endpoint has rate limiting:
- 5 failed attempts within any period
- 15-minute lockout after 5 failures
- Counter resets on successful login

## CSRF Protection

All POST endpoints require valid CSRF token:
- Token generated per session
- Token validated using timing-safe comparison
- Invalid token returns error message

## Subdirectory Support

All routes support subdirectory installation:
- Router normalizes URIs
- Controllers adjust redirect URLs
- Views receive `$basePath` variable
- Works automatically, no configuration needed
