# Managing Seasons

Seasons allow you to organise your football calendar and group competitions by year or period. You can create multiple seasons and switch between them.

## Viewing Seasons

Navigate to **Admin > Seasons** to see all your seasons.

The seasons list shows:

- **Season name**: Click to view details
- **Start date**: When the season begins
- **End date**: When the season ends
- **Status**: Active or Inactive
- **Leagues count**: Number of leagues in this season
- **Cups count**: Number of cups in this season
- **Actions**: Set Active, Edit, or Delete buttons

## Creating a Season

1. Navigate to **Admin > Seasons**
2. Click **+ Create Season**
3. Fill in the season details:
   - **Season Name**: Enter a descriptive name (e.g., "2024/25 Season")
   - **Start Date**: Select the season's start date
   - **End Date**: Select the season's end date
   - **Set as active season**: Tick this checkbox to make this the active season
4. Click **Create Season**

### Field Details

**Season Name**

Enter a name that clearly identifies the season. Common formats include:

- "2024/25 Season"
- "Spring 2025"
- "Winter League 2024"

The system automatically generates a URL-friendly slug from the season name.

**Start Date**

The date when competitions in this season begin. Use the date picker to select a date.

Format: Standard date input (YYYY-MM-DD displayed as DD/MM/YYYY in the browser).

**End Date**

The date when the season concludes. This should be after the start date.

**Set as active season**

When ticked, this season becomes the active season. The active season:

- Appears on the dashboard
- Is pre-selected when creating new leagues and cups
- Is used for the public website's default view

Only one season can be active at a time. Setting a new season as active automatically deactivates the previous one.

## Editing a Season

1. Navigate to **Admin > Seasons**
2. Click **Edit** next to the season you want to change
3. Update the season details
4. Click **Save Changes**

### What You Can Edit

On the edit screen:

- **Season Name**: Change the season's name
- **Start Date**: Adjust the start date
- **End Date**: Adjust the end date

**Note**: The Season ID is displayed but cannot be changed after creation. This ID is used internally to link competitions to the season.

The **Status** field shows whether the season is active. To change the status, use the **Set Active** button on the seasons list page.

If the season has competitions, you see a **Competitions** summary showing how many leagues and cups belong to this season. This is informational only and cannot be edited directly.

## Setting the Active Season

Only one season can be active at a time. To change which season is active:

1. Navigate to **Admin > Seasons**
2. Find the season you want to activate
3. Click **Set Active** in the Actions column

The previous active season automatically becomes inactive when you activate a different season.

## Deleting a Season

**Warning**: Deleting a season permanently removes it and all associated competitions, fixtures, and results. This action cannot be undone.

To delete a season:

1. Navigate to **Admin > Seasons**
2. Click **Delete** next to the season
3. Confirm the deletion when prompted

You cannot delete a season that is currently active. Set a different season as active first, then delete the unwanted season.

## Season Detail View

Click on a season name to view its details. The detail page shows:

- Season name and dates
- Active/Inactive status
- Count of competitions
- Links to edit the season

From the detail page, you can quickly navigate to edit the season or return to the seasons list.

## Tips for Managing Seasons

**Planning Ahead**

Create seasons in advance to plan your football calendar. You can create a season without setting it as active until you're ready to start.

**Overlapping Dates**

The system allows seasons with overlapping dates. For example, you can run a summer season and a winter season concurrently.

**Season Names**

Use consistent naming across seasons to make them easy to identify. If you run multiple types of competitions, consider including that in the season name (e.g., "2024/25 Youth Season").

**Active Season**

Keep the active season up to date. The active season determines what appears on your dashboard and public website by default.

## Next Steps

After creating a season:

- [Add teams](teams.md) to participate in competitions
- [Create leagues](leagues.md) for round-robin competitions
- [Create cups](cups.md) for knockout tournaments

Return to the [admin guide index](index.md).
