<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Model;
use App\Models\Team;
use App\Models\Season;

/**
 * CompetitionController - Abstract base for League and Cup controllers.
 *
 * Provides shared CRUD operations, validation, and fixture management
 * for competition-based entities (leagues, cups).
 */
abstract class CompetitionController extends Controller
{
    /**
     * @var Model|\App\Models\League|\App\Models\Cup
     */
    protected Model $competition;
    protected Team $team;
    protected Season $season;

    /**
     * Get the entity type name (singular, lowercase).
     * E.g., 'league', 'cup'
     */
    abstract protected function getEntityType(): string;

    /**
     * Get the entity type name (plural, lowercase).
     * E.g., 'leagues', 'cups'
     */
    abstract protected function getEntityTypePlural(): string;

    /**
     * Get the view path prefix.
     * E.g., 'leagues', 'cups'
     */
    abstract protected function getViewPrefix(): string;

    /**
     * Get additional data for index view.
     * Override in child classes to add entity-specific data.
     */
    protected function getIndexAdditionalData(array $items): array
    {
        return [];
    }

    /**
     * Get additional data for create view.
     * Override in child classes to add entity-specific data.
     */
    protected function getCreateAdditionalData(): array
    {
        return [];
    }

    /**
     * Get additional data for edit view.
     * Override in child classes to add entity-specific data.
     */
    protected function getEditAdditionalData(array $entity): array
    {
        return [];
    }

    /**
     * List all competitions.
     */
    public function index(): void
    {
        $items = $this->competition->all();

        // Add season names
        foreach ($items as &$item) {
            $season = $this->season->find($item['seasonId'] ?? '');
            $item['seasonName'] = $season['name'] ?? 'Unknown';
        }

        $plural = $this->getEntityTypePlural();

        $this->render($this->getViewPrefix() . '/index', array_merge([
            'title' => ucfirst($plural),
            'currentPage' => $plural,
            $plural => $items,
        ], $this->getIndexAdditionalData($items)));
    }

    /**
     * Show create form.
     */
    public function create(): void
    {
        $seasons = $this->season->all();
        $teams = $this->team->all();

        $plural = $this->getEntityTypePlural();

        $this->render($this->getViewPrefix() . '/create', array_merge([
            'title' => 'Create ' . ucfirst($this->getEntityType()),
            'currentPage' => $plural,
            'seasons' => $seasons,
            'teams' => $teams,
            'csrfToken' => $this->csrfToken(),
        ], $this->getCreateAdditionalData()));
    }

    /**
     * Store a new competition.
     */
    public function store(): void
    {
        $entityType = $this->getEntityType();
        $plural = $this->getEntityTypePlural();
        $redirectPath = "/admin/{$plural}";

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect($redirectPath . '/create');
            return;
        }

        $missing = $this->validateRequired(['name', 'seasonId']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields: ' . implode(', ', $missing));
            $this->redirect($redirectPath . '/create');
            return;
        }

        $name = $this->sanitizeString($this->post('name'));
        $seasonId = $this->post('seasonId');
        $startDate = $this->post('startDate', date('Y-m-d'));
        $frequency = $this->normalizeFrequency($this->post('frequency', 'weekly'));
        $matchTime = $this->normalizeTime($this->post('matchTime', '15:00'));
        $teamIds = $this->post('teamIds', []);

        if (!is_array($teamIds)) {
            $teamIds = [];
        }

        $record = $this->prepareStoreData([
            'name' => $name,
            'season_id' => $seasonId,
            'start_date' => $startDate,
            'frequency' => $frequency,
            'match_time' => $matchTime,
            'team_ids' => $teamIds,
        ]);

        try {
            $entity = $this->competition->create($record);
            $this->flash('success', ucfirst($entityType) . ' created successfully.');
            $this->redirect($redirectPath . '/' . ($entity['slug'] ?? $entity['id']));
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to create ' . $entityType . ': ' . $e->getMessage());
            $this->redirect($redirectPath . '/create');
        }
    }

    /**
     * Prepare data before storing.
     * Override in child classes for entity-specific processing.
     */
    protected function prepareStoreData(array $data): array
    {
        return $data;
    }

    /**
     * Show edit form.
     */
    public function edit(string $slug): void
    {
        $entity = $this->competition->findWhere('slug', $slug);

        if (!$entity) {
            $this->flash('error', ucfirst($this->getEntityType()) . ' not found.');
            $this->redirect('/admin/' . $this->getEntityTypePlural());
            return;
        }

        $seasons = $this->season->all();
        $teams = $this->team->all();
        $plural = $this->getEntityTypePlural();

        $this->render($this->getViewPrefix() . '/edit', array_merge([
            'title' => 'Edit ' . ucfirst($this->getEntityType()),
            'currentPage' => $plural,
            $this->getEntityType() => $entity,
            'seasons' => $seasons,
            'teams' => $teams,
            'selectedTeamIds' => $entity['teamIds'] ?? [],
            'csrfToken' => $this->csrfToken(),
            'basePath' => $this->basePath,
        ], $this->getEditAdditionalData($entity)));
    }

    /**
     * Update a competition.
     */
    public function update(string $slug): void
    {
        $entityType = $this->getEntityType();
        $plural = $this->getEntityTypePlural();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect("/admin/{$plural}/{$slug}/edit");
            return;
        }

        $entity = $this->competition->findWhere('slug', $slug);

        if (!$entity) {
            $this->flash('error', ucfirst($entityType) . ' not found.');
            $this->redirect("/admin/{$plural}");
            return;
        }

        $missing = $this->validateRequired(['name']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields: ' . implode(', ', $missing));
            $this->redirect("/admin/{$plural}/{$slug}/edit");
            return;
        }

        // Handle team changes
        $teamIds = $this->post('teamIds', []);
        if (!is_array($teamIds)) {
            $teamIds = [];
        }

        // Validate minimum 2 teams
        if (count($teamIds) < 2) {
            $this->flash('error', 'A competition must have at least 2 teams.');
            $this->redirect("/admin/{$plural}/{$slug}/edit");
            return;
        }

        // Check if teams changed
        $currentTeamIds = $entity['teamIds'] ?? [];
        sort($currentTeamIds);
        $newTeamIds = $teamIds;
        sort($newTeamIds);
        $teamsChanged = $currentTeamIds !== $newTeamIds;

        $name = $this->sanitizeString($this->post('name'));
        $startDate = $this->post('startDate');
        $frequency = $this->normalizeFrequency($this->post('frequency', 'weekly'));
        $matchTime = $this->normalizeTime($this->post('matchTime', '15:00'));

        $updates = $this->prepareUpdateData([
            'name' => $name,
            'start_date' => $startDate,
            'frequency' => $frequency,
            'match_time' => $matchTime,
        ], $entity);

        try {
            // Update team associations
            $this->competition->setTeams($entity['id'], $teamIds);

            // Auto-regenerate unplayed fixtures if teams changed
            if ($teamsChanged) {
                $this->regenerateUnplayedFixtures($entity, $teamIds);
            }

            $this->competition->update($entity['id'], $updates);
            $newSlug = $this->competition->find($entity['id'])['slug'] ?? $slug;
            $this->flash('success', ucfirst($entityType) . ' updated successfully.');
            $this->redirect("/admin/{$plural}/{$newSlug}");
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to update ' . $entityType . ': ' . $e->getMessage());
            $this->redirect("/admin/{$plural}/{$slug}/edit");
        }
    }

    /**
     * Prepare data before updating.
     * Override in child classes for entity-specific processing.
     */
    protected function prepareUpdateData(array $data, array $currentEntity): array
    {
        return $data;
    }

    /**
     * Regenerate unplayed fixtures when teams change.
     * For leagues: deletes unplayed fixtures and regenerates with new team list.
     * For cups: reschedules unplayed fixtures or regenerates bracket if needed.
     */
    protected function regenerateUnplayedFixtures(array $entity, array $teamIds): void
    {
        $entityType = $this->getEntityType();

        if ($entityType === 'league') {
            // For leagues: delete unplayed, regenerate all fixtures
            $this->competition->deleteUnplayedFixtures($entity['id']);
            $this->competition->generateFixtures(
                $entity['id'],
                $teamIds,
                $entity['startDate'] ?? date('Y-m-d'),
                $entity['frequency'] ?? 'weekly',
                $entity['matchTime'] ?? '15:00',
                false  // Don't delete existing (already deleted unplayed only)
            );
        } elseif ($entityType === 'cup') {
            // For cups: regenerate the entire bracket (preserves played matches in generateBracket)
            $this->competition->generateBracket(
                $entity['id'],
                $teamIds,
                $entity['startDate'] ?? date('Y-m-d'),
                $entity['frequency'] ?? 'weekly',
                $entity['matchTime'] ?? '15:00'
            );
        }
    }

    /**
     * Delete a competition.
     */
    public function delete(string $slug): void
    {
        $entityType = $this->getEntityType();
        $plural = $this->getEntityTypePlural();

        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect("/admin/{$plural}");
            return;
        }

        $entity = $this->competition->findWhere('slug', $slug);

        if ($entity) {
            $seasonId = $entity['seasonId'];
            $this->competition->delete($entity['id']);

            // Remove from season (no-op in current implementation but kept for consistency)
            if ($entityType === 'league') {
                $this->season->removeLeague($seasonId, $entity['id']);
            } elseif ($entityType === 'cup') {
                $this->season->removeCup($seasonId, $entity['id']);
            }

            $this->flash('success', ucfirst($entityType) . ' deleted successfully.');
        } else {
            $this->flash('error', ucfirst($entityType) . ' not found.');
        }

        $this->redirect("/admin/{$plural}");
    }
    /**
     * Parse structured scorer inputs.
     */
    protected function parseScorersInput(array $post, string $prefix): array
    {
        $input = $post[$prefix] ?? [];
        if (!is_array($input)) {
            return [];
        }

        $scorers = [];
        foreach ($input as $row) {
            $player = isset($row['player']) ? trim($row['player']) : '';
            $minute = isset($row['minute']) ? trim($row['minute']) : '';
            $ownGoal = isset($row['ownGoal']);

            if (!empty($player)) {
                $item = ['player' => $player, 'minute' => $minute];
                if ($ownGoal) {
                    $item['ownGoal'] = true;
                }
                $scorers[] = $item;
            }
        }
        return $scorers;
    }

    /**
     * Parse structured card inputs.
     */
    protected function parseCardsInput(array $post, string $prefix): array
    {
        $input = $post[$prefix . 'CardsCombined'] ?? [];
        $cards = [
            'sinBins' => [],
            'blue' => [],
            'yellow' => [],
            'red' => []
        ];

        if (is_array($input)) {
            foreach ($input as $row) {
                $type = $row['type'] ?? '';
                $player = isset($row['player']) ? trim($row['player']) : '';
                $minute = isset($row['minute']) ? trim($row['minute']) : '';

                if (!empty($player) && isset($cards[$type])) {
                    $cards[$type][] = [
                        'player' => $player,
                        'minute' => $minute
                    ];
                }
            }
        }

        return $cards;
    }

    /**
     * Convert parsed scorers and cards into match events and save to database.
     * Matches player names to player IDs and creates structured event records.
     */
    protected function saveMatchEvents(
        string $fixtureType,
        int $fixtureId,
        int $homeTeamId,
        int $awayTeamId,
        array $homeScorers,
        array $awayScorers,
        array $homeCards,
        array $awayCards
    ): void {
        $events = [];
        $playerModel = new \App\Models\Player();

        // Process home scorers
        foreach ($homeScorers as $scorer) {
            $playerId = $this->findPlayerIdByName($scorer['player'], $homeTeamId, $playerModel);
            $minute = !empty($scorer['minute']) ? (int) $scorer['minute'] : null;
            $ownGoal = $scorer['ownGoal'] ?? false;

            $events[] = [
                'fixture_type' => $fixtureType,
                'fixture_id' => $fixtureId,
                'team_id' => $homeTeamId,
                'player_id' => $playerId,
                'event_type' => 'goal',
                'minute' => $minute,
                'notes' => $ownGoal ? 'og' : null,
            ];
        }

        // Process away scorers
        foreach ($awayScorers as $scorer) {
            $playerId = $this->findPlayerIdByName($scorer['player'], $awayTeamId, $playerModel);
            $minute = !empty($scorer['minute']) ? (int) $scorer['minute'] : null;
            $ownGoal = $scorer['ownGoal'] ?? false;

            $events[] = [
                'fixture_type' => $fixtureType,
                'fixture_id' => $fixtureId,
                'team_id' => $awayTeamId,
                'player_id' => $playerId,
                'event_type' => 'goal',
                'minute' => $minute,
                'notes' => $ownGoal ? 'og' : null,
            ];
        }

        // Process home cards
        $this->processCardsForTeam($events, $homeCards, $fixtureType, $fixtureId, $homeTeamId, $playerModel);

        // Process away cards
        $this->processCardsForTeam($events, $awayCards, $fixtureType, $fixtureId, $awayTeamId, $playerModel);

        // Save all events to database
        $matchEventModel = new \App\Models\MatchEvent();
        $matchEventModel->replaceFixtureEvents($fixtureType, $fixtureId, $events);
    }

    /**
     * Process cards for a specific team and add to events array.
     */
    private function processCardsForTeam(
        array &$events,
        array $cards,
        string $fixtureType,
        int $fixtureId,
        int $teamId,
        \App\Models\Player $playerModel
    ): void {
        $typeMapping = [
            'yellow' => 'yellow_card',
            'red' => 'red_card',
            'blue' => 'blue_card',
            'sinBins' => 'sin_bin'
        ];

        foreach ($typeMapping as $formType => $eventType) {
            foreach ($cards[$formType] ?? [] as $card) {
                $playerId = $this->findPlayerIdByName($card['player'], $teamId, $playerModel);
                $minute = !empty($card['minute']) ? (int) $card['minute'] : null;

                $events[] = [
                    'fixture_type' => $fixtureType,
                    'fixture_id' => $fixtureId,
                    'team_id' => $teamId,
                    'player_id' => $playerId,
                    'event_type' => $eventType,
                    'minute' => $minute,
                    'notes' => null,
                ];
            }
        }
    }

    /**
     * Find player ID by name using the Player model's fuzzy matching.
     * Returns null if player not found (for own goals, unknown players, etc.)
     */
    private function findPlayerIdByName(string $playerName, int $teamId, \App\Models\Player $playerModel): ?int
    {
        return $playerModel->findIdByNameInTeam($playerName, $teamId);
    }
}
