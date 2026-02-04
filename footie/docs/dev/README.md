# WFCS Football - Developer Documentation

Welcome to the WFCS Football developer documentation. This guide will help you understand the application architecture, codebase structure, and development workflow.

## Table of Contents

- [README.md](README.md) - This file - Overview and getting started
- [architecture.md](architecture.md) - System architecture and design decisions
- [api.md](api.md) - API endpoints and route documentation
- [database.md](database.md) - Database schema and data models
- [configuration.md](configuration.md) - Configuration options and environment setup
- [testing.md](testing.md) - Testing guide and test suite documentation

## Quick Start

WFCS Football is a football club management system built to run on Galvani, an async event-driven PHP runtime. The application manages teams, seasons, leagues, and cup competitions with automatic fixture generation and standings calculation.

### Technology Stack

- **Runtime**: Galvani (async event-driven PHP)
- **Language**: PHP 8.1+ (strict types)
- **Database**: MySQL (embedded via Galvani)
- **Frontend**: Tailwind CSS 4.1, vanilla JavaScript
- **Architecture**: MVC pattern with custom framework

### Key Features

- **Season Management**: Create and manage multiple seasons with active season selection
- **Team Management**: Teams with player rosters, colors, and slugs
- **League Competitions**: Round-robin fixtures with automatic generation and standings calculation
- **Cup Competitions**: Single-elimination knockout tournaments with bracket generation
- **Fixture Management**: Automatic scheduling with configurable frequencies (weekly, fortnightly, monthly)
- **Results Tracking**: Record scores, scorers, cards, extra time, and penalty results
- **Public Interface**: View-only public pages for leagues, cups, teams, and standings
- **Admin Dashboard**: Authenticated admin area with full CRUD operations

### Project Structure

```
footie/
├── index.php                    # Application entry point
├── config/                      # Configuration files
│   ├── app.php                 # App settings (name, debug mode, password hash)
│   ├── database.php            # Database connection config
│   └── routes.php              # Route definitions
├── core/                        # Core framework classes
│   ├── Router.php              # URL routing and dispatch
│   ├── Controller.php          # Base controller with helpers
│   ├── Model.php               # Base model with CRUD operations
│   ├── View.php                # Template rendering engine
│   ├── Auth.php                # Authentication and sessions
│   └── Database.php            # Database connection singleton
├── app/                         # Application code
│   ├── Controllers/            # Request handlers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PublicController.php
│   │   ├── SeasonsController.php
│   │   ├── TeamsController.php
│   │   ├── LeaguesController.php
│   │   └── CupsController.php
│   ├── Models/                 # Data models and business logic
│   │   ├── Season.php
│   │   ├── Team.php
│   │   ├── League.php
│   │   ├── Cup.php
│   │   └── Traits/
│   │       └── HasTeams.php
│   └── Views/                  # HTML templates
│       ├── layouts/            # Layout templates
│       ├── partials/           # Reusable components
│       ├── auth/               # Login pages
│       ├── dashboard/          # Dashboard views
│       ├── public/             # Public-facing pages
│       ├── seasons/            # Season CRUD views
│       ├── teams/              # Team CRUD views
│       ├── leagues/            # League CRUD views
│       └── cups/               # Cup CRUD views
├── css/                         # Stylesheets
│   ├── input.css               # Tailwind source
│   ├── output.css              # Compiled CSS
│   └── fonts/                  # Local font files
├── images/                      # Static images
├── tests/                       # Test suite
│   ├── Core/                   # Core framework tests
│   ├── Models/                 # Model tests
│   └── run_tests.php           # Test runner
└── .env                         # Environment configuration (not committed)
```

### Installation & Setup

1. **Install the application** using the Galvani installer:
   ```bash
   ./galvani footie-install.php
   ```

2. **Start the server**:
   ```bash
   ./galvani --server :8080 --document-root .
   ```

3. **Access the application**:
   - Public site: `http://localhost:8080/footie/`
   - Admin login: `http://localhost:8080/footie/login`
   - Default password: `admin` (change this!)

### Development Workflow

1. **Make code changes** in `footie/` directory
2. **Refresh browser** - Galvani serves changes immediately
3. **Run tests** to verify functionality:
   ```bash
   ./galvani footie/tests/run_tests.php
   ```

### Code Standards

- PHP 8.1+ with strict types (`declare(strict_types=1)`)
- PSR-12 coding style
- Type hints on all parameters and return types
- Descriptive variable and method names
- DocBlock comments on all classes and public methods
- camelCase for PHP variables and methods
- snake_case for database columns

### Key Concepts

#### MVC Architecture

- **Models** handle data and business logic (CRUD, fixtures, standings)
- **Controllers** handle requests, validate input, call models, render views
- **Views** are PHP templates with extracted data variables

#### Routing

All requests go through `index.php` which loads the router. Routes are defined in `config/routes.php` with pattern matching for URL parameters (e.g., `/teams/{slug}`).

#### Authentication

Simple password-based auth with:
- Session management (2-hour timeout)
- Rate limiting (5 failed attempts = 15-minute lockout)
- CSRF token protection on all forms
- Secure session cookies (HttpOnly, SameSite=Strict)

#### Data Flow

```
Request → Router → Controller → Model → Database
                      ↓
                    View → Response
```

### Next Steps

- Read [architecture.md](architecture.md) for detailed system design
- See [api.md](api.md) for complete route documentation
- Check [database.md](database.md) for schema details
- Review [configuration.md](configuration.md) for environment setup
- Explore [testing.md](testing.md) for test suite information

## Contributing

When contributing:

1. Write tests for new features
2. Follow existing code style and conventions
3. Update documentation for API changes
4. Test with both sample data and empty database
5. Verify both public and admin interfaces work correctly

## Support

For questions or issues:
- Check existing documentation in `footie/docs/dev/`
- Review test suite in `footie/tests/` for usage examples
- See main README.md for general application information
