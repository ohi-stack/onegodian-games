import assert from 'node:assert/strict';
import { readFileSync, existsSync, statSync } from 'node:fs';
import { resolve } from 'node:path';
const dir = resolve(import.meta.dirname, '../plugin/onegodian-games/games/onegodian-wasm-demo');
for (const f of ['index.html', 'index.js', 'index.wasm']) {
  assert.ok(existsSync(resolve(dir, f)), 'missing ' + f);
  assert.ok(statSync(resolve(dir, f)).size > 8, 'empty ' + f);
}
assert.deepEqual([...readFileSync(resolve(dir, 'index.wasm')).subarray(0,8)], [0,97,115,109,1,0,0,0]);
const html = readFileSync(resolve(dir,'index.html'),'utf8');
const js = readFileSync(resolve(dir,'index.js'),'utf8');
assert.match(html, /<canvas\b/i);
assert.match(html, /index\.js/);
assert.ok(!/<script[^>]+src=["']https?:\/\//i.test(html));
assert.ok(js.includes('index.wasm'));
console.log('WASM binary and local loading assets validated');
