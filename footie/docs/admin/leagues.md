# Managing Leagues

Leagues are round-robin competitions where every team plays every other team. WFCS Football automatically generates fixtures and calculates league tables based on results.

## Viewing Leagues

Navigate to **Admin > Leagues** to see all leagues.

The leagues list shows:

- **League name**: Click to view the league table and fixtures
- **Season**: Which season the league belongs to
- **Number of teams**: How many teams are competing
- **Fixture status**: How many fixtures have been played
- **Actions**: View, Edit, Manage Fixtures, or Delete buttons

## Creating a League

Before creating a league, ensure you have:

- At least one season created
- At least two teams added to the system

To create a league:

1. Navigate to **Admin > Leagues**
2. Click **+ Create League**
3. Fill in the league details
4. Click **Create League**

WFCS Football automatically generates all fixtures in a round-robin format when you create the league.

### Required Fields

**League Name** (required)

Enter a name for your league competition.

Examples:

- "Premier Division"
- "Division One"
- "Sunday Morning League"

The system automatically generates a URL-friendly slug from the league name.

**Season** (required)

Select which season this league belongs to from the dropdown menu.

The dropdown shows all seasons, with the active season selected by default. Each league must belong to exactly one season.

**First Fixture Date** (required)

Select the date when the first fixtures should be played.

The system uses this date as the starting point when generating the fixture schedule. Subsequent fixtures are scheduled based on the frequency you set.

**Match Frequency** (required)

Choose how often fixtures are played:

- **Weekly**: Fixtures every 7 days
- **Fortnightly**: Fixtures every 14 days
- **Monthly**: Fixtures every 30 days

The system automatically calculates fixture dates based on this frequency, starting from the first fixture date.

**Typical Match Time** (required)

Enter the default time for fixtures in this league.

Use the time picker to select a time (e.g., 15:00, 19:30).

This time is applied to all generated fixtures. You can change individual fixture times later if needed.

**Select Teams** (required, minimum 2)

Tick the checkboxes for teams you want to include in this league.

You must select at least 2 teams to create a league. There is no maximum limit.

The team selector shows:

- Team colour indicator
- Team name
- Checkbox to include/exclude

A counter at the top shows how many teams you have selected.

## How Fixture Generation Works

When you create a league, WFCS Football:

1. Takes all selected teams
2. Creates a round-robin schedule (every team plays every other team)
3. Assigns dates based on your start date and frequency
4. Sets all fixtures to your chosen match time

For example, with 4 teams and weekly fixtures starting 1st January:

- Week 1 (1st Jan): Team A vs Team B, Team C vs Team D
- Week 2 (8th Jan): Team A vs Team C, Team B vs Team D
- Week 3 (15th Jan): Team A vs Team D, Team B vs Team C

The algorithm ensures no team plays multiple matches on the same date.

## Editing a League

1. Navigate to **Admin > Leagues**
2. Click **Edit** next to the league
3. Update the league details
4. Click **Save Changes**

### Managing Teams

You can add or remove teams from an active league:

1.  Scroll to the **Teams** section
2.  Tick or untick teams as needed
3.  Click **Save Changes**

**Important**: Changing the team lineup will **regenerate the schedule for all unplayed fixtures**. Matches that have already been played (with results) are preserved. A warning will appear to confirm this action.

### What You Cannot Edit

You cannot change:

- The season (leagues are permanently assigned to a season)

## League Detail View

Click on a league name to view its detail page. This shows:

- League table (standings) with:
  - Position
  - Team name
  - Played, Won, Drawn, Lost
  - Goals For, Goals Against, Goal Difference
  - Points
- Upcoming fixtures
- Recent results
- Actions: Edit League, Manage Fixtures

The league table updates automatically when you enter match results.

## Managing Fixtures

From the league detail page, click **Manage Fixtures** to access the fixtures screen.

Learn more about [managing fixtures](fixtures.md).

## Regenerating Fixtures

If you need to reschedule fixtures or change settings:

1. Navigate to the league detail page
2. Click **Manage Fixtures**
3. Scroll to the bottom and click **Regenerate Fixtures**
4. Set new scheduling parameters:
   - First Fixture Date
   - Match Frequency
   - Typical Match Time
5. Click **Regenerate Now**

### How Regeneration Works

When you regenerate fixtures:

- **Played fixtures** (those with results) are preserved
- **Unplayed fixtures** are deleted and replaced with a new schedule
- The new schedule uses the teams currently assigned to the league
- Dates are calculated from your new start date and frequency

This allows you to reschedule remaining fixtures without losing results already entered.

**Warning**: If no results have been entered, regeneration replaces the entire fixture list.

## Deleting a League

**Warning**: Deleting a league permanently removes it and all associated fixtures and results. This action cannot be undone.

To delete a league:

1. Navigate to **Admin > Leagues**
2. Click the league name to view details
3. Scroll to the **Danger Zone** section
4. Click **Delete League**
5. Confirm the deletion when prompted

The league, all its fixtures, and all results are permanently deleted.

## Understanding League Tables

WFCS Football calculates league standings automatically using standard football scoring:

- **Win**: 3 points
- **Draw**: 1 point
- **Loss**: 0 points

Teams are ranked by:

1. Total points (highest first)
2. Goal difference (if points are equal)
3. Goals scored (if goal difference is equal)

The table updates immediately when you enter or change results.

### Table Columns

- **Pos**: Current position in the table
- **Team**: Team name with colour indicator
- **P**: Played (number of matches played)
- **W**: Won
- **D**: Drawn
- **L**: Lost
- **GF**: Goals For (goals scored)
- **GA**: Goals Against (goals conceded)
- **GD**: Goal Difference (GF minus GA)
- **Pts**: Points (total points earned)

## Tips for Managing Leagues

**Choose Realistic Frequencies**

Consider your teams' availability when setting match frequency. Weekly fixtures work well for regular seasons, while monthly might suit occasional competitions.

**Plan Fixture Dates**

Set your first fixture date with enough notice for teams to prepare. Allow time between creating the league and the first match date.

**Check the Schedule**

After creating a league, review the generated fixtures to ensure the schedule works for all teams. Use regenerate if you need to adjust dates.

**Enter Results Promptly**

Update results soon after matches are played to keep the league table accurate and up to date.

**Communicate Changes**

If you regenerate fixtures, inform teams about the new schedule. The system does not automatically notify teams of changes.

## Fixture Schedule Examples

**4 Teams, Weekly Fixtures**

Total fixtures: 6 matches (each team plays 3 matches)

Week 1: 2 matches
Week 2: 2 matches
Week 3: 2 matches

**6 Teams, Fortnightly Fixtures**

Total fixtures: 15 matches (each team plays 5 matches)

Round 1 (Week 0): 3 matches
Round 2 (Week 2): 3 matches
Round 3 (Week 4): 3 matches
Round 4 (Week 6): 3 matches
Round 5 (Week 8): 3 matches

The schedule spreads fixtures evenly so no team plays multiple matches on the same date.

## Next Steps

After creating a league:

- [Manage fixtures](fixtures.md) to enter results
- View the league table on the public website
- Create additional leagues for different divisions or seasons

Return to the [admin guide index](index.md).
