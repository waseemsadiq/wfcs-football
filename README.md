# WFCS Football: Football Club Management System

WFCS Football is a web-based admin application for managing football clubs, leagues, seasons, and tournaments. You can create competitions, generate match schedules, track results, and manage teams all from a single dashboard.

## What You Can Do

### Admin Features (Authenticated Access)

- **Manage Seasons**: Create multiple seasons, set which season is active, and organise all competitions within specific timeframes
- **Manage Teams**: Add teams to your system, assign colours for identification, and manage player rosters
- **Create Leagues**: Set up league competitions using round-robin format where every team plays every other team both home and away
- **Configure League Fixtures**: Generate automatic match schedules with flexible timing (weekly, fortnightly, or monthly fixtures)
- **Record Match Results**: Input final scores, extra time, and penalty shoot-out results for completed fixtures
- **View Standings**: Automatically calculate league positions based on wins, draws, and losses
- **Create Cups**: Set up knockout-style cup tournaments with single-elimination brackets
- **Generate Cup Fixtures**: Automatically create tournament brackets with proper seeding and progression
- **Dashboard Overview**: See at a glance your active season, upcoming fixtures, team counts, and competition summaries

### Public Features (No Login Required)

- **Browse Leagues**: View all league competitions and their fixtures
- **Browse Cups**: See knockout tournament brackets and results
- **View Teams**: Explore team details, colours, and competition associations
- **Check Standings**: Monitor league positions and points as the season progresses

## Getting Started

### Prerequisites

You'll need:
- PHP 8.1 or higher
- Node.js (for stylesheet compilation)
- A web server with `.htaccess` support (Apache recommended)

### Installation

**1. Clone or extract the project**

```bash
cd /[project_directory]
```

**2. Install dependencies**

```bash
npm install
```

**3. Create your admin password**

```bash
php generate_password.php
```

Follow the prompt to enter your desired admin password. Copy the hash it produces.

**4. Configure your environment**

```bash
cp .env.example .env
```

Edit `.env` and add:
- The `ADMIN_PASSWORD_HASH` from step 3
- Set `FOOTIE_DEBUG=true` for development, or `false` for production

**5. Compile stylesheets**

For development (watch mode):
```bash
npx tailwindcss -i css/input.css -o css/output.css --watch
```

For production (minified):
```bash
npx tailwindcss -i css/input.css -o css/output.css --minify
```

**6. Start your local server**

```bash
php -S localhost:8000
```

Visit `http://localhost:8000` in your browser.

**7. Log in**

Click the admin link and enter your password.

## Running Tests

WFCS includes a comprehensive test suite with 58 tests covering all core functionality.

```bash
npm test
```

Or run tests directly:

```bash
php tests/run_tests.php
```

All tests execute in under one second and should show 100% pass rate.

## Project Structure

```
footie/
├── index.php                 # Application entry point
├── config/
│   ├── app.php              # Configuration settings
│   └── routes.php           # URL route definitions
├── core/                     # Framework components
│   ├── Router.php           # URL routing
│   ├── Controller.php       # Base controller logic
│   ├── Model.php            # Data handling
│   ├── View.php             # Template rendering
│   └── Auth.php             # Authentication & sessions
├── app/
│   ├── Controllers/         # Request handlers
│   ├── Models/              # Business logic
│   └── Views/               # HTML templates
├── data/                     # JSON data storage
├── css/                      # Tailwind CSS styling
├── images/                   # Static images
├── tests/                    # Test suite
├── dist/                     # Production files (deployed to server)
└── .env                      # Your local configuration (not committed)
```

## Key Features

### Fixture Generation

When you create a league, WFCS automatically generates all fixtures for the season using a round-robin algorithm. Each team plays every other team both home and away. The system handles even numbers of teams smoothly and generates BYE rounds for odd-numbered leagues.

You can adjust fixture frequency when creating the league (weekly, fortnightly, or monthly), and WFCS spaces matches accordingly across the season.

### Standings Calculation

League standings automatically update as you record match results. The system calculates:
- Matches played
- Wins, draws, and losses
- Points (3 for a win, 1 for a draw)
- Goal difference
- Goals for and against

Standings sort by points first, then goal difference, then goals scored.

### Secure Authentication

Your admin area is protected by:
- Secure password hashing (bcrypt)
- Session-based authentication
- Automatic logout after 2 hours of inactivity
- Rate limiting (5 failed login attempts = 15-minute lockout)
- CSRF token protection on all forms
- Secure session cookies with HttpOnly and SameSite flags

### Data Storage

All data is stored in JSON files within the `/data` directory. No database setup required. WFCS works perfectly for small to medium-sized clubs and organisations.

## Deploying to Production

WFCS stores production-ready files in the `/dist` folder. When deploying:

**1. Ensure `/dist` is up to date**

Copy only these files and folders to your server:
- `index.php`
- `app/`, `config/`, `core/`, `css/`, `data/`, `images/`
- `.htaccess` (not `.htaccess.txt`)
- `.env` (but NOT `.env.example`)

**2. Do NOT copy these to production**
- `node_modules/`, `package.json`, `package-lock.json`
- `docs/`, `tests/`, `generate_password.php`
- Markdown files (README.md, CLAUDE.md, etc.)
- Git directories (`.git/`, `.github/`, etc.)

**3. Set production environment**

Edit your `.env` on the server and set `FOOTIE_DEBUG=false`.

**4. Configure your web server**

Rename `.htaccess.txt` to `.htaccess` on your server. This enables clean URLs (e.g., `/leagues` instead of `/index.php?page=leagues`).

If deploying to a subdirectory, Footie detects the path automatically and adjusts URLs accordingly.

## Configuration

### Environment Variables

Edit `.env` to customise:

```bash
FOOTIE_DEBUG=true               # Show errors (true) or hide them (false)
ADMIN_PASSWORD_HASH=...         # Your bcrypt password hash
```

### Tailwind CSS

Footie uses Tailwind CSS 4.1 for styling with a dark theme (slate-950 background and green accents).

Edit `css/input.css` to customise colours or add custom styles. Always recompile after changes:

```bash
npx tailwindcss -i css/input.css -o css/output.css --minify
```

## Understanding the Code

### Models (Business Logic)

- **Season**: Manages season creation and active season selection
- **Team**: Handles team data and team-league associations
- **League**: Generates round-robin fixtures and calculates standings
- **Cup**: Creates tournament brackets with single-elimination format

See the `/app/Models` folder for the complete logic.

### Controllers (Request Handling)

Each controller handles a specific feature area (seasons, teams, leagues, cups, authentication). Controllers validate input, call model methods, and render views.

### Views (Templates)

Templates use Tailwind CSS for styling. Reusable components (buttons, forms, tables) are in `/app/Views/partials/` to keep code DRY.

## Security Considerations

- Never commit `.env` to version control (it contains your password hash)
- Always use `FOOTIE_DEBUG=false` in production
- Keep PHP updated to the latest version
- Enable HTTPS on your server
- Use strong, unique admin passwords
- Regularly review access logs

## Troubleshooting

**Route not found error**

Check that `.htaccess` is properly configured on your server. If you're in a subdirectory, verify the `RewriteBase` directive matches your path.

**Login not working**

Verify your `.env` contains a valid `ADMIN_PASSWORD_HASH`. Regenerate it with `php generate_password.php` if needed.

**Tailwind styles not appearing**

Recompile CSS: `npx tailwindcss -i css/input.css -o css/output.css --minify`

**Tests failing**

Ensure PHP is version 8.1 or higher: `php -v`

## Development

When developing:

- Run tests frequently: `npm test`
- Watch for CSS changes: `npx tailwindcss -i css/input.css -o css/output.css --watch`
- Keep debug mode on: `FOOTIE_DEBUG=true` in `.env`
- Use the test suite to verify changes don't break functionality

## Support

For issues or questions, check the documentation in the `/docs` folder or review the test suite in `/tests` to understand how features work.

## Licence

This project is proprietary software.
