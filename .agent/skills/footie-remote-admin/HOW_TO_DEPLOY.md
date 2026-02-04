# How to Deploy "Footie Remote Admin"

This folder contains a standalone AI Skill designed for your System Administrator.

## Prerequisities

1.  The Admin must use an AI tool that supports Agentic Skills (Antigravity, Claude Desktop with Custom Tooling support, etc).
2.  The Admin must have SSH access (key-based preferred) to the remote server.

## Installation for the Admin

1.  Zip this entire folder: `footie-remote-admin/`.
2.  Send the ZIP to your Admin.
3.  Instruct them to unzip it into their AI Agent's skill directory (e.g. `.agent/skills/`).

## Configuration

Before zipping, you (the Developer) can edit `SKILL.md` to pre-seed the connection details:

```markdown
- **Default SSH Host**: "admin@production-server.com"
- **Default Remote Path**: "/var/www/html/footie"
```

This saves the Admin from having to type it every time.

## Security

- **Password**: The skill will ask the Admin for the App Password (same one used to login to the web panel).
- **Verification**: The password is NOT checked by the AI. It is sent to the server in the script, hashed, and checked against the live `.env` file. If the password is wrong, the script dies instantly.
