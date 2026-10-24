# OneGodian Games WordPress Plugin — v0.1.0 Candidate

A bundled **2D and 3D browser-game integration layer** for WordPress, maintained in the canonical repository `ohi-stack/onegodian-games`.

## Installation

1. From a verified GitHub Actions run, obtain the **onegodian-games-plugin-candidate** ZIP artifact. The artifact wraps `onegodian-games.zip`; extract the plugin ZIP from it.
2. Install `onegodian-games.zip` in WordPress staging: Plugins → Add New → Upload Plugin.
3. Activate **OneGodian Games**, then create a test WordPress page.
4. Insert `[onegodian_games]` or `[onegodian_game game="rise-of-civilization"]`.
5. To show the public catalog, use `[onegodian_games_catalog]`.
6. Review the OneGodian Games entry in WordPress administration for current titles and shortcode help.

**Minimums:** WordPress 6.3, PHP 8.2. The plugin makes no database migration.

## Current game catalog

The versioned manifest is `games/catalog.json`, the plugin-local canonical source of playable-status and 2D/3D classification.

| ID | Type | Status | Game |
|---|---|---|---|
| `rise-of-civilization` | 2D | Playable (source; staging unverified) | Three-district platformer |
| `onegodian-3d-exploration` | 3D | Planned | No executable 3D game is distributed |

**Safety gate:** `[onegodian_game]` only launches items with `status: "playable"`, a `type` of `2d` or `3d`, an allowed relative `games/<id>/index.html` path, and a real bundled HTML file. Arbitrary URLs and unimplemented 3D titles cannot load.

## Input

Arrow Left/Right or A/D = run; Up, W or Space = jump/double-jump; R = restart. Touch buttons are available below the canvas. The goal flag ends the current district.

Gold coins represent **local game score only**, never wallet balances, ODC or any exchangeable asset. Sessions are ephemeral; no leaderboard, API calls or player account data is collected.

## Isolation and compatibility

The iframe uses `sandbox="allow-scripts"` **without** `allow-same-origin` to isolate scripts from WordPress cookies and sessions. A strict CSP may block this bundled inline game script; review it before staging deployment. Future 3D games must be independently tested with WebGL browser policies, asset loading under sandboxed opaque origins, mobile GPU budgets, error states and reduced-motion preferences. Adding a manifest entry alone does not activate a production 3D game.

## Developer checks

```sh
php -l plugin/onegodian-games/onegodian-games.php
php plugin/onegodian-games/tests/verify-plugin.php
node plugin/onegodian-games/tests/verify-platformer.mjs
node scripts/verify-catalog.mjs
```

## Release and rollback

See `docs/RELEASE.md`. Backup the existing site/plugin ZIP, stage, verify and record the build hash. If issues occur, disable the plugin, remove its shortcodes, and restore the preceding plugin version. Source/CI success does **not** confirm deployment to any WordPress site.
