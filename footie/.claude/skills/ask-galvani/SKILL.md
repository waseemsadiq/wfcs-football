---
name: ask-galvani
description: Query Galvani PHP runtime documentation via the galvani-docs MCP server installed in Claude CLI. Use when the user asks about Galvani features, APIs, workers, HTTP handling, shared storage, fibres, futures, or other Galvani-specific topics.
---

# Ask Galvani

Query the Galvani documentation using the `galvani-docs` MCP server via Claude CLI.

## When to use

- User asks about Galvani PHP runtime features
- User needs help with Galvani APIs (HTTP, Workers, SharedStorage, etc.)
- User wants to understand Galvani concepts (fibres, futures, parallel execution)
- User needs code examples from Galvani docs

## Configuration

The skill requires the Galvani distribution path. Check `config.json` for the current path.

**Default path**: `/Users/waseem/Desktop/galvani`

## Before querying

1. **Validate the path exists**:

   ```bash
   ls <galvani_path>/galvani
   ```

2. **If validation fails**, ask the user:

   > "The Galvani path `<path>` doesn't exist. Where is your Galvani installation?"

   Then update `config.json` with the new path.

## How to query

Run this command from any directory:

```bash
cd <galvani_path> && claude -p "<user_question>" --allowedTools "mcp__galvani-docs__search_docs,Read,mcp__galvani-docs"
```

Replace:

- `<galvani_path>` with the path from `config.json`
- `<user_question>` with the user's actual question about Galvani

## Example

User asks: "How do I use WebSockets in Galvani?"

```bash
cd /Users/waseem/Desktop/galvani && claude -p "How do I use WebSockets in Galvani? Be concise but thorough." --allowedTools "mcp__galvani-docs__search_docs,Read,mcp__galvani-docs"
```

## Notes

- Queries take 30-60 seconds (Claude CLI startup + MCP server processing)
- The MCP server is project-scoped to the Galvani directory
- Always include `--allowedTools` to pre-approve MCP tool usage
