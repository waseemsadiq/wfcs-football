-- Add referee role to team_staff table
-- This allows tracking referees as support staff who can be assigned to matches

ALTER TABLE team_staff
MODIFY COLUMN role ENUM('coach', 'assistant_coach', 'manager', 'referee', 'contact', 'other') NOT NULL;
