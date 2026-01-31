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
$router->get('/league/{slug}', 'PublicController', 'league', false);
$router->get('/cup/{slug}', 'PublicController', 'cup', false);
$router->get('/team/{slug}', 'PublicController', 'team', false);

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
$router->get('/admin/teams/create', 'TeamsController', 'create');
$router->post('/admin/teams/store', 'TeamsController', 'store');
$router->get('/admin/teams/{slug}', 'TeamsController', 'show');
$router->get('/admin/teams/{slug}/edit', 'TeamsController', 'edit');
$router->post('/admin/teams/{slug}/update', 'TeamsController', 'update');
$router->post('/admin/teams/{slug}/delete', 'TeamsController', 'delete');
$router->post('/admin/teams/delete-multiple', 'TeamsController', 'deleteMultiple');

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
$router->post('/admin/cups/{slug}/regenerate-fixtures', 'CupsController', 'regenerateFixtures');

return $router;
