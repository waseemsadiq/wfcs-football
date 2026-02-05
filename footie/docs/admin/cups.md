# Managing Cups

Cups are knockout tournaments with bracket-style fixtures. Teams are randomly drawn into a bracket, and winners progress to the next round until a champion is crowned.

## Viewing Cups

Navigate to **Admin > Cups** to see all cup competitions.

The cups list shows:

- **Cup name**: Click to view the bracket
- **Season**: Which season the cup belongs to
- **Number of teams**: How many teams entered
- **Current round**: Which stage the tournament has reached
- **Actions**: View, Edit, Manage Fixtures, or Delete buttons

## Creating a Cup

Before creating a cup, ensure you have:

- At least one season created
- At least two teams added to the system

To create a cup:

1. Navigate to **Admin > Cups**
2. Click **+ Create Cup**
3. Fill in the cup details
4. Click **Create Cup**

WFCS Football automatically generates a knockout bracket and schedules the first round when you create the cup.

### Required Fields

**Cup Name** (required)

Enter a name for your cup competition.

Examples:

- "FA Cup"
- "League Cup"
- "Challenge Trophy"
- "Charity Shield"

The system automatically generates a URL-friendly slug from the cup name.

**Season** (required)

Select which season this cup belongs to from the dropdown menu.

The dropdown shows all seasons, with the active season selected by default. Each cup must belong to exactly one season.

### Scheduling Defaults (Optional but Recommended)

These settings control how the first round is scheduled and provide defaults for subsequent rounds:

**First Fixture Date**

Select the date when the first round should be played.

The system uses this date when generating the first round of fixtures. If you leave this blank, you need to set fixture dates manually.

**Match Frequency**

Choose how often cup rounds are played:

- **Weekly**: Rounds every 7 days
- **Fortnightly**: Rounds every 14 days
- **Monthly**: Rounds every 30 days

This determines the gap between rounds. For example, with weekly frequency and a first round date of 1st January:

- First Round: 1st January
- Second Round: 8th January
- Quarter Finals: 15th January

**Typical Match Time**

Enter the default time for cup fixtures.

Use the time picker to select a time (e.g., 15:00, 19:30).

This time is applied to all generated fixtures. You can change individual fixture times later.

### Team Selection

**Select Teams** (required, minimum 2)

Tick the checkboxes for teams you want to enter into the cup.

You must select at least 2 teams. You can include as many teams as you like.

The team selector shows:

- Team colour indicator
- Team name
- Checkbox to include/exclude
- Selected team count at the top

## How Bracket Generation Works

When you create a cup, WFCS Football:

1. Takes all selected teams
2. Randomly draws them into a knockout bracket
3. Creates a bracket with the appropriate number of rounds
4. Schedules the first round based on your settings
5. Creates placeholder fixtures for future rounds

### Bracket Size

The system creates a bracket based on the number of teams:

- **2 teams**: 1 round (Final)
- **3-4 teams**: 2 rounds (Semi-Finals, Final)
- **5-8 teams**: 3 rounds (Quarter Finals, Semi-Finals, Final)
- **9-16 teams**: 4 rounds (Round of 16, Quarter Finals, Semi-Finals, Final)
- And so on...

If the number of teams is not a power of 2, some teams receive byes (automatic progression to the next round).

### Random Draw

Teams are randomly assigned to bracket positions when the cup is created. This ensures a fair draw.

The draw is permanent once created. To change the draw, you must delete and recreate the cup.

## Editing a Cup

1. Navigate to **Admin > Cups**
2. Click **Edit** next to the cup
3. Update the cup details
4. Click **Save Changes**

### What You Can Edit

**Cup Name**

Change the cup's name. The slug updates automatically if you change the name.

**Scheduling Defaults**

You can update:

- **First Fixture Date**: Changes the reference date for regeneration
- **Match Frequency**: Updates the frequency for regeneration
- **Typical Match Time**: Changes the default time for regeneration

These settings are used when you regenerate remaining fixtures. They do not automatically reschedule existing fixtures.

### Managing Teams

You can add or remove teams from an active cup:

1. Scroll to the **Teams** section
2. Tick or untick teams as needed
3. Click **Save Changes**

**Important**: Changing the team lineup will **regenerate the bracket for all unplayed matches**. Matches that have already been played (with results) are preserved. A warning will appear to confirm this action.

### What You Cannot Edit

You cannot change:

- The season (cups are permanently assigned to a season)

## Cup Detail View (Bracket)

Click on a cup name to view the tournament bracket. This shows:

- All rounds from first round to final
- Team matchups in each round
- Match results where entered
- Winners progressing through the bracket
- Placeholder "TBD" for matches not yet determined

The bracket view provides a visual overview of the entire tournament structure.

## Managing Fixtures

From the cup detail page, click **Manage Fixtures** to access the fixtures screen.

The fixtures screen shows all rounds with:

- Match pairings
- Date and time for each fixture
- Score entry for matches where teams are known
- Extra time and penalty options for knockout matches

Learn more about [managing cup fixtures](fixtures.md#cup-fixtures).

## Cup Match Results

Cup matches are knockout, so there must be a winner. If scores are level after normal time, you can record:

- **Extra Time**: Additional 30 minutes of play
- **Penalties**: Penalty shootout score

The system uses these to determine the winner and progress them to the next round.

## Advancing Through Rounds

As you enter results:

1. The system determines the winner of each match
2. Winners automatically populate the next round
3. Fixture dates for the next round are calculated based on your frequency setting
4. Teams progress through the bracket until one team wins the final

You do not need to manually set up later rounds. The bracket structure is created when you create the cup.

## Regenerating Fixtures

If you need to reschedule remaining fixtures:

1. Navigate to the cup detail page
2. Click **Manage Fixtures**
3. Scroll to the bottom and click **Regenerate Fixtures**
4. Set scheduling parameters:
   - First Round Date
   - Round Frequency
   - Typical Match Time
5. Click **Regenerate Now**

### How Cup Regeneration Works

When you regenerate cup fixtures:

- **Completed matches** (with results and winners) are preserved
- **Unplayed fixtures** in the remaining rounds are rescheduled
- Future round dates are recalculated based on your new frequency
- The bracket structure remains intact

If no results have been entered, regeneration reschedules the entire tournament.

**Note**: Unlike leagues, cup regeneration does not change the bracket draw. It only reschedules fixtures. To change team pairings, you must delete and recreate the cup.

## Deleting a Cup

**Warning**: Deleting a cup permanently removes it and all associated fixtures and results. This action cannot be undone.

To delete a cup:

1. Navigate to **Admin > Cups**
2. Click the cup name to view details
3. Scroll to the **Danger Zone** section
4. Click **Delete Cup**
5. Confirm the deletion when prompted

The cup, all its fixtures, all results, and the bracket structure are permanently deleted.

## Understanding Cup Brackets

WFCS Football creates a standard single-elimination bracket:

- Each match has a winner
- Winners progress to the next round
- Losers are eliminated from the competition
- The tournament continues until one team remains

### Byes

If the number of teams is not a power of 2 (e.g., 3, 5, 6, 7, 9-15 teams), some teams receive byes.

A bye means the team automatically advances to the next round without playing. Byes are assigned randomly during the draw.

For example, with 5 teams:

- 3 teams play in Round 1 (1 match, 1 team gets a bye, 2 teams get a bye)
- 4 teams play in the Semi-Finals
- 2 teams play in the Final

### Round Names

The system automatically names rounds based on the number of teams:

- **Final**: Last match (2 teams)
- **Semi-Finals**: Second-to-last round (4 teams)
- **Quarter Finals**: Third-to-last round (8 teams)
- **Round of 16**: Fourth-to-last round (16 teams)
- **Round 1, Round 2**: Earlier rounds

## Recording Extra Time and Penalties

For knockout matches that are level after normal time:

1. Navigate to **Manage Fixtures**
2. Find the match in question
3. Enter the normal time score
4. Expand **Extra Time / Penalties**
5. Tick **Went to Extra Time** if applicable
6. Enter the score after extra time
7. Tick **Penalties** if the match went to a shootout
8. Enter the penalty shootout score
9. Click **Save**

The system determines the winner based on:

1. Normal time score (if not level)
2. Extra time score (if extra time was played)
3. Penalty score (if penalties were taken)

The winner automatically advances to the next round.

## Tips for Managing Cups

**Plan Your Schedule**

Cup tournaments can span several weeks or months depending on the number of rounds and your frequency setting. Plan the start date to ensure the final falls at an appropriate time.

**Check Bracket Balance**

After creating a cup, review the bracket to ensure the draw is fair. While the draw is random, you can delete and recreate the cup if you get an unbalanced bracket.

**Schedule Buffer**

Use fortnightly or monthly frequencies for cups to allow time for potential replays or rescheduling if matches cannot be played.

**Communicate Draws**

After creating a cup, inform teams of their first-round opponents. The system does not automatically notify teams.

**Enter Results Promptly**

Update results after each round so the bracket stays current and teams know their next opponents.

## Fixture Schedule Examples

**8 Team Cup, Weekly Frequency**

Total rounds: 3 (Quarter Finals, Semi-Finals, Final)

- Week 1 (1st Jan): Quarter Finals (4 matches)
- Week 2 (8th Jan): Semi-Finals (2 matches)
- Week 3 (15th Jan): Final (1 match)

**5 Team Cup, Fortnightly Frequency**

Total rounds: 3 (Round 1 with byes, Semi-Finals, Final)

- Week 0 (1st Jan): Round 1 (1 match, 3 teams get byes)
- Week 2 (15th Jan): Semi-Finals (2 matches)
- Week 4 (29th Jan): Final (1 match)

## Next Steps

After creating a cup:

- [Manage fixtures](fixtures.md) to enter results
- View the bracket on the public website
- Create additional cups for different tournaments or seasons

Return to the [admin guide index](index.md).
