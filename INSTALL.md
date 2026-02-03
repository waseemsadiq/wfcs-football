# Footie App Installation

## Basic Installation

```bash
./galvani footie-install.php
```

Interactive prompts will guide you through the setup (press Enter to accept defaults).

---

## Command-Line Flags

### `--app-dir=DIR` (alias: `-d DIR`)
**Default:** `footie`
**Description:** Directory where the app will be installed. The installer will rename the `footie/` directory to your chosen name.

**Example:**
```bash
./galvani footie-install.php --app-dir=sports
# App will be available at: http://localhost:8080/sports/
```

---

### `--db-name=NAME`
**Default:** `wfcs`
**Description:** Name of the MySQL database to create.

**Example:**
```bash
./galvani footie-install.php --db-name=my_league
```

---

### `--load-sample=Y/n`
**Default:** `Y`
**Description:** Load sample data (1 season, 8 teams, 2 leagues, 3 cups, 40 fixtures). Set to `n` to start with an empty database.

**Example:**
```bash
./galvani footie-install.php --load-sample=n
```

---

### `--admin-password=PASS`
**Default:** `admin` (⚠️ **Change in production!**)
**Description:** Set a custom admin password. If not provided, the default password is `admin`.

**Example:**
```bash
./galvani footie-install.php --admin-password=SecurePass123
```

---

### `-h` or `--help`
**Description:** Display help message with all available options.

**Example:**
```bash
./galvani footie-install.php --help
```

---

## Full Example (All Flags)

```bash
./galvani footie-install.php \
  --app-dir=sports \
  --db-name=my_league \
  --admin-password=SecurePass123 \
  --load-sample=Y
```

This will:
- Install to `sports/` directory
- Create database named `my_league`
- Set admin password to `SecurePass123`
- Load all sample data

---

## After Installation

1. **Start the server:**
   ```bash
   ./galvani
   ```

2. **Visit your app:**
   ```
   http://localhost:8080/{app-dir}/
   ```

3. **Admin login:**
   - Password: Whatever you set (or `admin` if using default)
