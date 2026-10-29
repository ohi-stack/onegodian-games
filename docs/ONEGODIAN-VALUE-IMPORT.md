# OneGodian Value: Control the Flow — import record

Status: catalog entry only. No Google AI Studio source, runnable game, or deployment was provided to this repository.

The screenshots show a prototype, but cannot establish its implementation, controls, save behavior, security, or ability to run in the WordPress plugin's sandboxed iframe. The catalog entry remains planned and unlaunchable until the actual source is imported and evaluated.

## Integration target

Game ID: `onegodian-value-control-the-flow`. The intended local runtime entry is `plugin/onegodian-games/games/onegodian-value-control-the-flow/index.html`, subject to source compatibility review. The catalog's entry remains null until this file and its dependencies are included and verified. Preserve existing game and shortcode behavior.

## Scope and boundaries

The requested game uses fictional residents, accounts, events and game credits; no real funds, wallet, financial accounts, providers or transaction execution. The prototype description includes story missions and an endless city mode, but these have not been inspected or verified here. The screenshots show a mobile layout issue that requires testing and correction once source is available.

## Import and acceptance

1. Obtain the original project source and associated assets from Google AI Studio. Inspect ownership, license, dependencies, secrets, external calls and build configuration before packaging.
2. Determine how to build a local, self-contained runtime and test compatibility with the existing `sandbox="allow-scripts"` iframe. Do not weaken sandbox permissions to make it work without a separate security review. Account for browser storage restrictions under opaque iframe origins.
3. Test start, input, mission completion and replay, reset, navigation, error/fallback and mobile screen sizes; check any actual save behavior and accurately describe its persistence. Validate no real financial access or endpoints.
4. Add runtime assets and catalog entry in a branch, update the game-specific instructions, run catalog/plugin and gameplay checks, and assess WordPress staging before describing it as playable.

Game cover art exists separately in the user's files; it has not been added to this repository.
