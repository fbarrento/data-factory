# Claude Code Configuration

This directory contains project-specific configuration for Claude Code.

## Structure

- **`settings.json`** - Project settings (model, permissions, hooks, MCP servers)
- **`commands/`** - Custom slash commands (`.md` files)
- **`hooks/`** - Shell scripts that run on specific events

## Custom Commands

Create markdown files in `commands/` to define custom slash commands:
- File name determines command: `commands/test.md` → `/test`
- File content is the prompt Claude executes

## Hooks

Create shell scripts in `hooks/` to run on events:
- `user-prompt-submit.sh` - Runs when user submits a message
- `tool-call-request.sh` - Runs before a tool is executed
- `tool-call-result.sh` - Runs after a tool completes

Enable hooks in `settings.json`.

## Documentation

For more information, visit: https://docs.claude.com/en/docs/claude-code