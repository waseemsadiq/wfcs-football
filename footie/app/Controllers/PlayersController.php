<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Models\Season;

/**
 * Controller for managing football players.
 * Handles all player CRUD operations and views.
 */
class PlayersController extends Controller
{
    private Player $playerModel;
    private Team $teamModel;

    public function __construct()
    {
        parent::__construct();
        $this->playerModel = new Player();
        $this->teamModel = new Team();
    }

    /**
     * Display a list of all players.
     */
    public function index(): void
    {
        $teamId = $this->get('team_id');
        $pool = $this->get('pool', '0') === '1';

        if ($pool) {
            $players = $this->playerModel->getPoolPlayers();
        } elseif ($teamId) {
            $players = $this->playerModel->getByTeam($teamId);
        } else {
            $players = $this->playerModel->all();
        }

        $teams = $this->teamModel->allSorted();

        $this->render('players/index', [
            'title' => $pool ? 'Pool Players' : 'Players',
            'currentPage' => 'players',
            'players' => $players,
            'teams' => $teams,
            'selectedTeamId' => $teamId,
            'pool' => $pool,
        ]);
    }

    /**
     * Show a single player with full details.
     */
    public function show(string $slug): void
    {
        $player = $this->playerModel->findWhere('slug', $slug);

        if (!$player) {
            $this->flash('error', 'Player not found.');
            $this->redirect('/admin/players');
            return;
        }

        $playerWithTeam = $this->playerModel->getWithTeam($player['id']);
        $stats = $this->playerModel->getStats($player['id']);
        $events = $this->playerModel->getEvents($player['id']);

        $this->render('players/show', [
            'title' => $player['name'],
            'currentPage' => 'players',
            'player' => $playerWithTeam,
            'stats' => $stats,
            'events' => $events,
        ]);
    }

    /**
     * Show the form to create a new player.
     */
    public function create(): void
    {
        $teams = $this->teamModel->allSorted();

        $this->render('players/create', [
            'title' => 'Add Player',
            'currentPage' => 'players',
            'teams' => $teams,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new player in the database.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/players/create');
            return;
        }

        $missing = $this->validateRequired(['name']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide a player name.');
            $this->redirect('/admin/players/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $teamId = $this->post('teamId', null);
        $position = $this->post('position', null);
        $squadNumber = $this->post('squadNumber', null);
        $status = $this->post('status', 'active');
        $isPoolPlayer = $this->post('isPoolPlayer', '0') === '1';

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Player name must be between 1 and 100 characters.');
            $this->redirect('/admin/players/create');
            return;
        }

        // Validate team ID if provided
        if ($teamId !== null && $teamId !== '' && !$isPoolPlayer) {
            $teamId = (int) $teamId;
            $team = $this->teamModel->find($teamId);
            if (!$team) {
                $this->flash('error', 'Invalid team selected.');
                $this->redirect('/admin/players/create');
                return;
            }
        } else {
            $teamId = null;
        }

        // Validate position
        if ($position !== null && $position !== '') {
            $validPositions = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];
            if (!in_array($position, $validPositions)) {
                $this->flash('error', 'Invalid position selected.');
                $this->redirect('/admin/players/create');
                return;
            }
        }

        // Validate squad number uniqueness per team
        if ($squadNumber !== null && $squadNumber !== '' && $teamId !== null) {
            $squadNumber = (int) $squadNumber;
            if ($squadNumber < 1 || $squadNumber > 99) {
                $this->flash('error', 'Squad number must be between 1 and 99.');
                $this->redirect('/admin/players/create');
                return;
            }

            if (!$this->playerModel->isSquadNumberAvailable($squadNumber, $teamId)) {
                $this->flash('error', 'Squad number ' . $squadNumber . ' is already taken by another player on this team.');
                $this->redirect('/admin/players/create');
                return;
            }
        } else {
            $squadNumber = null;
        }

        // Validate status
        $validStatuses = ['active', 'injured', 'suspended', 'unavailable'];
        if (!in_array($status, $validStatuses)) {
            $status = 'active';
        }

        $player = $this->playerModel->create([
            'name' => $name,
            'team_id' => $teamId,
            'position' => $position,
            'squad_number' => $squadNumber,
            'status' => $status,
            'is_pool_player' => $isPoolPlayer ? 1 : 0,
        ]);

        $this->flash('success', 'Player created. ' . $player['name'] . ' is ready to play.');
        $this->redirect('/admin/players/' . $player['slug']);
    }

    /**
     * Show the form to edit an existing player.
     */
    public function edit(string $slug): void
    {
        $player = $this->playerModel->findWhere('slug', $slug);

        if (!$player) {
            $this->flash('error', 'Player not found.');
            $this->redirect('/admin/players');
            return;
        }

        $teams = $this->teamModel->allSorted();

        $this->render('players/edit', [
            'title' => 'Edit ' . $player['name'],
            'currentPage' => 'players',
            'player' => $player,
            'teams' => $teams,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update an existing player.
     */
    public function update(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/players/' . $slug . '/edit');
            return;
        }

        $player = $this->playerModel->findWhere('slug', $slug);

        if (!$player) {
            $this->flash('error', 'Player not found.');
            $this->redirect('/admin/players');
            return;
        }

        $missing = $this->validateRequired(['name']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide a player name.');
            $this->redirect('/admin/players/' . $slug . '/edit');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $teamId = $this->post('teamId', null);
        $position = $this->post('position', null);
        $squadNumber = $this->post('squadNumber', null);
        $status = $this->post('status', 'active');
        $isPoolPlayer = $this->post('isPoolPlayer', '0') === '1';

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Player name must be between 1 and 100 characters.');
            $this->redirect('/admin/players/' . $slug . '/edit');
            return;
        }

        // Validate team ID if provided
        if ($teamId !== null && $teamId !== '' && !$isPoolPlayer) {
            $teamId = (int) $teamId;
            $team = $this->teamModel->find($teamId);
            if (!$team) {
                $this->flash('error', 'Invalid team selected.');
                $this->redirect('/admin/players/' . $slug . '/edit');
                return;
            }
        } else {
            $teamId = null;
        }

        // Validate position
        if ($position !== null && $position !== '') {
            $validPositions = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];
            if (!in_array($position, $validPositions)) {
                $this->flash('error', 'Invalid position selected.');
                $this->redirect('/admin/players/' . $slug . '/edit');
                return;
            }
        }

        // Validate squad number uniqueness per team
        if ($squadNumber !== null && $squadNumber !== '' && $teamId !== null) {
            $squadNumber = (int) $squadNumber;
            if ($squadNumber < 1 || $squadNumber > 99) {
                $this->flash('error', 'Squad number must be between 1 and 99.');
                $this->redirect('/admin/players/' . $slug . '/edit');
                return;
            }

            if (!$this->playerModel->isSquadNumberAvailable($squadNumber, $teamId, $player['id'])) {
                $this->flash('error', 'Squad number ' . $squadNumber . ' is already taken by another player on this team.');
                $this->redirect('/admin/players/' . $slug . '/edit');
                return;
            }
        } else {
            $squadNumber = null;
        }

        // Validate status
        $validStatuses = ['active', 'injured', 'suspended', 'unavailable'];
        if (!in_array($status, $validStatuses)) {
            $status = 'active';
        }

        // If slug changed we need to regenerate it
        $updateData = [
            'name' => $name,
            'team_id' => $teamId,
            'position' => $position,
            'squad_number' => $squadNumber,
            'status' => $status,
            'is_pool_player' => $isPoolPlayer ? 1 : 0,
        ];

        $this->playerModel->update($player['id'], $updateData);

        $updatedPlayer = $this->playerModel->find($player['id']);
        $newSlug = $updatedPlayer['slug'];

        $this->flash('success', 'Player updated.');
        $this->redirect('/admin/players/' . $newSlug);
    }

    /**
     * Delete a player.
     */
    public function delete(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/players');
            return;
        }

        $player = $this->playerModel->findWhere('slug', $slug);

        if (!$player) {
            $this->flash('error', 'Player not found.');
            $this->redirect('/admin/players');
            return;
        }

        $playerName = $player['name'];
        $this->playerModel->delete($player['id']);

        $this->flash('success', $playerName . ' has been deleted.');
        $this->redirect('/admin/players');
    }

    /**
     * Delete multiple players.
     */
    public function deleteMultiple(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/players');
            return;
        }

        $playerIds = $this->post('player_ids', []);

        if (!is_array($playerIds) || empty($playerIds)) {
            $this->flash('error', 'No players selected.');
            $this->redirect('/admin/players');
            return;
        }

        $deletedCount = 0;
        foreach ($playerIds as $id) {
            if ($this->playerModel->delete($id)) {
                $deletedCount++;
            }
        }

        $message = $deletedCount . ' player' . ($deletedCount !== 1 ? 's' : '') . ' deleted successfully.';
        $this->flash('success', $message);
        $this->redirect('/admin/players');
    }
}
