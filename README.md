
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

![Shuffle Animation Preview](https://github.com/user-attachments/assets/41a43d72-697f-48f7-86bd-c0abc7c63869)

### No Animation (Animation ID: `0`)
This animation is more straightforward, where players simply click to claim a lootbox without any special effects.

## Setup & Configuration

The plugin is simple to configure, with lootboxes stored in the `lootboxes` folder inside your plugin's data directory. Each lootbox is defined by a `.yml` file, where you can customize rewards, animations, and other settings.

### Example Lootbox Configuration:

```yaml
item:
  material: CHEST # Material of the lootbox
  name: "&r&cExample Lootbox"
  lore:
    - "&r&7This is an example lootbox"
animation:
  type: 0 # Type of animation of the lootbox
  settings: # Settings will vary depending on the animation type
    message: "&r&dMartian&fLootbox&8 | &r&6&l* &e&l{AMOUNT}x {ITEM}"
    broadcast:
      enable: true
      header: '&r&e&l(!) &e{PLAYER} has just open the &c&lExample Lootbox &eand has
        gotten'
      message: '&r&6&l* &e&l{AMOUNT}x {ITEM}'
    sound:
      start: random.click
      prize: player.levelup
    skippable: false
    reward-preview:
      enable: true
      items:
        filler: "BLACK_STAINED_GLASS_PANE"
        name: " "
        navigation:
          back:
            material: "RED_DYE"
            name: "&r&c&lBack"
            lore:
              - "&r&7Click to go back"
          next:
            material: "GREEN_DYE"
            name: "&r&a&lNext"
            lore:
              - "&r&7Click to go next"

rewards:
  - item: diamond
    amount: 1
  - item: diamond_sword
    name: "&r&cDiamond Sword"
  - item: diamond_helmet
    name: "&r&cDiamond Helmet"
    amount: 1
    enchantments:
      - enchant: "protection"
        level: 4
  - item: paper
    amount: 1
    name: "&r&cBank Note"
    lore:
      - "&r&7Right click to claim!"
    nbt:
      tag: "banknote"
      value: 1000
  - item: gold_ingot
  - item: iron_ingot
  - item: oak_plank
  - item: dirt
  - item: grass_block
  - item: stone
  - item: cobblestone
  - item: obsidian
  - item: netherrack
  - item: end_stone
  - item: end_stone_brick
  - item: end_stone_brick_slab
  - item: stick
  - item: cobweb
  - item: sugar_cane
  - item: glowstone_dust
  - item: gold_nugget
  - item: iron_nugget
  - item: iron_block
  - item: gold_block
  - item: diamond_block
  - item: emerald_block
  - item: netherite_block
  - item: coal_block
  - item: redstone_block
  - item: lapis_block
  - item: lapis_lazuli
  - item: cactus
bonus-rewards: 
  - item: diamond_sword
    enchantments:
      - enchant: "sharpness"
        level: 6
```

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
[Watch Shuffle Animation Preview](https://github.com/user-attachments/assets/41a43d72-697f-48f7-86bd-c0abc7c63869)

**No Animation (ID: 0)**  
This animation simply displays the lootbox with no shuffle effects. Players can click to claim their lootboxes directly without any visual effects.

## TODO

- [ ] Implement chances on lootbox rewards for better randomization.
- [ ] More animations coming soon!
