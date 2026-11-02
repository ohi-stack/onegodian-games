# Importing an existing web game

This folder is the *compiled runtime* published by the OneGodian Games WordPress plugin. Place each reviewed game in `games/<unique-slug>/index.html`, with its own `assets/` directory. Never replace `rise-of-civilization`.

For a standalone HTML5 game: copy `index.html` and assets here after auditing third-party dependencies. For a Vite/React, Phaser, Babylon.js or Three.js game: build with relative asset base `./` and copy compiled output here. Test it in a cross-origin sandboxed iframe without `allow-same-origin`; many localStorage, cookie, service-worker and WebGL assumptions do not work there.

Register the game in `games/catalog.json`. Use `status:"planned", entry:null` until the local build is present and playtested; only switch to `playable` with `entry:"games/<slug>/index.html"`. Game-specific controls may be recorded as `controls`. See `docs/GAME-IMPORT-ARCHITECTURE.md` for full checks, rights, releases and rollback. No user-upload or arbitrary remote URL should become a public executable.
