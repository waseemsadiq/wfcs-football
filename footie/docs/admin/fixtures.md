# Managing Fixtures

Fixtures are the matches in your competitions. You can record results, enter detailed match information, reschedule individual fixtures, or regenerate entire schedules.

## Accessing Fixtures

Navigate to a competition's fixtures page:

1. Go to **Admin > Leagues** or **Admin > Cups**
2. Click on a competition name
3. Click **Manage Fixtures** from the competition detail page

Or from the dashboard:

1. Select a competition from the **Upcoming Fixtures** dropdown
2. Click through to the competition detail page
3. Click **Manage Fixtures**

## League Fixtures

League fixtures are displayed grouped by date, showing all matches scheduled for each day.

Each fixture shows:

- **Home team** (with colour indicator)
- **Score inputs** (for recording results)
- **Away team** (with colour indicator)
- **Date and time** (editable)
- **Match details** (expandable for scorers and cards)
- **Update button** (to save changes)

### Recording a League Result

To enter a match result:

1. Navigate to the league's fixtures page
2. Find the match in the list
3. Enter the home team's score in the left score box
4. Enter the away team's score in the right score box
5. Click **Update**

The score boxes accept numbers from 0 to 99.

The league table updates automatically when you save a result.

### Adding Match Details

To record scorers and cards:

1. Find the match in the fixtures list
2. Click **Match Details (Scorers & Cards)** to expand the details panel
3. Enter goal scorers for the home team (e.g., "Smith 23', Jones 45'")
4. Enter goal scorers for the away team (e.g., "Brown 67'")
5. Enter cards for the home team (e.g., "Wilson (Y), Taylor (R)")
6. Enter cards for the away team (e.g., "Roberts (Y)")
7. Click **Update**

#### Scorers Format

Enter scorers in any format you prefer:

- "Smith 23'" (player name and minute)
- "Smith 23', Jones 45'" (multiple scorers)
- "Smith (2)" (player with goal count)
- "Smith 23', 67'" (same player, multiple times)

The system stores exactly what you enter and displays it on the public website.

#### Cards Format

Enter cards with player names and card type:

- "Wilson (Y)" for a yellow card
- "Taylor (R)" for a red card
- "Wilson (Y), Taylor (R)" for multiple cards

Use (Y) for yellow and (R) for red.

### Editing Fixture Date and Time

To change when a match is played:

1. Find the match in the fixtures list
2. Edit the **Date** field (click the date picker)
3. Edit the **Time** field (click the time picker)
4. Click **Update**

The match moves to the new date in the fixtures list and updates on the public website.

### Clearing a Result

To remove a result that was entered incorrectly:

1. Find the match in the fixtures list
2. Clear both score boxes (delete the numbers)
3. Click **Update**

The match returns to an unplayed state and is removed from the league table calculations.

## Cup Fixtures

Cup fixtures are organised by round, showing the tournament bracket structure.

Each fixture shows:

- **Home team** (with colour indicator)
- **Score inputs** (for recording results)
- **Away team** (with colour indicator)
- **Date and time** (editable, compact format)
- **Extra time and penalties** (expandable options)
- **Match details** (expandable for scorers and cards)
- **Save button**

Matches where teams are not yet known display "TBD" (To Be Determined) and show "Match not determined yet" with a note that they're waiting for previous round results.

### Recording a Cup Result

To enter a knockout match result:

1. Navigate to the cup's fixtures page
2. Find the match in the appropriate round
3. Enter the home team's score
4. Enter the away team's score
5. If the scores are level, add extra time or penalty information (see below)
6. Click **Save**

The winning team automatically advances to the next round when you save a result.

### Recording Extra Time

If a match went to extra time:

1. Find the match in the fixtures list
2. Enter the normal time score in the main score boxes
3. Click **Extra Time / Penalties** to expand options
4. Toggle **Went to Extra Time** to ON (the switch turns primary colour)
5. Enter the score after extra time in the ET score boxes
6. Click **Save**

The extra time score determines the winner if the match was still level after normal time.

### Recording Penalties

If a match went to a penalty shootout:

1. Find the match in the fixtures list
2. Enter the normal time score (and extra time if applicable)
3. Click **Extra Time / Penalties** to expand options
4. Toggle **Penalties** to ON
5. Enter the penalty shootout score
6. Click **Save**

The penalty score determines the winner if the match was level after extra time (or normal time if no extra time was played).

### How Winners Are Determined

The system determines cup match winners using this priority:

1. **Normal time**: If scores are not level, the team with more goals wins
2. **Extra time**: If extra time was played, the team with more goals after ET wins
3. **Penalties**: If penalties were taken, the team with the higher penalty score wins

There must always be a winner in a cup match. Level scores require extra time or penalties.

### Adding Match Details (Cup)

Cup fixtures support the same scorers and cards details as league fixtures:

1. Find the match in the fixtures list
2. Click **Match Details (Scorers, Cards)** to expand
3. Enter scorers and cards for both teams
4. Click **Save**

The format is the same as league matches (see above).

### Editing Cup Fixture Dates

Cup fixture dates can be changed individually:

1. Find the match in the fixtures list
2. Edit the **Date** field (compact date picker)
3. Edit the **Time** field (compact time picker)
4. Click **Save**

Changing one fixture's date does not affect other fixtures in the round.

## Regenerating Fixtures

If you need to reschedule multiple fixtures at once, use the regenerate feature.

### Regenerating League Fixtures

1. Navigate to the league's fixtures page
2. Scroll to the bottom
3. Click **Regenerate Fixtures**
4. Set new parameters:
   - **First Fixture Date**: New starting date
   - **Match Frequency**: Weekly, Fortnightly, or Monthly
   - **Typical Match Time**: Default time for fixtures
5. Click **Regenerate Now**

The modal shows a confirmation message when regeneration completes. Click **Close & Refresh** to reload the page with the new schedule.

#### What Happens During League Regeneration

- **Played fixtures** (those with results) keep their original dates and times
- **Unplayed fixtures** are deleted
- A new round-robin schedule is created for unplayed matches
- New fixtures use the teams currently in the league
- Dates are calculated from the new start date using the new frequency

This allows you to reschedule the rest of the season without losing results already entered.

### Regenerating Cup Fixtures

1. Navigate to the cup's fixtures page
2. Scroll to the bottom
3. Click **Regenerate Fixtures**
4. Set new parameters:
   - **First Round Date**: New date for the first round
   - **Round Frequency**: Weekly, Fortnightly, or Monthly
   - **Typical Match Time**: Default time for fixtures
5. Click **Regenerate Now**

#### What Happens During Cup Regeneration

- **Completed matches** (with results and winners) are preserved
- **Unplayed fixtures** in remaining rounds are rescheduled
- Round dates are recalculated from the first round date
- The bracket structure stays the same (team pairings do not change)

Cup regeneration does not redraw the bracket. It only reschedules fixtures. To change team pairings, delete and recreate the cup.

## Fixtures on the Dashboard

The dashboard shows upcoming fixtures for quick access:

1. Select a competition from the dropdown menu
2. View fixtures grouped by date
3. For matches on or before today, enter scores directly
4. Click the checkmark button to save

This provides a quick way to record results without navigating to the full fixtures page.

Scores entered on the dashboard sync immediately with the competition's full fixtures list.

## Tips for Managing Fixtures

**Enter Results Promptly**

Update results soon after matches are played to keep tables and brackets current. This also helps teams see standings and next opponents quickly.

**Use Batch Regeneration**

If you need to change multiple fixture dates, use the regenerate feature instead of editing each fixture individually. This is faster and ensures consistent spacing between matches.

**Preserve Important Dates**

When regenerating, set the start date carefully to avoid scheduling fixtures on holidays, tournament dates, or other conflicts.

**Check After Regeneration**

After regenerating fixtures, review the new schedule to ensure dates work for all teams. You can regenerate again if needed.

**Match Details Are Optional**

You can record just the score, or include scorers and cards. Match details enhance the public website but are not required for league tables or cup progression.

**Extra Time vs Penalties**

For cup matches, you can use extra time alone, penalties alone, or both. If you toggle extra time on, you must enter the ET score. Same for penalties.

**Editing vs Regenerating**

- **Edit individual fixtures** when you need to reschedule one or two matches
- **Regenerate** when you need to reschedule an entire competition or significant portion

**Time Format**

Times are displayed in 24-hour format (e.g., 15:00, 19:30). The time picker helps you select times without typing.

## Fixture States

Fixtures can be in different states:

**Scheduled**: Date set, no result entered
**Played**: Result entered, included in table calculations
**TBD** (Cups only): Teams not yet known, waiting for previous round

You can enter or edit results for scheduled and played fixtures. TBD fixtures become editable once previous round results determine the teams.

## Common Tasks

### Rescheduling a Single Match

1. Navigate to fixtures page
2. Find the match
3. Change the date and/or time
4. Click Update/Save

### Recording a Postponed Match

If a match is postponed, change its date to the new date. The result fields remain empty until the match is played.

### Correcting a Wrong Score

1. Navigate to fixtures page
2. Find the match
3. Change the scores
4. Click Update/Save

Tables and brackets update immediately.

### Removing Match Details

To clear scorers or cards while keeping the score:

1. Navigate to fixtures page
2. Expand Match Details
3. Delete the text from scorers/cards fields
4. Click Update/Save

### Viewing Fixtures by Date

League fixtures are automatically grouped by date. Scroll through the page to find fixtures on a specific date.

Cup fixtures are grouped by round. Look for the round heading (e.g., "Quarter Finals") then find the match within that round.

## Next Steps

After managing fixtures:

- View updated league tables on competition detail pages
- Check cup brackets to see who has progressed
- Monitor upcoming fixtures on the dashboard
- Publish results to the public website

Return to the [admin guide index](index.md).
