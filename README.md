# CommandBlockerX
Block people from using commands in PocketMine-MP

Block certain types of command senders from executing commands

## How to configure
The following code is the default config.yml:
```yaml
blocked-commands: # List of blocked commands without the slash (/)
  me: # Command name
    console: true # Block in console
    in-game: true # Block in game
    rcon: true # Block in RCON
  say:
    console: false
    in-game: true
    rcon: false
messages: # List of configurable messages sent to command senders
  command-blocked: "§cThis command is blocked" # If a command is blocked, use § for color
```
The blocked-commands key contains multiple nested arrays which tells the plugin which users are allowed to use that command.

To add new commands to the list, do the same as the example commands underneath them.

You can also remove commands from the list by deleting all of the data for that command.

> Once you have configured your list, you can use ``/commandblocker reload`` to apply your changes