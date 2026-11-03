# OneGodian Games — multi-game import architecture (candidate)

**Owner:** ONEGODIAN, LLC • **Author/originator:** One Gregory OneGodian • **Status:** architecture and onboarding contract; no newly imported games claimed.

## Preserve existing runtime

The existing `rise-of-civilization` slug, manifest, WordPress shortcodes, isolated iframe `sandbox="allow-scripts"`, and three-level 2D platformer remain unchanged. This repository is a game distribution/catalog and WordPress presentation layer, not the source of truth for the larger `onegodia-rise-of-the-digital-world` game. Never overwrite an existing game in order to ingest another.

## Package-per-game layout

```text
games-source/<slug>/                  # optional source project (HTML/Canvas, Phaser, Three.js, React/Vite, etc.)
  README.md                           # attribution, license/rights, toolchain, tested controls
  package.json                        # if built with Node
  public/...
  src/...
plugin/onegodian-games/games/
  catalog.json                        # canonical curated registry
  <slug>/
    index.html                        # self-contained browser entry; local relative URLs
    assets/...                        # compiled JS/CSS/images/audio; no CDN dependency by default
```

For HTML-only games, copy reviewed HTML and local assets into the packaged game folder; for React/Vite/Phaser/Three.js games, build into that folder with *relative* base URLs (`./`), then validate inside the origin-isolated iframe. Keep large source trees outside the installable WordPress plugin ZIP. Each game must have its own slug, entry, version, and authored rights documentation. No arbitrary external iframe URLs or uploads from anonymous users.

## Catalog contract

Use the existing `schema_version: 1.0.0` and `games[]` array. Required fields for every imported candidate: `id` (unique lowercase slug), `title`, `type` (`2d` or `3d`), `category`, `status` (`planned` or `playable`), `entry` (null for planned; `games/<id>/index.html` for playable), `version` and `description`. Optional presentation fields: `controls` (plain-language keyboard/touch instructions), `orientation` (`landscape` or `portrait`), `minimum_height` (integer 320–1000), `input` (keyboard/touch/gamepad), `age_rating` (editorial), `author`, `rights`, `source_repository`, `release_status`. Never mark executable presence as proof of deployed production status.

Example metadata (illustrative, NOT added to live catalog):

```json
{
  "id": "example-game",
  "title": "Example Game",
  "type": "2d",
  "category": "arcade",
  "status": "planned",
  "entry": null,
  "version": "0.1.0",
  "description": "Pending import and review.",
  "controls": "Arrow keys to move; tap buttons on mobile.",
  "author": "One Gregory OneGodian",
  "rights": "Review before public distribution"
}
```

## Import pipeline

1. Inventory each game: original source, framework, dependencies, current runnable version, assets, ownership/third-party licenses, original author and completion status.
2. Assign stable slug; check collisions and preserve original commit/version history.
3. Audit HTML/JS dependency tree and check for remote scripts, data exfiltration, third-party assets, secrets, unsafe postMessage handlers, unlicensed media and arbitrary iframe navigation.
4. Build to local `games/<slug>/index.html` with relative asset references and no required WordPress cookies; use adapter only when necessary.
5. Test in `sandbox="allow-scripts"`, without `allow-same-origin`; check network/CSP, canvas audio unlock, touch handling, keyboard focus and iframe resize.
6. Add a catalog record; keep `planned` until a reviewed, bundled, verified executable is available.
7. Run catalog validator, PHP syntax and smoke checks; verify WordPress activation and upgrade, catalog browsing, single-game shortcode, cross-game isolation and browser/mobile playtests.
8. Record release ZIP SHA-256, game asset checksums, deployed revision, screenshots/recordings, license review and rollback instructions before enabling production.

## Plugin and public routes

- `[onegodian_games]` renders the established default platformer, preserving compatibility.
- `[onegodian_game game="slug"]` launches a single verified catalog entry.
- `[onegodian_games_catalog]` provides the full catalog, showing planned entries as unavailable.
- Proposed WordPress page routes: `/games/` (catalog), `/games/<slug>/` (shortcode-driven dedicated pages), `/games/how-to-play/` (help). These routes are **not auto-created by this architecture document**.
- Admin: OneGodian Games → Inventory and shortcode instructions already exist; editorial import UI, search/filter, screenshots, releases and metrics are future work, not current functionality.

## Platform boundaries

WordPress plugin owns game listings and presentation. Game runtimes own ephemeral gameplay only. OneGodian Members owns member identity and consented account context; future achievements/saves require separate authenticated, server-authoritative game services and explicit privacy review. OneGodian Platform/API supplies scoped interoperability, not game-state authority. OneGodian Visual Studio may compose pages containing game shortcodes. Commerce/licensing, if later enabled, belongs to ONEGODIAN, LLC and approved WooCommerce integrations. **No ODeFi/ODC wallet, cash-out, trading, wagering or financially redeemable scores** are included.

## Acceptance and rollback

Import at least one additional independently authored test game without changing the default game's code; demonstrate two games launching by shortcode, no broken source-relative assets, no cross-game storage sharing, no remote dependency requirement, and independent isolation of keyboard/touch behavior. Verify responsive view on real iPhone Safari and Android Chrome, desktop browsers, screen-reader text equivalents, reduced-motion, offline fail states, cache busting, CSP and plugin uninstall/reinstall. In staging, restore prior plugin ZIP and catalog as rollback rehearsal; promotion requires a human-reviewed exact-SHA build, CI evidence and deployment verification.
