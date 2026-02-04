---
name: footie-admin
description: Administer the Footie App database (CRUD) using ephemeral PHP scripts. Supports Local and Remote (SSH) execution.
---

# Footie Admin Skill

This skill allows you to admin the database (Add/Edit/Delete/List Teams, Seasons, etc.) by writing and executing ephemeral PHP scripts. It works in a "Zero-Touch" manner, meaning it creates temporary files and deletes them immediately after execution.

## When to Use

Use this skill when the user asks to:

- "Add a new team called X"
- "Update the colour of team Y"
- "List all players in the database"
- "Create a new Season"
- "Debug the database data"

## Workflow

### 1. Determine Target Environment

Ask the user (or infer from context) if this action is **Local** or **Remote**.

- **Local**: Run commands directly on the machine.
- **Remote**: Requires `SSH_HOST` (e.g., `user@ip`) and `REMOTE_PATH` (e.g., `/var/www/html/footie`). If these are not known, ASK the user.

### 2. Generate Unique ID

Generate a short 4-character random string to use as an ID for this run (e.g., `a7x9`). This prevents file collisions.

### 3. Workflow Steps

#### A. Define Paths

- **Asset Source**: `.agent/skills/footie-admin/assets/console-bootstrap.php`
- **Bootstrap Target**: `{APP_ROOT}/bootstrap_{ID}.php`
- **Task Target**: `{APP_ROOT}/task_{ID}.php`

> **Note**: For Remote execution, `{APP_ROOT}` refers to `{REMOTE_PATH}`.

#### B. Generate The Task Script

Write a PHP script that performs the user's requested action.
**Crucial Rules**:

1.  **Require Bootstrap**: The FIRST line must be: `require __DIR__ . '/bootstrap_{ID}.php';`
2.  **Use Abstractions**: Assume the Models exist. Do not redefine them.
3.  **Output Results**: Use `echo` to print what happened (e.g., "Team X created with ID 5").
4.  **Reference Models**: See `references/models.md` for syntax.

#### C. Execute (Local)

1.  **Copy Bootstrap**: `cp .agent/skills/footie-admin/assets/console-bootstrap.php {APP_ROOT}/bootstrap_{ID}.php`
    - _Do NOT read the file content._
2.  **Save Task Script**: Write the generated PHP code to `{APP_ROOT}/task_{ID}.php`.
3.  **Run**: `php {APP_ROOT}/task_{ID}.php`
4.  **Cleanup**: `rm {APP_ROOT}/bootstrap_{ID}.php {APP_ROOT}/task_{ID}.php`

#### D. Execute (Remote / SSH)

1.  **Upload Bootstrap**: `scp .agent/skills/footie-admin/assets/console-bootstrap.php {SSH_HOST}:{REMOTE_PATH}/bootstrap_{ID}.php`
    - _Do NOT read the file content._
2.  **Upload Task Script**:
    - Save generated code to a local temp file `temp_{ID}.php`.
    - `scp temp_{ID}.php {SSH_HOST}:{REMOTE_PATH}/task_{ID}.php`
    - `rm temp_{ID}.php` (local cleanup)
3.  **Run**: `ssh {SSH_HOST} "php {REMOTE_PATH}/task_{ID}.php"`
4.  **Cleanup**: `ssh {SSH_HOST} "rm {REMOTE_PATH}/bootstrap_{ID}.php {REMOTE_PATH}/task_{ID}.php"`

## Important Notes

- **Do not** try to read `console-bootstrap.php` into the context. It wastes tokens. Just copy/upload it.
- **Do not** leave files behind. Always cleanup in a `finally` block or ensure cleanup commands run even if the PHP script fails.
- For `SSH_HOST` and `REMOTE_PATH`, if the user has provided them once, remember them for the session.
