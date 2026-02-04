<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Team;

/**
 * Controller for managing football teams.
 * Handles all team CRUD operations and views.
 */
class TeamsController extends Controller
{
    private Team $teamModel;

    public function __construct()
    {
        parent::__construct();
        $this->teamModel = new Team();
    }

    /**
     * Display a list of all teams.
     */
    public function index(): void
    {
        $teams = $this->teamModel->allSorted();

        $this->render('teams/index', [
            'title' => 'Teams',
            'currentPage' => 'teams',
            'teams' => $teams,
        ]);
    }

    /**
     * Show a single team with full details.
     */
    public function show(string $slug): void
    {
        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            $this->flash('error', 'Team not found.');
            $this->redirect('/admin/teams');
            return;
        }

        // Get competitions this team participates in
        $leagues = [];
        $cups = [];

        $seasonModel = new \App\Models\Season();
        $activeSeason = $seasonModel->getActive();

        if ($activeSeason) {
            $leagueModel = new \App\Models\League();
            $cupModel = new \App\Models\Cup();

            $allLeagues = $leagueModel->getBySeasonId($activeSeason['id']);
            $allCups = $cupModel->getBySeasonId($activeSeason['id']);

            // Filter leagues that include this team
            $leagues = array_filter($allLeagues, function($league) use ($team) {
                return in_array($team['id'], $league['teamIds'] ?? []);
            });

            // Filter cups that include this team
            $cups = array_filter($allCups, function($cup) use ($team) {
                return in_array($team['id'], $cup['teamIds'] ?? []);
            });
        }

        $this->render('teams/show', [
            'title' => $team['name'],
            'currentPage' => 'teams',
            'team' => $team,
            'leagues' => array_values($leagues),
            'cups' => array_values($cups),
        ]);
    }

    /**
     * Show the form to create a new team.
     */
    public function create(): void
    {
        $this->render('teams/create', [
            'title' => 'Add Team',
            'currentPage' => 'teams',
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new team in the database.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/teams/create');
            return;
        }

        $missing = $this->validateRequired(['name']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide a team name.');
            $this->redirect('/admin/teams/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $contact = $this->sanitizeString($this->post('contact', ''), 100);
        $email = $this->sanitizeString($this->post('email', ''), 255);
        $colour = $this->post('colour', '#1a5f2a');

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Team name must be between 1 and 100 characters.');
            $this->redirect('/admin/teams/create');
            return;
        }

        if ($email !== '' && !$this->validateEmail($email)) {
            $this->flash('error', 'Please provide a valid email address.');
            $this->redirect('/admin/teams/create');
            return;
        }

        if (!$this->validateHexColour($colour)) {
            $colour = '#1a5f2a'; // Default colour if invalid
        }

        $playersText = $this->post('players', '');
        $players = $this->teamModel->parsePlayersFromText($playersText);

        $team = $this->teamModel->create([
            'name' => $name,
            'contact' => $contact,
            'email' => $email,
            'colour' => $colour,
            'players' => $players,
        ]);

        $this->flash('success', 'Team created. ' . $team['name'] . ' is ready to go.');
        $this->redirect('/admin/teams/' . $team['slug']);
    }

    /**
     * Show the form to edit an existing team.
     */
    public function edit(string $slug): void
    {
        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            $this->flash('error', 'Team not found.');
            $this->redirect('/admin/teams');
            return;
        }

        $playersText = $this->teamModel->playersToText($team['players'] ?? []);

        $this->render('teams/edit', [
            'title' => 'Edit ' . $team['name'],
            'currentPage' => 'teams',
            'team' => $team,
            'playersText' => $playersText,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update an existing team.
     */
    public function update(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/teams/' . $slug . '/edit');
            return;
        }

        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            $this->flash('error', 'Team not found.');
            $this->redirect('/admin/teams');
            return;
        }

        $missing = $this->validateRequired(['name']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide a team name.');
            $this->redirect('/admin/teams/' . $slug . '/edit');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $contact = $this->sanitizeString($this->post('contact', ''), 100);
        $email = $this->sanitizeString($this->post('email', ''), 255);
        $colour = $this->post('colour', '#1a5f2a');

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Team name must be between 1 and 100 characters.');
            $this->redirect('/admin/teams/' . $slug . '/edit');
            return;
        }

        if ($email !== '' && !$this->validateEmail($email)) {
            $this->flash('error', 'Please provide a valid email address.');
            $this->redirect('/admin/teams/' . $slug . '/edit');
            return;
        }

        if (!$this->validateHexColour($colour)) {
            $colour = '#1a5f2a'; // Default colour if invalid
        }

        $playersText = $this->post('players', '');
        $players = $this->teamModel->parsePlayersFromText($playersText);

        $this->teamModel->update($team['id'], [
            'name' => $name,
            'contact' => $contact,
            'email' => $email,
            'colour' => $colour,
            'players' => $players,
        ]);

        $newSlug = Team::slugify($name);

        $this->flash('success', 'Team updated.');
        $this->redirect('/admin/teams/' . $newSlug);
    }

    /**
     * Delete a team.
     */
    public function delete(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/teams');
            return;
        }

        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            $this->flash('error', 'Team not found.');
            $this->redirect('/admin/teams');
            return;
        }

        $teamName = $team['name'];
        $this->teamModel->delete($team['id']);

        $this->flash('success', $teamName . ' has been deleted.');
        $this->redirect('/admin/teams');
    }

    /**
     * Delete multiple teams.
     */
    public function deleteMultiple(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/teams');
            return;
        }

        $teamIds = $this->post('team_ids', []);

        if (!is_array($teamIds) || empty($teamIds)) {
            $this->flash('error', 'No teams selected.');
            $this->redirect('/admin/teams');
            return;
        }

        $deletedCount = 0;
        foreach ($teamIds as $id) {
            if ($this->teamModel->delete($id)) {
                $deletedCount++;
            }
        }

        $message = $deletedCount . ' team' . ($deletedCount !== 1 ? 's' : '') . ' deleted successfully.';
        $this->flash('success', $message);
        $this->redirect('/admin/teams');
    }
}
