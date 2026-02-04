---
name: footie-remote-admin
description: "Remote Admin Tool: Manage the Footie App database on a remote server via SSH or SFTP/HTTP. Requires Admin Password."
---

# Footie Remote Admin Skill

This skill is designed for **System Administrators** to manage the live Footie App. It executes PHP scripts on the remote server via SSH to perform actions (CRUD).

**SECURITY NOTICE**: This skill requires the App Administrator's Password to run. The password is verified against the hash stored in the remote `.env` file (`ADMIN_PASSWORD_HASH`).

## Configuration (Edit before packaging)

The administrator should set these values in their copy of `SKILL.md` or provide them when asked.

- **Default SSH Host**: `{{SSH_HOST}}` (e.g. admin@192.168.1.50)
- **Default Remote Path**: `{{REMOTE_PATH}}` (e.g. /var/www/footie)
- **Default Public URL**: `{{PUBLIC_URL}}` (e.g. https://footie.app) - _Required for SFTP/HTTP mode_

## Workflow

### 1. Authentication & Setup

1.  **Ask for Password**: If the user hasn't provided the "App Admin Password" in this session, ASK FOR IT.
2.  **Verify Target**: Confirm `SSH_HOST` and `REMOTE_PATH`. If not strict in constants above, ask the user.

### 2. Generate Logic

1.  **Generate ID**: Create a random ID `xID`.
2.  **Generate Script**: Write the PHP script to perform the task.
    - **HEAD OF SCRIPT**: MUST include the Auth Block (see below).
    - **BODY**: The logic (Models, etc).

#### Auth Block (Must be at the top of every generated script)

```php
<?php
require __DIR__ . '/bootstrap_{ID}.php';

// Auth Check
$providedPass = '{{USER_PROVIDED_PASSWORD}}'; // AI: Inject the password the user gave you
$envHash = getenv('ADMIN_PASSWORD_HASH');

if (!$envHash || !password_verify($providedPass, $envHash)) {
    // Artificial small delay to prevent timing attacks equivalent
    usleep(200000);
    fwrite(STDERR, "ACCESS DENIED: Authentication failed.\n");
    exit(1);
}
// Auth Success
```

### 3. Execution (Deployment)

> **Note**: `{SKILL_DIR}` refers to the directory containing this SKILL.md file.

#### Mode A: SSH Execution (Preferred)

1.  **Upload Bootstrap**: `scp {SKILL_DIR}/assets/console-bootstrap.php {SSH_HOST}:{REMOTE_PATH}/bootstrap_{ID}.php`
2.  **Upload Task**: `scp local_temp_task.php {SSH_HOST}:{REMOTE_PATH}/task_{ID}.php`
3.  **Execute**: `ssh {SSH_HOST} "php {REMOTE_PATH}/task_{ID}.php"`
4.  **Cleanup**: `ssh {SSH_HOST} "rm {REMOTE_PATH}/bootstrap_{ID}.php {REMOTE_PATH}/task_{ID}.php"`

#### Mode B: SFTP + HTTP Execution (Fallback)

_Use when SSH shell is unavailable._

1.  **Add Self-Destruct**: Ensure the task script deletes itself and the bootstrap file using `register_shutdown_function`.
2.  **Upload**: Use `scp` (or sftp) to upload `bootstrap_{ID}.php` and `task_{ID}.php` to `{REMOTE_PATH}`.
3.  **Execute**: `curl -L {PUBLIC_URL}/task_{ID}.php`
4.  **Verify**: Check response body/headers. Files should be deleted automatically.

## Reference

See `references/models.md` for Model usage.
