# WFCS Football

WFCS Football allows you to manage football clubs, leagues, seasons, and tournaments from a single simple dashboard. You can create competitions, generate match schedules automatically, and track results with ease.

## Getting Started

To get up and running with WFCS Football, you need to set up the application environment and connect it to your database.

### Prerequisites

You will need the following installed on your system:

- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB
- A web server (like Apache)

### Installation

You can install the application using the automated installer script. This script allows you to set up your database and create the necessary tables in one go.

1.  Open your terminal and navigate to the project directory.
2.  Run the installer command:
    ```bash
    ./galvani footie-install.php
    ```
3.  Follow the on-screen prompts to enter your database credentials and set your admin password.

The installer allows you to choose whether to load sample data or start with a fresh database.

### Manual Configuration

If you prefer to configure the connection manually, you can edit the configuration files directly.

1.  Navigate to `footie > config`.
2.  Duplicate the example files:
    - Copy `database-shared.example.php` to `database-shared.php`
    - Copy `app-shared.example.php` to `app-shared.php`
3.  Open `database-shared.php` and enter your database connection details.

## Using the Application

### Admin Access

You can access the admin dashboard to manage your leagues and teams.

1.  Open your browser and navigate to your site URL (e.g., `http://localhost:8000/footie`).
2.  Click the **Admin** link.
3.  Enter the password you created during installation.

### Managing Seasons

The **Season Management** area allows you to organise your competitions by year or period.

1.  Navigate to `Admin > Seasons`.
2.  Select **Add New Season**.
3.  Enter the start and end dates.

You can set a season as "Active" to ensure it appears by default on the public facing site.

### Creating Leagues and Fixtures

WFCS Football allows you to generate impartial fixture lists automatically.

1.  Navigate to `Admin > Leagues`.
2.  Create a new league and add your teams.
3.  Select **Generate Fixtures**.

The system will automatically pair teams for Home and Away matches.

## Troubleshooting

If you encounter issues, here are a few things you can check.

**Database Connection Errors**
If you see a "Connection Failed" message, check your credentials in `footie > config > database-shared.php`. Ensure the username, password, and database name are correct.

**Admin Password**
If you forget your admin password, you can reset it by updating the `ADMIN_PASSWORD_HASH` in your `.env` file. You can generate a new hash using the `generate_password.php` utility.
