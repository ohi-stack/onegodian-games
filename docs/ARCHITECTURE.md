# OneGodian Games™ — Architecture v0.1.0

## Product boundaries

The **OneGodian Games** repository owns independently packaged **2D and 3D browser games**, the game catalog, and WordPress presentation/integration. It does not inherit the main Onegodia 3D world's server authority or game-session systems.

| Layer | Authority | Verified current implementation |
|---|---|---|
| WordPress game plugin | `plugin/onegodian-games/onegodian-games.php` | Shortcodes, game manifest validation, admin page, sandboxed player |
| Game release catalog | `plugin/onegodian-games/games/catalog.json` | One playable 2D title; one explicitly planned 3D entry |
| 2D runtime | `games/rise-of-civilization/index.html` | Standalone canvas platformer; local ephemeral score and checkpoint |
| 3D runtime | Per-game WebGL/Three.js project and build | Not implemented in this version |
| Cross-game identity, cloud saves, achievements | Authorized OneGodian Game Services (separate backend contracts) | Not implemented; no user accounts or persistent score records |
| WordPress site configuration | WordPress administrators | No live installation verified |
| Main Onegodia universe | `ohi-stack/onegodia-rise-of-the-digital-world` | Independent authoritative immersive product |

## Game manifest

`games/catalog.json` lists a game ID, name, 2D/3D type, category, status, local entry path and optional metadata. Status transitions require tested assets and human approval.

- **planned**: catalog can show a noninteractive roadmap entry; game shortcode cannot load it.
- **playable**: only when local `games/<slug>/index.html` exists and its entry path is validated. This means executable source exists, **not** that WordPress or production browser acceptance passed.
- Do not invent status keys such as `production` based on CI alone.

The existing WordPress entrypoint uses strict allowlisted local paths. It never accepts user-supplied remote game URLs.

## 2D game runtime standards

Use deterministic state transitions, input abstraction for arrow/WASD/touch, double-jump control, moving-platform displacement, collision checks, death/respawn, visible score state and a level completion condition. Use game-only numerical points. An accessible game description, focusable controls and an alternative instruction paragraph must be visible outside the canvas. Mobile touch targets should be comfortably tappable.

The included proof game uses three fixed levels and an animation loop with clamped frame delta; it does not save progress or send telemetry. Source smoke tests exercise jump/double-jump, pickups, hazards, checkpoint, moving platforms, goal, level select and reset.

## 3D game runtime standards (future)

Build distinct per-game bundles rather than sharing an unversioned global Three.js instance within WordPress:

1. Render through a documented WebGL/R3F/Three.js adapter and manage GPU resources on disposal.
2. Keep models, textures and game scenes under a path that works in a **sandboxed, origin-isolated iframe**. Validate cross-origin fetch/image/canvas policies against production CSP. Do not add `allow-same-origin` casually.
3. Provide scalable quality settings, input remapping and touch/gamepad fallback where relevant, reduced motion, lost-context recovery and static error/fallback UI for non-WebGL browsers.
4. Keep collider data separate from decorative meshes, and define camera, character, checkpoint, score and progression contracts.
5. For user accounts, persistent achievements or multiplayer, integrate through an authenticated service with verifiable ownership and server-side authorization; never trust client-only points.
6. Version game asset manifests and record desktop + real-device GPU performance, accessibility acceptance and rollback evidence before publishing.

The current 3D catalog entry is **planned** and explicitly not a playable demo.

## Ownership, legal and security

ONEGODIAN, LLC maintains the Games platform as its own software and creative work. No repository LICENSE grant is currently present; do not infer open-source permission from public access. Embedded games must not read WordPress authentication cookies. Administrative operations must use WordPress capabilities, nonces for state changes and audit trails where applicable.

ODC, NFT-style items, tradeable rewards, wallets, payments, casino/gambling or tokenized mechanics remain out of scope and disabled until separate legal, security and release review.

## Build and verification

Run all scripts listed in the root README, inspect the GitHub Actions artifact, stage-install the ZIP, and verify on desktop Chrome/Firefox and real mobile Safari/Chrome. No production or downloadable ZIP installation claim follows solely from a successful Actions build.
