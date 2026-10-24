import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const path = resolve(root, 'plugin/onegodian-games/games/catalog.json');
const gameDirectory = resolve(root, 'plugin/onegodian-games');
const catalog = JSON.parse(readFileSync(path, 'utf8'));
const plugin = readFileSync(resolve(gameDirectory, 'onegodian-games.php'), 'utf8');
assert.equal(catalog.schema_version, '1.0.0', 'catalog schema is versioned');
assert.equal(catalog.authority, 'ohi-stack/onegodian-games');
assert.ok(Array.isArray(catalog.games));
assert.ok(catalog.games.length >= 2);
const ids = new Set();
let playable2d = 0;
let planned3d = 0;
for (const game of catalog.games) {
  assert.match(game.id, /^[a-z0-9-]+$/, 'IDs must be safe slugs');
  assert.ok(!ids.has(game.id), 'no duplicate game IDs');
  ids.add(game.id);
  assert.ok(['2d', '3d'].includes(game.type), 'only 2D/3D game types');
  assert.ok(['planned', 'playable'].includes(game.status), 'explicit release status');
  if (game.status === 'playable') {
    assert.match(game.entry, /^games\/[a-z0-9-]+\/index\.html$/);
    assert.ok(existsSync(resolve(gameDirectory, game.entry)), 'playable entry exists');
    assert.ok(game.entry.includes(game.id), 'game entry must match its ID');
    if (game.type === '2d') playable2d++;
  } else {
    assert.equal(game.entry, null, 'planned games must not have a launchable URL');
    if (game.type === '3d') planned3d++;
  }
}
assert.equal(playable2d, 1, 'one playable 2D reference game');
const platformer = catalog.games.find(game => game.id === 'rise-of-civilization');
assert.equal(platformer.title, 'ONEGODIA: Rise of Civilization — 2D Platformer');
assert.equal(platformer.version, '0.2.0');
for (const feature of ['run', 'jump', 'double-jump', 'pause-resume', 'elapsed-timer', 'distance-progress', 'session-medals']) {
  assert.ok(platformer.features.includes(feature), 'upgraded feature is described: ' + feature);
}
assert.equal(planned3d, 1, 'one clearly planned 3D game');
for (const token of ['onegodian_games_catalog', 'onegodian_games', 'onegodian_game', "['2d', '3d']", 'is_file', 'allow-scripts']) {
  assert.ok(plugin.includes(token), 'plugin must implement registry control: ' + token);
}
assert.ok(!plugin.includes('allow-same-origin'), 'no iframe-origin privilege escalation');
console.log('OneGodian Games: 2D/3D catalog source contracts passed');
