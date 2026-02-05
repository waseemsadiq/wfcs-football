---
name: using-notebooklm
description: Orchestrates interactions with Google NotebookLM via MCP to perform deep research, manage notebooks, and generate multimedia content. Use when the user asks to research a topic, summarize documents, or create study aids using NotebookLM.
---

# Using NotebookLM

## When to use this skill

- Researching complex topics (finding sources, summarizing findings)
- Creating new NotebookLM notebooks or managing existing ones
- Generating audio overviews (podcasts) or video overviews
- Importing sources from URLs, text, or Google Drive
- Creating study guides, quizzes, flashcards, or briefing docs
- Generating visual aids like mind maps, timelines, or potential slides

## Workflow

### 1. Research & Discovery (Finding New Sources)

Use this workflow when the user wants to research a topic from scratch.

#### Standard Flow (Single Topic)

1.  **Start Research**: Call `research_start(query="...", source="web|drive", mode="fast|deep")`.
    - Use `mode="fast"` for quick overviews (~30s).
    - Use `mode="deep"` for comprehensive searches (~5min).
2.  **Monitor Progress**: Call `research_status(notebook_id="...", task_id="...")` to poll for completion.
    - Use `max_wait=0` for non-blocking status check.
    - Use `max_wait=300` to block until completion (up to 5 minutes).
3.  **Import Findings**: Call `research_import(notebook_id="...", task_id="...")` to add discovered sources to the notebook.

#### Batch Flow (Multiple Topics, Deferred Import)

Use this when starting research on multiple notebooks at once:

1.  **Start All Research Jobs**: Call `research_start` for each notebook. Save the task IDs to `.agent/pending_research.json` in the current workspace. Create the file if it doesn't exist:

    ```json
    {
      "pending": [
        {
          "notebook_id": "abc123",
          "task_id": "xyz789",
          "title": "Topic Name",
          "started": "2026-01-27T22:30:00Z"
        }
      ]
    }
    ```

2.  **Return Immediately**: Don't block. Tell the user research is running in the background.

3.  **Check & Import Later**: When user asks to continue or check status:
    - Read `.agent/pending_research.json` from the current workspace
    - For each pending task, call `research_status(max_wait=0)`
    - If `status="completed"`, call `research_import` and remove from pending list
    - Update the JSON file with remaining pending tasks
    - Delete the file when all tasks are imported

**Note**: The `pending_research.json` file is created dynamically per-workspace, not distributed with the skill.

### 2. Content Creation (Notebooks & Sources)

Use this workflow to build a notebook manually.

1.  **Create/Select Notebook**:
    - List existing: `notebook_list(max_results=20)`.
    - Create new: `notebook_create(title="...")`.
    - Get details: `notebook_get(notebook_id="...")`.
2.  **Add Sources**:
    - From URL: `notebook_add_url(notebook_id="...", url="...")`.
    - From Text: `notebook_add_text(notebook_id="...", text="...")`.
    - From Drive: `notebook_add_drive(notebook_id="...", document_id="...")`.
3.  **Manage Sources**:
    - Get content: `source_get_content(source_id="...")`.
    - Describe: `source_describe(source_id="...")`.

### 3. Generation (Multimedia & Study Aids)

Use these tools to generate artifacts from the notebook's content.

- **Audio/Video**:
  - `audio_overview_create(notebook_id="...", format="...", confirm=True)`
  - `video_overview_create(notebook_id="...", format="...", visual_style="...", confirm=True)`
- **Study Materials**:
  - `report_create(notebook_id="...", report_format="Briefing Doc|Study Guide|...", confirm=True)`
  - `flashcards_create` or `quiz_create`.
- **Visuals**:
  - `mind_map_create` or `infographic_create`.
  - `slide_deck_create` or `data_table_create`.

**Note**: All generation tools require `confirm=True` after user approval. Since you are an agent acting on behalf of the user, you should ask for confirmation if the operation is significant, or proceed if the user explicitly requested the output.

## Instructions

- **Authentication**: Usage requires an authenticated session. If tools fail with auth errors, advise the user to run `notebooklm-mcp-auth` in their terminal, then call `refresh_auth`.
- **Context Awareness**: Use `notebook_describe` to get a summary of a notebook's content before querying it, to ensure it contains relevant information.
- **Querying**: Use `notebook_query` to ask questions specific to the sources _in the notebook_. Use `research_start` to find _new_ information from the web.
- **Studio Artifacts**: Generated content (audio, video, etc.) is stored in the "Studio". Use `studio_status` to retrieve URLs for these generated assets.
