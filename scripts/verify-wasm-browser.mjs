import assert from 'node:assert/strict';
import { createServer } from 'node:http';
import { readFileSync } from 'node:fs';
import { resolve, extname } from 'node:path';
import { chromium } from 'playwright';

const base = resolve(import.meta.dirname, '../plugin/onegodian-games/games/onegodian-wasm-demo');
const contentTypes = {'.html':'text/html; charset=utf-8','.js':'text/javascript; charset=utf-8','.wasm':'application/wasm'};
const server = createServer((req,res) => {
  const path = new URL(req.url,'http://localhost').pathname;
  if (path === '/') {
    res.setHeader('Content-Type','text/html; charset=utf-8');
    res.end('<!doctype html><html><body><iframe title="OneGodian C++ Demo" sandbox="allow-scripts" src="/games/index.html"></iframe></body></html>');
    return;
  }
  const file = path.replace('/games/','');
  if (!['index.html','index.js','index.wasm'].includes(file) || !path.startsWith('/games/')) { res.writeHead(404); res.end(); return; }
  res.setHeader('Content-Type',contentTypes[extname(file)]);
  // A sandboxed iframe has an opaque origin, including for its own static WASM fetch.
  // Public non-credentialed game assets must permit that origin to read the binary.
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.end(readFileSync(resolve(base,file)));
});
await new Promise(ok=>server.listen(0,'127.0.0.1',ok));
const address = 'http://127.0.0.1:' + server.address().port;
const browser = await chromium.launch({headless:true,args:['--no-sandbox']});
try {
  const page = await browser.newPage();
  const failures = [];
  page.on('pageerror', err=>failures.push('pageerror: ' + err.message));
  page.on('console', msg=>{ if(msg.type()==='error') failures.push('console: ' + msg.text()); });
  page.on('requestfailed', request=>failures.push('request: ' + request.url() + ' - ' + request.failure()?.errorText));
  await page.goto(address);
  const frame = page.frameLocator('iframe[title="OneGodian C++ Demo"]');
  try {
    await frame.locator('html[data-wasm-ready="true"]').waitFor({state:'attached',timeout:20000});
  } catch (error) {
    throw new Error('WASM runtime did not initialize; browser diagnostics: ' + JSON.stringify(failures) + '; status: ' + await frame.locator('#status').textContent().catch(()=>'<unavailable>'), {cause:error});
  }
  assert.equal(await frame.locator('#status').textContent(), 'Game running');
  assert.deepEqual(failures, []);
  assert.equal(await page.locator('iframe').getAttribute('sandbox'),'allow-scripts');
  console.log('Browser sandbox + C++ WASM loaded successfully');
} finally {
  await browser.close();
  await new Promise((ok,fail)=>server.close(err=>err?fail(err):ok()));
}
