-- Migration 007: Fixture Details Schema
-- Add rich content fields for fixture detail pages

-- Add status and rich content fields to league_fixtures
ALTER TABLE league_fixtures
  ADD COLUMN status ENUM('scheduled','in_progress','completed','postponed','cancelled') DEFAULT 'scheduled' AFTER is_live,
  ADD COLUMN match_report TEXT NULL AFTER status,
  ADD COLUMN live_stream_url VARCHAR(500) NULL AFTER match_report,
  ADD COLUMN full_match_url VARCHAR(500) NULL AFTER live_stream_url,
  ADD COLUMN highlights_url VARCHAR(500) NULL AFTER full_match_url;

-- Add status and rich content fields to cup_fixtures
ALTER TABLE cup_fixtures
  ADD COLUMN status ENUM('scheduled','in_progress','completed','postponed','cancelled') DEFAULT 'scheduled' AFTER is_live,
  ADD COLUMN match_report TEXT NULL AFTER status,
  ADD COLUMN live_stream_url VARCHAR(500) NULL AFTER match_report,
  ADD COLUMN full_match_url VARCHAR(500) NULL AFTER live_stream_url,
  ADD COLUMN highlights_url VARCHAR(500) NULL AFTER full_match_url;

-- Create fixture_photos table for match photo galleries
CREATE TABLE IF NOT EXISTS fixture_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fixture_id INT NOT NULL,
  fixture_type ENUM('league', 'cup') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  caption VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  created_at DATETIME NOT NULL,
  INDEX idx_fixture (fixture_id, fixture_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
