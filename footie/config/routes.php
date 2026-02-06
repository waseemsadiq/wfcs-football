<?php

declare(strict_types=1);

use Core\Router;

/**
 * Application routes.
 *
 * Routes are defined as: method, path, controller, action, protected (default: true)
 * Use {param} for URL parameters, e.g., /teams/{id}
 */

$router = new Router();

// ===================
// PUBLIC ROUTES (no auth required)
// ===================

// Public pages
$router->get('/', 'PublicController', 'index', false);
$router->get('/leagues', 'PublicController', 'leagues', false);
$router->get('/leagues/{slug}/data', 'PublicController', 'leagueData', false);
$router->get('/cups', 'PublicController', 'cups', false);
$router->get('/cups/{slug}/data', 'PublicController', 'cupData', false);
$router->get('/teams', 'PublicController', 'teams', false);
$router->get('/teams/{slug}/data', 'PublicController', 'teamData', false);
$router->get('/players', 'PublicController', 'players', false);
$router->get('/top-scorers/data', 'PublicController', 'topScorersData', false);
$router->get('/top-scorers', 'PublicController', 'topScorers', false);
$router->get('/fixture/{type}/{competitionSlug}/{fixtureSlug}', 'PublicController', 'fixture', false);
$router->get('/league/{slug}', 'PublicController', 'league', false);
$router->get('/cup/{slug}', 'PublicController', 'cup', false);
$router->get('/team/{slug}', 'PublicController', 'team', false);
$router->get('/player/{slug}', 'PublicController', 'player', false);

// ===================
// AUTHENTICATION ROUTES (no auth required)
// ===================

$router->get('/login', 'AuthController', 'showLogin', false);
$router->post('/login', 'AuthController', 'login', false);
$router->post('/logout', 'AuthController', 'logout', false);

// ===================
// ADMIN ROUTES (auth required)
// ===================

// Dashboard
$router->get('/admin', 'DashboardController', 'index');
$router->get('/admin/dashboard/upcoming-fixtures', 'DashboardController', 'getUpcomingFixtures');

// Teams
$router->get('/admin/teams', 'TeamsController', 'index');
$router->get('/admin/teams/ajax/list', 'TeamsController', 'getTeamsList');
$router->get('/admin/teams/create', 'TeamsController', 'create');
$router->post('/admin/teams/store', 'TeamsController', 'store');
$router->get('/admin/teams/{slug}', 'TeamsController', 'show');
$router->get('/admin/teams/{slug}/edit', 'TeamsController', 'edit');
$router->post('/admin/teams/{slug}/update', 'TeamsController', 'update');
$router->post('/admin/teams/{slug}/delete', 'TeamsController', 'delete');
$router->post('/admin/teams/delete-multiple', 'TeamsController', 'deleteMultiple');

// Staff
$router->get('/admin/staff', 'StaffController', 'index');
$router->get('/admin/staff/ajax/list', 'StaffController', 'getStaffList');
$router->get('/admin/staff/create', 'StaffController', 'create');
$router->post('/admin/staff/store', 'StaffController', 'store');
$router->get('/admin/staff/{id}', 'StaffController', 'show');
$router->get('/admin/staff/{id}/edit', 'StaffController', 'edit');
$router->post('/admin/staff/{id}/update', 'StaffController', 'update');
$router->post('/admin/staff/{id}/delete', 'StaffController', 'delete');
$router->post('/admin/staff/delete-multiple', 'StaffController', 'deleteMultiple');

// Players
$router->get('/admin/players', 'PlayersController', 'index');
$router->get('/admin/players/create', 'PlayersController', 'create');
$router->post('/admin/players/store', 'PlayersController', 'store');
$router->get('/admin/players/{slug}', 'PlayersController', 'show');
$router->get('/admin/players/{slug}/edit', 'PlayersController', 'edit');
$router->post('/admin/players/{slug}/update', 'PlayersController', 'update');
$router->post('/admin/players/{slug}/delete', 'PlayersController', 'delete');
$router->post('/admin/players/delete-multiple', 'PlayersController', 'deleteMultiple');
$router->get('/admin/players/ajax/list', 'PlayersController', 'getPlayersList');

// Seasons
$router->get('/admin/seasons', 'SeasonsController', 'index');
$router->get('/admin/seasons/create', 'SeasonsController', 'create');
$router->post('/admin/seasons/store', 'SeasonsController', 'store');
$router->get('/admin/seasons/{slug}', 'SeasonsController', 'show');
$router->get('/admin/seasons/{slug}/edit', 'SeasonsController', 'edit');
$router->post('/admin/seasons/{slug}/update', 'SeasonsController', 'update');
$router->post('/admin/seasons/{slug}/delete', 'SeasonsController', 'delete');
$router->post('/admin/seasons/{slug}/set-active', 'SeasonsController', 'setActive');

// Leagues
$router->get('/admin/leagues', 'LeaguesController', 'index');
$router->get('/admin/leagues/create', 'LeaguesController', 'create');
$router->post('/admin/leagues/store', 'LeaguesController', 'store');
$router->get('/admin/leagues/{slug}', 'LeaguesController', 'show');
$router->get('/admin/leagues/{slug}/edit', 'LeaguesController', 'edit');
$router->post('/admin/leagues/{slug}/update', 'LeaguesController', 'update');
$router->post('/admin/leagues/{slug}/delete', 'LeaguesController', 'delete');
$router->get('/admin/leagues/{slug}/fixtures', 'LeaguesController', 'fixtures');
$router->post('/admin/leagues/{slug}/fixtures', 'LeaguesController', 'updateFixtures');
$router->post('/admin/leagues/{slug}/regenerate-fixtures', 'LeaguesController', 'regenerateFixtures');
$router->get('/admin/leagues/ajax/scorer-row', 'LeaguesController', 'renderScorerRow');
$router->get('/admin/leagues/ajax/card-row', 'LeaguesController', 'renderCardRow');

// Cups
$router->get('/admin/cups', 'CupsController', 'index');
$router->get('/admin/cups/create', 'CupsController', 'create');
$router->post('/admin/cups/store', 'CupsController', 'store');
$router->get('/admin/cups/{slug}', 'CupsController', 'show');
$router->get('/admin/cups/{slug}/edit', 'CupsController', 'edit');
$router->post('/admin/cups/{slug}/update', 'CupsController', 'update');
$router->post('/admin/cups/{slug}/delete', 'CupsController', 'delete');
$router->get('/admin/cups/{slug}/fixtures', 'CupsController', 'fixtures');
$router->post('/admin/cups/{slug}/fixtures', 'CupsController', 'updateFixtures');
$router->get('/admin/cups/ajax/scorer-row', 'CupsController', 'renderScorerRow');
$router->get('/admin/cups/ajax/card-row', 'CupsController', 'renderCardRow');
$router->post('/admin/cups/{slug}/regenerate-fixtures', 'CupsController', 'regenerateFixtures');

// Fixture Details (admin)
$router->get('/admin/fixture/{type}/{competitionSlug}/{fixtureSlug}', 'FixturesController', 'fixtureDetail');
$router->post('/admin/fixture/{type}/{competitionSlug}/{fixtureSlug}', 'FixturesController', 'updateFixtureDetail');

return $router;
