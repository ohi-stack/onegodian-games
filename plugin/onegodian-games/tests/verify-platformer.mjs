import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import vm from 'node:vm';

const html = readFileSync(resolve(import.meta.dirname, '../games/rise-of-civilization/index.html'), 'utf8');
const match = html.match(/<script>([\s\S]*?)<\/script>/);
assert.ok(match, 'game must include an inline script');
assert.match(html, /<canvas[^>]+id="stage"/);
assert.match(html, /data-key="jump"/);
assert.match(html, /id="level"/);
assert.match(html, /id="restart"/);
assert.match(html, /id="status"/);
assert.match(html, /id="overlay"/);
assert.doesNotMatch(html, /<script\s+src=/i, 'game must not depend on external JavaScript');

new vm.Script(match[1], { filename: 'platformer.js' });

const elements = new Map();
function element(id) {
  if (!elements.has(id)) {
    elements.set(id, {
      id, textContent: '', value: '', hidden: false, listeners: {},
      addEventListener(type, cb) { this.listeners[type] = cb; },
      getContext() {
        const gradient = { addColorStop() {} };
        return {
          createLinearGradient() { return gradient; }, fillRect() {}, beginPath() {},
          arc() {}, fill() {}, save() {}, restore() {}, translate() {}, rotate() {},
          moveTo() {}, lineTo() {}, fillText() {}
        };
      }
    });
  }
  return elements.get(id);
}
const root = {
  querySelector(selector) { return element(selector.slice(1)); },
  querySelectorAll() { return []; }
};
const windowListeners = {};
const sandbox = {
  document: {
    getElementById() { return root; },
    addEventListener() {},
    hidden: false
  },
  window: { addEventListener(type, fn) { windowListeners[type] = fn; } },
  requestAnimationFrame() {},
  console, Math, Set, Number, String
};
sandbox.globalThis = sandbox;
const hook = 'globalThis.__test = { loadLevel, update, requestJump, platforms, get: () => ({ player, level, levelIndex, coins, lives, checkpointActive, playing, mode }) };';
const instrumented = match[1].replace(/\}\)\(\);\s*$/, hook + '\n})();');
assert.notEqual(instrumented, match[1], 'test hook must attach to the actual game closure');
vm.runInNewContext(instrumented, sandbox, { timeout: 1000 });
const game = sandbox.__test;
assert.ok(game, 'game initializes');

game.loadLevel(0, true);
game.update(0.016);
assert.equal(game.get().player.grounded, true, 'player lands on ground');
game.requestJump();
game.update(0.016);
assert.equal(game.get().player.jumps, 1, 'first jump works');
assert.ok(game.get().player.vy < 0);
game.requestJump();
game.update(0.016);
assert.equal(game.get().player.jumps, 2, 'double jump works');

game.loadLevel(0, true);
game.get().player.x = 148;
game.get().player.y = 371;
game.update(0.016);
assert.equal(game.get().coins.size, 1, 'coin collection increments score');
assert.match(element('status').textContent, /Coins 1\/8/);

game.get().player.x = 1125;
game.get().player.y = 389;
game.update(0.016);
assert.equal(game.get().checkpointActive, true, 'checkpoint activates');
assert.equal(game.get().player.spawn, 1140);

game.get().player.x = 715;
game.get().player.y = 389;
game.update(0.016);
assert.equal(game.get().lives, 2, 'hazard costs one life');
assert.equal(game.get().player.x, 1140, 'death respawns at checkpoint');

const moving = game.platforms(1, 0).filter(p => p.moving);
assert.ok(moving.length >= 1);
assert.ok(moving.some(p => p.dx !== 0), 'moving platforms change position');

game.get().player.x = 2010;
game.get().player.y = 389;
game.update(0.016);
assert.equal(game.get().mode, 'won', 'goal flag completes level');

game.loadLevel(1, true);
assert.equal(game.get().levelIndex, 1, 'level selection changes district');
game.loadLevel(2, true);
assert.equal(game.get().levelIndex, 2);
assert.equal(game.get().lives, 3, 'restart resets lives');
assert.equal(game.get().coins.size, 0, 'restart resets coins');
console.log('OneGodian Games: platformer mechanics and HTML smoke checks passed');
