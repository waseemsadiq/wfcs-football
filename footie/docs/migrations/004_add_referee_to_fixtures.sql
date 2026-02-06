-- Add referee field to fixture tables
-- Allows assigning referees (from team_staff) to matches

ALTER TABLE league_fixtures
ADD COLUMN referee_id INT DEFAULT NULL AFTER away_score,
ADD INDEX idx_referee (referee_id),
ADD CONSTRAINT fk_league_fixture_referee
    FOREIGN KEY (referee_id) REFERENCES team_staff(id) ON DELETE SET NULL;

ALTER TABLE cup_fixtures
ADD COLUMN referee_id INT DEFAULT NULL AFTER away_score,
ADD INDEX idx_referee (referee_id),
ADD CONSTRAINT fk_cup_fixture_referee
    FOREIGN KEY (referee_id) REFERENCES team_staff(id) ON DELETE SET NULL;
