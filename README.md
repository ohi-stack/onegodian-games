# OneGodian Games™

**Official OneGodian Games platform and WordPress plugin.** Develops interactive **2D and 3D browser games**, game catalogs, player experiences, achievements, progression systems, and reusable game engines for the OneGodian ecosystem.

**Canonical repository:** [ohi-stack/onegodian-games](https://github.com/ohi-stack/onegodian-games)  
**Organization:** ONEGODIAN, LLC  
**Project status:** 2D WordPress plugin v0.2.0 candidate; 3D modular-game support is a defined architecture and **not yet a playable implementation**.

## What is implemented

The first bundled game is **ONEGODIA: Rise of Civilization — 2D Platformer**, a 2D side-scrolling platformer with three districts: Awakening, Sky Gardens, and Citadel. Players can run, jump, double-jump, ride moving platforms, collect coins, avoid hazards, activate checkpoints, lose lives, and reach goal flags. The v0.2.0 upgrade adds P/Esc pause/resume, a run timer, a route-progress display, session-only district medals, animated collectible feedback, and a OneGodian human-civilization skyline. The game supports desktop keyboard controls, touchscreen controls, and restart/level selection.

The game is bundled with a self-contained **OneGodian Games WordPress plugin** that provides:

- `[onegodian_games]` — embeds the default playable game.
- `[onegodian_game game="rise-of-civilization"]` — embeds a specific available game.
- `[onegodian_games_catalog]` — displays the catalog, with playable and roadmap titles clearly distinguished.
- A WordPress **OneGodian Games** administration page with game inventory and embed instructions.
- An isolated iframe player (`sandbox="allow-scripts"`), a bundled local runtime, catalog validation and explicit playable-status checks.
- PHP/Node smoke tests and a CI pipeline generating a WordPress-installable ZIP review artifact.

**Canonical game ID:** `rise-of-civilization` (unchanged, preserving existing shortcode compatibility).

## 2D and 3D platform architecture

| Domain | Responsibility | Status |
|---|---|---|
| 2D browser games | Canvas/Web-based playable experiences and reusable controls | **First playable source included** |
| 3D browser games | WebGL/Three.js or equivalent immersive standalone browser experiences | **Planned; no 3D executable bundled** |
| Game catalog | Game ID, dimension (2D/3D), entry file and release status | **Implemented in manifest** |
| WordPress integration | Plugin, administration page, shortcodes and isolated local embeds | **Implemented in source, staging pending** |
| Player experience | Keyboard/touch controls, HUD, level flow and non-monetary points | **Implemented for first game** |
| Achievements / accounts | Authenticated identity, earned achievements, durable progression | **Planned; no backend/identity storage yet** |
| Reusable game engines | Common input, physics, rendering adapters and game packages | **Future extraction; do not claim production engine** |
| Multiplayer / shared state | Server-authoritative sessions and multiplayer features | **Not included** |

**Important separation:** The larger immersive title **Onegodia: Rise of the Digital World™** retains its own canonical source in `ohi-stack/onegodia-rise-of-the-digital-world`. OneGodian Games is the dedicated browser-game catalog and WordPress integration product, not a fork or replacement of that world's authoritative progression or game services.

## Repository map

```text
onegodian-games/
├── .github/workflows/onegodian-games.yml     # Verification and candidate ZIP
├── README.md
├── docs/
│   ├── ARCHITECTURE.md                        # Ownership and 2D/3D standards
│   └── RELEASE.md                             # WordPress staging and rollback
└── plugin/
    └── onegodian-games/
        ├── onegodian-games.php                # WP bootstrap, shortcodes, admin
        ├── README.md                          # Plugin guide
        ├── games/
        │   ├── catalog.json                   # Machine-readable game registry
        │   └── rise-of-civilization/
        │       └── index.html                 # Self-contained 2D platformer
        └── tests/
            ├── verify-platformer.mjs
            └── verify-plugin.php
```

## Development and verification

```bash
php -l plugin/onegodian-games/onegodian-games.php
php plugin/onegodian-games/tests/verify-plugin.php
node plugin/onegodian-games/tests/verify-platformer.mjs
node scripts/verify-catalog.mjs
```

CI packages only the plugin runtime (excluding source-level test files) as `onegodian-games.zip` for WordPress staging installation; see [release procedure](docs/RELEASE.md). The ZIP is a **build artifact**, not proof of installation on OneGodian.com, OneGodian.org or any other live site.

## Accessibility, security and game economy

- 2D gameplay supports keyboard and touch. Future 3D modules must support input fallbacks, reduced motion, appropriate labels and accessible non-WebGL failure handling.
- Games are loaded only from a validated **local asset entry**, with explicit `playable` status. Planned 3D titles cannot launch from a WordPress shortcode.
- Gold coins are **fictional local game points**, not OneGodian Digital Coin (ODC) or a tradeable asset. This plugin does not include wallets, tokens, payments, NFT-style assets, gambling, real-money rewards or authentication.
- No external network APIs, telemetry, database migrations, cookies or persistent player state are part of the bundled platformer.
- The repository is public, but **no open-source license has been granted by a repository LICENSE file**. Confirm IP and distribution terms before accepting contributions or distributing derivative builds.

## Definition of done

A version is production-ready **only after source tests, WordPress staging activation, mobile/desktop acceptance, security review, deployment, site verification and rollback evidence** are complete. Passing CI establishes source/build quality, not public production status.
