<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\TeamStaff;
use App\Models\Team;

/**
 * Controller for managing team support staff.
 * Handles all staff CRUD operations and views.
 */
class StaffController extends Controller
{
    private TeamStaff $staffModel;
    private Team $teamModel;

    public function __construct()
    {
        parent::__construct();
        $this->staffModel = new TeamStaff();
        $this->teamModel = new Team();
    }

    /**
     * Display a list of all staff.
     */
    public function index(): void
    {
        $page = max(1, (int) $this->get('page', 1));
        $perPage = 20;
        $teamId = $this->get('team_id');
        $role = $this->get('role');

        // Build where conditions
        $where = [];
        if ($teamId) {
            $where['team_id'] = (int) $teamId;
        }
        if ($role && $this->staffModel->isValidRole($role)) {
            $where['role'] = $role;
        }

        // Get total count
        $totalCount = $this->staffModel->count($where);

        // Calculate pagination
        $pagination = $this->paginate($totalCount, $page, $perPage);

        // Get paginated staff
        $staff = $this->staffModel->paginate($perPage, $pagination['offset'], $where, 'name', 'ASC');

        // Enrich with team data
        $teamModel = $this->teamModel;
        $staff = array_map(function ($member) use ($teamModel) {
            $team = $teamModel->find($member['teamId']);
            $member['team'] = $team;
            return $member;
        }, $staff);

        $teams = $this->teamModel->all();

        $this->render('staff/index', [
            'title' => 'Support Staff',
            'currentPage' => 'staff',
            'staff' => $staff,
            'teams' => $teams,
            'selectedTeamId' => $teamId,
            'selectedRole' => $role,
            'roles' => TeamStaff::getValidRoles(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * Show a single staff member with full details.
     */
    public function show(int $id): void
    {
        $staff = $this->staffModel->getWithTeam($id);

        if (!$staff) {
            $this->flash('error', 'Staff member not found.');
            $this->redirect('/admin/staff');
            return;
        }

        $this->render('staff/show', [
            'title' => $staff['name'],
            'currentPage' => 'staff',
            'staff' => $staff,
        ]);
    }

    /**
     * Show the form to create a new staff member.
     */
    public function create(): void
    {
        $teams = $this->teamModel->all();
        $selectedTeamId = $this->get('team_id');

        $this->render('staff/create', [
            'title' => 'Add Staff Member',
            'currentPage' => 'staff',
            'teams' => $teams,
            'roles' => TeamStaff::getValidRoles(),
            'selectedTeamId' => $selectedTeamId,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new staff member in the database.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/staff/create');
            return;
        }

        $missing = $this->validateRequired(['name', 'team_id', 'role']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide name, team, and role.');
            $this->redirect('/admin/staff/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $teamId = (int) $this->post('team_id');
        $role = $this->post('role');
        $phone = $this->sanitizeString($this->post('phone', ''), 50);
        $email = $this->sanitizeString($this->post('email', ''), 100);

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Name must be between 1 and 100 characters.');
            $this->redirect('/admin/staff/create');
            return;
        }

        // Validate team exists
        $team = $this->teamModel->find($teamId);
        if (!$team) {
            $this->flash('error', 'Invalid team selected.');
            $this->redirect('/admin/staff/create');
            return;
        }

        // Validate role
        if (!$this->staffModel->isValidRole($role)) {
            $this->flash('error', 'Invalid role selected.');
            $this->redirect('/admin/staff/create');
            return;
        }

        // Validate email if provided
        if ($email !== '' && !$this->validateEmail($email)) {
            $this->flash('error', 'Please provide a valid email address.');
            $this->redirect('/admin/staff/create');
            return;
        }

        $staff = $this->staffModel->create([
            'name' => $name,
            'team_id' => $teamId,
            'role' => $role,
            'phone' => $phone ?: null,
            'email' => $email ?: null,
        ]);

        $this->flash('success', $staff['name'] . ' added to ' . $team['name'] . '.');
        $this->redirect('/admin/staff/' . $staff['id']);
    }

    /**
     * Show the form to edit an existing staff member.
     */
    public function edit(int $id): void
    {
        $staff = $this->staffModel->find($id);

        if (!$staff) {
            $this->flash('error', 'Staff member not found.');
            $this->redirect('/admin/staff');
            return;
        }

        $teams = $this->teamModel->all();

        $this->render('staff/edit', [
            'title' => 'Edit ' . $staff['name'],
            'currentPage' => 'staff',
            'staff' => $staff,
            'teams' => $teams,
            'roles' => TeamStaff::getValidRoles(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update an existing staff member.
     */
    public function update(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        $staff = $this->staffModel->find($id);

        if (!$staff) {
            $this->flash('error', 'Staff member not found.');
            $this->redirect('/admin/staff');
            return;
        }

        $missing = $this->validateRequired(['name', 'team_id', 'role']);
        if (!empty($missing)) {
            $this->flash('error', 'Please provide name, team, and role.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $teamId = (int) $this->post('team_id');
        $role = $this->post('role');
        $phone = $this->sanitizeString($this->post('phone', ''), 50);
        $email = $this->sanitizeString($this->post('email', ''), 100);

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Name must be between 1 and 100 characters.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        // Validate team exists
        $team = $this->teamModel->find($teamId);
        if (!$team) {
            $this->flash('error', 'Invalid team selected.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        // Validate role
        if (!$this->staffModel->isValidRole($role)) {
            $this->flash('error', 'Invalid role selected.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        // Validate email if provided
        if ($email !== '' && !$this->validateEmail($email)) {
            $this->flash('error', 'Please provide a valid email address.');
            $this->redirect('/admin/staff/' . $id . '/edit');
            return;
        }

        $this->staffModel->update($id, [
            'name' => $name,
            'team_id' => $teamId,
            'role' => $role,
            'phone' => $phone ?: null,
            'email' => $email ?: null,
        ]);

        $this->flash('success', 'Staff member updated.');
        $this->redirect('/admin/staff/' . $id);
    }

    /**
     * Delete a staff member.
     */
    public function delete(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/staff');
            return;
        }

        $staff = $this->staffModel->find($id);

        if (!$staff) {
            $this->flash('error', 'Staff member not found.');
            $this->redirect('/admin/staff');
            return;
        }

        $staffName = $staff['name'];
        $this->staffModel->delete($id);

        $this->flash('success', $staffName . ' has been removed.');
        $this->redirect('/admin/staff');
    }

    /**
     * Delete multiple staff members.
     */
    public function deleteMultiple(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/staff');
            return;
        }

        $staffIds = $this->post('staff_ids', []);

        if (!is_array($staffIds) || empty($staffIds)) {
            $this->flash('error', 'No staff members selected.');
            $this->redirect('/admin/staff');
            return;
        }

        $deletedCount = 0;
        foreach ($staffIds as $id) {
            if ($this->staffModel->delete((int) $id)) {
                $deletedCount++;
            }
        }

        $message = $deletedCount . ' staff member' . ($deletedCount !== 1 ? 's' : '') . ' removed successfully.';
        $this->flash('success', $message);
        $this->redirect('/admin/staff');
    }

    /**
     * AJAX endpoint to get filtered staff list.
     */
    public function getStaffList(): void
    {
        $page = max(1, (int) $this->get('page', 1));
        $perPage = 20;
        $teamId = $this->get('team_id');
        $role = $this->get('role');

        // Build where conditions
        $where = [];
        if ($teamId) {
            $where['team_id'] = (int) $teamId;
        }
        if ($role && $this->staffModel->isValidRole($role)) {
            $where['role'] = $role;
        }

        // Get total count
        $totalCount = $this->staffModel->count($where);

        // Calculate pagination
        $pagination = $this->paginate($totalCount, $page, $perPage);

        // Get paginated staff
        $staff = $this->staffModel->paginate($perPage, $pagination['offset'], $where, 'name', 'ASC');

        // Enrich with team data
        $teamModel = $this->teamModel;
        $staff = array_map(function ($member) use ($teamModel) {
            $team = $teamModel->find($member['teamId']);
            $member['team'] = $team;
            return $member;
        }, $staff);

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\\\');

        $this->renderPartial('staff/staff_table', [
            'staff' => $staff,
            'basePath' => $basePath,
            'pagination' => $pagination,
        ]);
    }
}
