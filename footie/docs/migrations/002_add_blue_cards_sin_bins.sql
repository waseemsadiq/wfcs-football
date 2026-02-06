-- Migration 002: Add Blue Cards and Sin Bins Support
-- Extends match_events and player_stats to track all card types

-- Add blue_card and sin_bin to event_type enum
ALTER TABLE match_events
  MODIFY COLUMN event_type ENUM('goal', 'yellow_card', 'red_card', 'blue_card', 'sin_bin', 'assist') NOT NULL;

-- Add blue_cards and sin_bins columns to player_stats
ALTER TABLE player_stats
  ADD COLUMN blue_cards INT DEFAULT 0 AFTER red_cards,
  ADD COLUMN sin_bins INT DEFAULT 0 AFTER blue_cards;
