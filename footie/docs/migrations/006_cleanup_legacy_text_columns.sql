-- Migration 006: Cleanup Legacy TEXT Columns
-- Phase 6 final step - Remove unused home_scorers, away_scorers, home_cards, away_cards
--
-- SAFE TO RUN: All data migrated to match_events table during Phase 4
-- Verified: 0 fixtures have data in these columns
-- Verified: Code uses match_events exclusively via getMatchEventsForFixture()

-- Drop legacy columns from league_fixtures
ALTER TABLE league_fixtures
  DROP COLUMN home_scorers,
  DROP COLUMN away_scorers,
  DROP COLUMN home_cards,
  DROP COLUMN away_cards;

-- Drop legacy columns from cup_fixtures
ALTER TABLE cup_fixtures
  DROP COLUMN home_scorers,
  DROP COLUMN away_scorers,
  DROP COLUMN home_cards,
  DROP COLUMN away_cards;
