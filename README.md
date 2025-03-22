
# MartianLootbox

**MartianLootbox** is a powerful, customizable lootbox plugin for PocketMine, designed to offer a variety of animations, customizable rewards, and advanced features to enhance the player's experience on your server. With this plugin, players can enjoy visually stunning lootbox animations like shuffle effects or even no-animation modes for a more straightforward claim experience.

## Features

- **Customizable Lootboxes**: Create unique lootboxes with various rewards and effects.
- **Animations**: Includes multiple animations like shuffle, no-animation (plain claim), and more. (Easy to add custom animations later.)
- **Rewards System**: Set up configurable reward chances and item setups.
- **Lootbox Previews**: Players can preview lootboxes before claiming.
- **Lootbox Command**: The `/martianlootbox give` command lets admins distribute lootboxes.
  
## Animations

MartianLootbox comes with two animations by default:

### Shuffle Animation (Animation ID: `1`)
The shuffle animation creates an exciting experience where the lootboxes are shuffled before revealing the rewards.

### No Animation (Animation ID: `0`)
This animation is more straightforward, where players simply click to claim a lootbox without any special effects.

### How to Create Lootboxes

1. Navigate to the `lootboxes` directory inside the plugin's data folder.
2. Create a `.yml` file (e.g., `example.yml`).
3. Define your lootbox's rewards, animations, and settings in the `.yml` file.

## Commands

### `/martianlootbox give <lootbox_id> <player> [amount]`
Give a specific lootbox to a player.

### `/martianlootbox list`
Displays all available lootboxes on the server.

### `/martianlootbox info`
Displays plugin information.

## Permissions

The plugin does not have explicit permission checks for lootbox access at the moment, but you can easily implement permission-based access if needed for your server.

## Previews

### Animation Previews

**Shuffle Animation (ID: 1)**  
https://github.com/user-attachments/assets/4342b118-708d-46ba-a0fb-7c203dfbaae3

**No Animation (ID: 0)**  
This animation simply displays the lootbox with no shuffle effects. Players can click to claim their lootboxes directly without any visual effects.

## TODO

- [ ] Implement chances on lootbox rewards for better randomization.
- [ ] More animations coming soon!
