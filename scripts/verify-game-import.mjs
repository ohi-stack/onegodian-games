import assert from 'node:assert/strict';
import { existsSync, readFileSync, readdirSync, statSync } from 'node:fs';
import { resolve, dirname, sep } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const runtime = resolve(root, 'plugin/onegodian-games');
const manifest = JSON.parse(readFileSync(resolve(runtime, 'games/catalog.json'), 'utf8'));
const ids = new Set();
for (const game of manifest.games) {
  assert.match(game.id, /^[a-z0-9-]+$/);
  assert.ok(!ids.has(game.id), 'duplicate slug: ' + game.id);
  ids.add(game.id);
  assert.ok(['2d', '3d'].includes(game.type));
  assert.ok(['planned', 'playable'].includes(game.status));
  if (game.controls !== undefined) assert.ok(typeof game.controls === 'string' && game.controls.length < 500);
  if (game.minimum_height !== undefined) assert.ok(Number.isInteger(game.minimum_height) && game.minimum_height >= 320 && game.minimum_height <= 1000);
  if (game.orientation !== undefined) assert.ok(['portrait', 'landscape'].includes(game.orientation));
  if (game.status === 'planned') { assert.equal(game.entry, null); continue; }
  assert.equal(game.entry, `games/${game.id}/index.html`);
  const entry = resolve(runtime, game.entry);
  assert.ok(entry.startsWith(runtime + sep) && existsSync(entry), 'missing playable entry: ' + entry);
  const html = readFileSync(entry, 'utf8');
  assert.match(html, /<html\b/i, 'game must have an HTML document: ' + game.id);
  assert.ok(!/<script[^>]+src=["']https?:\/\//i.test(html), 'review remote scripts: ' + game.id);
}
assert.ok(ids.has('rise-of-civilization'), 'default slug must remain stable');
console.log(`Import contract validated: ${ids.size} catalog entries; no new game activation implied.`);
