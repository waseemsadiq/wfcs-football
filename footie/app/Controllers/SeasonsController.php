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

        $missing = $this->validateRequired(['name', 'startDate', 'endDate']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields: ' . implode(', ', $missing));
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');
        $isActive = $this->post('isActive') === '1';

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

        // Validate dates
        if ($startDate >= $endDate) {
            $this->flash('error', 'The end date must be after the start date.');
            $this->redirect('/admin/seasons/create');
            return;
        }

        // Create the season with unique slug
        $slug = $this->season->generateUniqueSlug($name);
        $result = $this->season->create([
            'name' => $name,
            'slug' => $slug,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $isActive ? 1 : 0,
        ]);

        // Retrieve the new ID to handle activation
        // Model::create may return the ID or the record array depending on implementation
        if (is_array($result) && isset($result['id'])) {
            $createdId = $result['id'];
        } elseif (is_numeric($result)) {
            $createdId = $result;
        } else {
            // Fallback: find by slug if ID not returned directly
            $newSeason = $this->season->findWhere('slug', $slug);
            $createdId = $newSeason['id'] ?? null;
        }

        if ($isActive && $createdId) {
            $this->season->setActive($createdId);
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
            'start_date' => $startDate,
            'end_date' => $endDate,
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
