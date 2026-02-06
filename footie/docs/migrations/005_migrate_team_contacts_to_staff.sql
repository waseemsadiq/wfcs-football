-- Migrate existing team contact data to team_staff table
-- This moves contact person, phone, and email from teams table to team_staff records

-- Insert contact records for teams that have contact information
INSERT INTO team_staff (team_id, name, role, phone, email, created_at, updated_at)
SELECT
    id,
    contact,
    'contact',
    phone,
    email,
    NOW(),
    NOW()
FROM teams
WHERE contact IS NOT NULL
  AND contact != ''
  AND NOT EXISTS (
    SELECT 1 FROM team_staff ts
    WHERE ts.team_id = teams.id
      AND ts.name = teams.contact
      AND ts.role = 'contact'
  );

-- Remove old contact fields from teams table
ALTER TABLE teams
DROP COLUMN contact,
DROP COLUMN phone,
DROP COLUMN email;
