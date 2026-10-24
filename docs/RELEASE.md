# OneGodian Games — Candidate Packaging and Deployment

## Source verification

```bash
php -l plugin/onegodian-games/onegodian-games.php
php plugin/onegodian-games/tests/verify-plugin.php
node plugin/onegodian-games/tests/verify-platformer.mjs
node scripts/verify-catalog.mjs
```

The GitHub Actions workflow `.github/workflows/onegodian-games.yml` runs these checks on pull requests and on changes to the target files.

## Packaging

The workflow creates `onegodian-games.zip` containing the directory `onegodian-games/` with the plugin bootstrap, validated catalog, and HTML game assets, excluding development tests and README. It uploads that ZIP as the workflow artifact `onegodian-games-plugin-candidate`.

Manual equivalent from the repository root:

```bash
mkdir -p dist
cd plugin
zip -r ../dist/onegodian-games.zip onegodian-games -x 'onegodian-games/tests/*' 'onegodian-games/README.md'
```

The artifact download may be a wrapper ZIP containing `onegodian-games.zip`. Use the inner ZIP for WordPress's Upload Plugin function.

## Staging acceptance

1. Confirm staging WordPress >=6.3 and PHP >=8.2, current plugin baseline, compatibility and full backup.
2. Install ZIP in WordPress staging, activate and check admin area. No activation errors or unexpected settings changes.
3. Create a test page with `[onegodian_games]`, another with `[onegodian_games_catalog]`, and one using `[onegodian_game game="rise-of-civilization"]`.
4. Confirm all three districts: run, jump, double-jump, moving platforms, eight collectible coins/level, hazard deaths, three lives, checkpoint respawn, level selection and goal-flag completion. Check P/Esc Pause → Resume, the elapsed timer and distance progress, and medal summaries. Verify the title renders exactly **ONEGODIA: Rise of Civilization — 2D Platformer** in the catalog, shortcode heading and game metadata.
5. Confirm `onegodian-3d-exploration` is visible as **Planned** and cannot be launched using the game shortcode.
6. Check keyboard and touch interaction on 1440×900 desktop and a real narrow-screen iPhone/Android device. Verify accessibility labels, focus, non-WebGL fallback for future games, console errors, CSP, no WordPress session leakage and cache behavior.
7. Record passing screenshots, exact source commit, ZIP checksum, WordPress/PHP versions, browser/device results and rollback path.

## Production gate

Only after staging acceptance, a human-approved release candidate may be installed on the intended production WordPress site. Update the public catalog/page links and verify live URLs, active plugin version, responsiveness, performance and source provenance. Do **not** call the release production-ready just because the code is on GitHub or CI is green.

## Rollback

Disable the plugin and restore the previous ZIP and original pages if the deployment regresses. The v0.2.0 candidate makes no database migrations and stores no user progress. If the site did not previously have this plugin, rollback is removal of the new pages/shortcodes and plugin files. Keep the previous backup and deployment evidence.
