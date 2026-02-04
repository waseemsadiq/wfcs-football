# Field Reference

This is a complete reference of every input field in the WFCS Football admin area. Fields are organised by section.

## Seasons

### Season Name
- **Location**: Seasons > Create / Edit
- **Type**: Text input
- **Required**: Yes
- **Format**: Free text, any length
- **Examples**: "2024/25 Season", "Spring 2025", "Winter League 2024"
- **Purpose**: Identifies the season throughout the system and on the public website.

### Start Date
- **Location**: Seasons > Create / Edit
- **Type**: Date picker
- **Required**: Yes
- **Format**: YYYY-MM-DD (browser displays in local format)
- **Purpose**: Marks the beginning of the season period. Used for reference and organisation.

### End Date
- **Location**: Seasons > Create / Edit
- **Type**: Date picker
- **Required**: Yes
- **Format**: YYYY-MM-DD (browser displays in local format)
- **Purpose**: Marks the end of the season period. Should be after the start date.

### Set as Active Season
- **Location**: Seasons > Create
- **Type**: Checkbox
- **Required**: No
- **Default**: Unchecked
- **Purpose**: When ticked, this season becomes the active season. The active season appears on the dashboard and is used as the default when creating competitions. Only one season can be active at a time.

## Teams

### Team Name
- **Location**: Teams > Add / Edit
- **Type**: Text input
- **Required**: Yes
- **Format**: Free text, any length
- **Examples**: "Red Lions FC", "St Mary's Athletic", "Riverside Rangers"
- **Purpose**: The team's name as it appears in fixtures, tables, and brackets.

### Contact Person
- **Location**: Teams > Add / Edit
- **Type**: Text input
- **Required**: No
- **Format**: Free text (typically full name)
- **Examples**: "John Smith", "Sarah Wilson"
- **Purpose**: The main contact for this team. Could be a manager, captain, or club secretary.

### Phone Number
- **Location**: Teams > Add / Edit
- **Type**: Telephone input
- **Required**: No
- **Format**: Any format accepted (no validation)
- **Examples**: "07700 900123", "+44 7700 900123", "01234 567890"
- **Purpose**: Contact telephone number for the team.

### Email Address
- **Location**: Teams > Add / Edit
- **Type**: Email input
- **Required**: No
- **Format**: Valid email address
- **Examples**: "john@example.com", "redlions@football.com"
- **Purpose**: Contact email address for team communications.

### Team Colour
- **Location**: Teams > Add / Edit
- **Type**: Colour picker
- **Required**: No (defaults to #1a5f2a)
- **Format**: Hex colour code (e.g., #1a5f2a)
- **Purpose**: Identifies the team in fixtures and tables. A small colour dot appears next to the team name throughout the system.
- **Quick Pick**: Click "Quick Pick Team Colour" to choose from professional football team colours organised by league.

### Players
- **Location**: Teams > Add / Edit
- **Type**: Multi-line text area
- **Required**: No
- **Format**: One player name per line
- **Examples**:
  ```
  James Wilson
  David Brown
  Michael Taylor
  ```
- **Purpose**: Team roster for record-keeping. Names are stored exactly as entered.

## Leagues

### League Name
- **Location**: Leagues > Create / Edit
- **Type**: Text input
- **Required**: Yes
- **Format**: Free text, any length
- **Examples**: "Premier Division", "Division One", "Sunday Morning League"
- **Purpose**: The league's name as it appears on the website and in navigation.

### Season
- **Location**: Leagues > Create
- **Type**: Dropdown select
- **Required**: Yes
- **Format**: Select from existing seasons
- **Purpose**: Assigns this league to a season. The active season is selected by default. This cannot be changed after creation.

### First Fixture Date
- **Location**: Leagues > Create / Edit / Regenerate
- **Type**: Date picker
- **Required**: Yes
- **Format**: YYYY-MM-DD
- **Purpose**: The starting date for the fixture schedule. The system calculates all fixture dates from this date using the match frequency.

### Match Frequency
- **Location**: Leagues > Create / Edit / Regenerate
- **Type**: Dropdown select
- **Required**: Yes
- **Options**:
  - Weekly (fixtures every 7 days)
  - Fortnightly (fixtures every 14 days)
  - Monthly (fixtures every 30 days)
- **Default**: Weekly
- **Purpose**: Determines how often fixtures are scheduled. The system spaces fixtures by this interval.

### Typical Match Time
- **Location**: Leagues > Create / Edit / Regenerate
- **Type**: Time picker
- **Required**: Yes
- **Format**: HH:MM (24-hour format)
- **Default**: 15:00
- **Examples**: 15:00 (3:00 PM), 19:30 (7:30 PM), 14:00 (2:00 PM)
- **Purpose**: The default time applied to all fixtures. You can change individual fixture times later.

### Select Teams
- **Location**: Leagues > Create
- **Type**: Checkbox grid
- **Required**: Yes (minimum 2 teams)
- **Format**: Tick boxes for each team
- **Purpose**: Chooses which teams participate in this league. The system creates a round-robin schedule where every team plays every other team. A counter shows how many teams are selected.

## Cups

### Cup Name
- **Location**: Cups > Create / Edit
- **Type**: Text input
- **Required**: Yes
- **Format**: Free text, any length
- **Examples**: "FA Cup", "League Cup", "Challenge Trophy"
- **Purpose**: The cup's name as it appears on the website and in navigation.

### Season
- **Location**: Cups > Create
- **Type**: Dropdown select
- **Required**: Yes
- **Format**: Select from existing seasons
- **Purpose**: Assigns this cup to a season. The active season is selected by default. This cannot be changed after creation.

### First Fixture Date
- **Location**: Cups > Create / Edit / Regenerate
- **Type**: Date picker
- **Required**: No (but recommended)
- **Format**: YYYY-MM-DD
- **Purpose**: The date for first round fixtures. The system schedules subsequent rounds based on the round frequency. If blank, you must set fixture dates manually.
- **Label Context**: Shown as "First Round Date" in the regenerate modal.

### Match Frequency
- **Location**: Cups > Create / Edit / Regenerate
- **Type**: Dropdown select
- **Required**: No (but recommended)
- **Options**:
  - Weekly (rounds every 7 days)
  - Fortnightly (rounds every 14 days)
  - Monthly (rounds every 30 days)
- **Default**: Weekly
- **Purpose**: Determines the gap between rounds. For example, with weekly frequency: Round 1 on Jan 1st, Round 2 on Jan 8th, Final on Jan 15th.
- **Label Context**: Shown as "Round Frequency" in the regenerate modal.

### Typical Match Time
- **Location**: Cups > Create / Edit / Regenerate
- **Type**: Time picker
- **Required**: No (but recommended)
- **Format**: HH:MM (24-hour format)
- **Default**: 15:00
- **Examples**: 15:00, 19:30, 20:00
- **Purpose**: The default time for all cup fixtures. Can be changed for individual matches.

### Select Teams
- **Location**: Cups > Create
- **Type**: Checkbox grid
- **Required**: Yes (minimum 2 teams)
- **Format**: Tick boxes for each team
- **Purpose**: Chooses teams for the knockout tournament. Teams are randomly drawn into a bracket. The number of teams determines the number of rounds.
- **Note**: The team count displays at the top of the selector.

## Fixtures (Leagues)

### Home Score
- **Location**: Leagues > Fixtures / Dashboard
- **Type**: Number input
- **Required**: No (leave blank for unplayed matches)
- **Format**: Integer from 0 to 99
- **Examples**: 0, 1, 3, 5
- **Purpose**: Goals scored by the home team. Updates league table when saved.

### Away Score
- **Location**: Leagues > Fixtures / Dashboard
- **Type**: Number input
- **Required**: No (leave blank for unplayed matches)
- **Format**: Integer from 0 to 99
- **Examples**: 0, 1, 2, 4
- **Purpose**: Goals scored by the away team. Updates league table when saved.

### Date
- **Location**: Leagues > Fixtures
- **Type**: Date picker
- **Required**: Yes (set during generation)
- **Format**: YYYY-MM-DD
- **Purpose**: When this match is scheduled. Change to reschedule individual fixtures. Matches are grouped by date in the fixtures list.

### Time
- **Location**: Leagues > Fixtures
- **Type**: Time picker
- **Required**: Yes (set during generation)
- **Format**: HH:MM
- **Default**: Uses league's typical match time
- **Purpose**: What time the match kicks off. Change to reschedule individual fixtures.

### Home Scorers
- **Location**: Leagues > Fixtures > Match Details
- **Type**: Text input
- **Required**: No
- **Format**: Free text (suggested format: "Player 23', Player 45'")
- **Examples**: "Smith 23'", "Jones 10', Wilson 67'", "Brown (2)"
- **Purpose**: Records who scored for the home team. Displayed on the public website match details.

### Home Cards
- **Location**: Leagues > Fixtures > Match Details
- **Type**: Text input
- **Required**: No
- **Format**: Free text (suggested format: "Player (Y)" or "Player (R)")
- **Examples**: "Wilson (Y)", "Taylor (R)", "Smith (Y), Jones (Y)"
- **Purpose**: Records yellow and red cards for the home team. Use (Y) for yellow, (R) for red.

### Away Scorers
- **Location**: Leagues > Fixtures > Match Details
- **Type**: Text input
- **Required**: No
- **Format**: Free text (suggested format: "Player 23', Player 45'")
- **Examples**: "Brown 34'", "Taylor 56', 78'"
- **Purpose**: Records who scored for the away team. Displayed on the public website.

### Away Cards
- **Location**: Leagues > Fixtures > Match Details
- **Type**: Text input
- **Required**: No
- **Format**: Free text (suggested format: "Player (Y)" or "Player (R)")
- **Examples**: "Roberts (Y)", "Williams (R)"
- **Purpose**: Records yellow and red cards for the away team.

## Fixtures (Cups)

Cup fixtures include all the league fixture fields plus extra time and penalty fields:

### Home Score
- Same as league fixtures (see above)

### Away Score
- Same as league fixtures (see above)

### Date
- **Location**: Cups > Fixtures
- **Type**: Compact date picker
- **Format**: YYYY-MM-DD
- **Purpose**: Same as league fixtures, but displayed in a more compact format for space efficiency in the bracket view.

### Time
- **Location**: Cups > Fixtures
- **Type**: Compact time picker
- **Format**: HH:MM
- **Purpose**: Same as league fixtures, displayed compactly.

### Went to Extra Time
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Toggle switch
- **Required**: No
- **Default**: OFF
- **Purpose**: Indicates if this match went to extra time. When toggled ON, the extra time score inputs appear. Use this when the match was level after normal time and additional time was played.

### Home Score ET (Extra Time)
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Number input
- **Required**: If "Went to Extra Time" is ON
- **Format**: Integer from 0 to 99
- **Purpose**: Home team's score after extra time. This includes any goals scored during extra time plus the normal time score. The system uses this to determine the winner if extra time was played.

### Away Score ET (Extra Time)
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Number input
- **Required**: If "Went to Extra Time" is ON
- **Format**: Integer from 0 to 99
- **Purpose**: Away team's score after extra time. Includes extra time goals plus normal time goals.

### Penalties
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Toggle switch
- **Required**: No
- **Default**: OFF
- **Purpose**: Indicates if this match went to a penalty shootout. When toggled ON, the penalty score inputs appear. Use this when the match was still level after extra time (or normal time if no extra time was played).

### Home Penalty Score
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Number input
- **Required**: If "Penalties" is ON
- **Format**: Integer from 0 to 99
- **Purpose**: Number of penalties scored by the home team in the shootout. This determines the winner when the match score is level.

### Away Penalty Score
- **Location**: Cups > Fixtures > Extra Time / Penalties
- **Type**: Number input
- **Required**: If "Penalties" is ON
- **Format**: Integer from 0 to 99
- **Purpose**: Number of penalties scored by the away team in the shootout.

### Home Scorers
- Same as league fixtures (see above)
- **Note**: For matches with extra time, include "(ET)" after scorer's name if they scored during extra time (e.g., "Smith 105' (ET)")

### Home Cards
- Same as league fixtures (see above)

### Away Scorers
- Same as league fixtures (see above)

### Away Cards
- Same as league fixtures (see above)

## Regenerate Fixtures Modal

These fields appear in the regenerate fixtures modal for both leagues and cups:

### First Fixture Date / First Round Date
- **Type**: Date picker
- **Required**: Yes
- **Format**: YYYY-MM-DD
- **Purpose**: The new starting date for the fixture schedule. The system recalculates all unplayed fixtures from this date.
- **Label**: "First Fixture Date" for leagues, "First Round Date" for cups.

### Match Frequency / Round Frequency
- **Type**: Dropdown select
- **Required**: Yes
- **Options**: Weekly, Fortnightly, Monthly
- **Purpose**: The new frequency for scheduling. Determines spacing between fixtures or rounds.
- **Label**: "Match Frequency" for leagues, "Round Frequency" for cups.

### Typical Match Time
- **Type**: Time picker
- **Required**: Yes
- **Format**: HH:MM
- **Default**: Uses current league/cup setting
- **Purpose**: The new default time for all regenerated fixtures.

## Authentication

### Password
- **Location**: Login page
- **Type**: Password input
- **Required**: Yes
- **Format**: Text (hidden when typing)
- **Purpose**: The admin password for accessing the admin area. This is set during installation.

## General Notes

### Required Fields

Fields marked with a red asterisk (*) in the interface are required. The form will not submit until these fields are completed.

### Date Formats

All date pickers accept and display dates in your browser's local format. Dates are stored in YYYY-MM-DD format internally.

### Time Formats

All time pickers use 24-hour format (HH:MM). Times are displayed as entered (e.g., 15:00, 19:30).

### Validation

- **Email fields**: Must contain a valid email format (user@domain.com)
- **Number fields**: Accept only integers within the specified range
- **Required fields**: Cannot be left empty when submitting

### Editing vs Creating

Some fields (like Season selection for leagues/cups) can only be set during creation and cannot be changed afterward. The edit form displays these as read-only or omits them entirely.

### Default Values

Many fields have sensible defaults:

- Team colour: #1a5f2a (dark green)
- Match time: 15:00 (3:00 PM)
- Match frequency: Weekly

You can change these defaults when creating or editing.

## Field Behaviour

### Auto-generation

Some values are generated automatically:

- **Slugs**: URL-friendly versions of names (e.g., "Premier Division" becomes "premier-division")
- **Fixture schedules**: Round-robin for leagues, knockout brackets for cups
- **League tables**: Calculated from match results

These do not appear as input fields because the system manages them.

### Conditional Fields

Some fields only appear based on other selections:

- **Extra time score boxes**: Only visible when "Went to Extra Time" is toggled ON
- **Penalty score boxes**: Only visible when "Penalties" is toggled ON
- **Match details**: Hidden until you expand the details section

### Real-time Updates

Some fields update dynamically:

- **Team count**: Updates as you tick/untick team checkboxes
- **Colour value**: Updates as you select colours from the picker or quick pick
- **Selected colour chip**: Highlights when you select a quick pick colour

Return to the [admin guide index](index.md).
