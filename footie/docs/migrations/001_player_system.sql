-- Migration 001: Player Management System - Phase 1
-- Creates enhanced player system with match events and statistics tracking

-- Extend players table with enhanced fields
ALTER TABLE players
  ADD COLUMN slug VARCHAR(100) UNIQUE DEFAULT NULL AFTER name,
  ADD COLUMN position VARCHAR(50) DEFAULT NULL,
  ADD COLUMN squad_number INT DEFAULT NULL,
  ADD COLUMN status ENUM('active', 'injured', 'suspended', 'unavailable') DEFAULT 'active',
  ADD COLUMN is_pool_player BOOLEAN DEFAULT 0,
  ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Make team_id nullable for pool players
ALTER TABLE players MODIFY COLUMN team_id INT DEFAULT NULL;

-- Add unique constraint for squad numbers per team
ALTER TABLE players
  ADD UNIQUE KEY unique_squad_number_per_team (team_id, squad_number);

-- Match events table (replaces JSON TEXT fields in fixtures)
CREATE TABLE match_events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fixture_type ENUM('league', 'cup') NOT NULL,
  fixture_id INT NOT NULL,
  team_id INT NOT NULL,
  player_id INT DEFAULT NULL,  -- NULL for own goals, unknowns
  event_type ENUM('goal', 'yellow_card', 'red_card', 'assist') NOT NULL,
  minute INT DEFAULT NULL,
  notes TEXT DEFAULT NULL,  -- 'og', 'pen', etc.
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_fixture (fixture_type, fixture_id),
  INDEX idx_player (player_id),
  INDEX idx_team (team_id),
  FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE SET NULL,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Player statistics (cached for performance)
CREATE TABLE player_stats (
  player_id INT PRIMARY KEY,
  team_id INT DEFAULT NULL,
  total_goals INT DEFAULT 0,
  total_assists INT DEFAULT 0,
  yellow_cards INT DEFAULT 0,
  red_cards INT DEFAULT 0,
  matches_played INT DEFAULT 0,
  last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Support staff table
CREATE TABLE team_staff (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  role ENUM('coach', 'assistant_coach', 'manager', 'contact', 'other') NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
