<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Season;

/**
 * Controller for managing football seasons.
 * Handles all CRUD operations and the active season toggle.
 */
class SeasonsController extends Controller
{
    private Season $season;

    public function __construct()
    {
        parent::__construct();
        $this->season = new Season();
    }

    /**
     * Display all seasons.
     */
    public function index(): void
    {
        $seasons = $this->season->allSorted();

        $this->render('seasons/index', [
            'title' => 'Seasons',
            'currentPage' => 'seasons',
            'seasons' => $seasons,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Show the form to create a new season.
     */
    public function create(): void
    {
        $this->render('seasons/create', [
            'title' => 'Create Season',
            'currentPage' => 'seasons',
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new season.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        $missing = $this->validateRequired(['id', 'name', 'startDate', 'endDate']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields: ' . implode(', ', $missing));
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Validate and sanitize inputs
        $id = $this->sanitizeString($this->post('id'), 50);
        $name = $this->sanitizeString($this->post('name'), 100);
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');
        $isActive = $this->post('isActive') === '1';

        // Validate ID format (alphanumeric, hyphens, underscores only)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            $this->flash('error', 'Season ID can only contain letters, numbers, hyphens, and underscores.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        if (!$this->validateLength($id, 1, 50)) {
            $this->flash('error', 'Season ID must be between 1 and 50 characters.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Season name must be between 1 and 100 characters.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        if (!$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid start date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        if (!$this->validateDate($endDate)) {
            $this->flash('error', 'Invalid end date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Check if ID already exists
        if ($this->season->idExists($id)) {
            $this->flash('error', 'A season with this ID already exists. Please choose a different ID.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Validate dates
        if ($startDate >= $endDate) {
            $this->flash('error', 'The end date must be after the start date.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Create the season
        $this->season->createWithId([
            'id' => $id,
            'name' => $name,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isActive' => false,
            'leagueIds' => [],
            'cupIds' => [],
        ]);

        // Set as active if requested (this deactivates other seasons)
        if ($isActive) {
            $this->season->setActive($id);
        }

        $this->flash('success', 'Season created successfully.');
        $this->redirect('/admin/seasons');
    }

    /**
     * Display a single season.
     */
    public function show(string $slug): void
    {
        $season = $this->season->findWhere('slug', $slug);

        if (!$season) {
            $this->flash('error', 'Season not found.');
            $this->redirect('/admin/seasons');
            return;
        }

        $this->render('seasons/show', [
            'title' => $season['name'],
            'currentPage' => 'seasons',
            'season' => $season,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Show the form to edit a season.
     */
    public function edit(string $slug): void
    {
        $season = $this->season->findWhere('slug', $slug);

        if (!$season) {
            $this->flash('error', 'Season not found.');
            $this->redirect('/admin/seasons');
            return;
        }

        $this->render('seasons/edit', [
            'title' => 'Edit ' . $season['name'],
            'currentPage' => 'seasons',
            'season' => $season,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update an existing season.
     */
    public function update(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        $season = $this->season->findWhere('slug', $slug);

        if (!$season) {
            $this->flash('error', 'Season not found.');
            $this->redirect('/admin/seasons');
            return;
        }

        $missing = $this->validateRequired(['name', 'startDate', 'endDate']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields: ' . implode(', ', $missing));
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Season name must be between 1 and 100 characters.');
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        if (!$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid start date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        if (!$this->validateDate($endDate)) {
            $this->flash('error', 'Invalid end date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        // Validate dates
        if ($startDate >= $endDate) {
            $this->flash('error', 'The end date must be after the start date.');
            $this->redirect('/admin/seasons/' . $slug . '/edit');
            return;
        }

        $this->season->update($season['id'], [
            'name' => $name,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $this->flash('success', 'Season updated successfully.');
        $this->redirect('/admin/seasons');
    }

    /**
     * Delete a season.
     */
    public function delete(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/seasons');
            return;
        }

        $season = $this->season->findWhere('slug', $slug);

        if (!$season) {
            $this->flash('error', 'Season not found.');
            $this->redirect('/admin/seasons');
            return;
        }

        // Check if the season has leagues or cups
        if (!empty($season['leagueIds']) || !empty($season['cupIds'])) {
            $this->flash('error', 'Cannot delete a season with associated leagues or cups. Remove them first.');
            $this->redirect('/admin/seasons');
            return;
        }

        $this->season->delete($season['id']);

        $this->flash('success', 'Season deleted successfully.');
        $this->redirect('/admin/seasons');
    }

    /**
     * Set a season as the active season.
     */
    public function setActive(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/seasons');
            return;
        }

        $season = $this->season->findWhere('slug', $slug);

        if (!$season) {
            $this->flash('error', 'Season not found.');
            $this->redirect('/admin/seasons');
            return;
        }

        $this->season->setActive($season['id']);

        $this->flash('success', $season['name'] . ' is now the active season.');
        $this->redirect('/admin/seasons');
    }
}
