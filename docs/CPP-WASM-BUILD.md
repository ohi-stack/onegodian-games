# OneGodian Games — C++/WebAssembly adapter

Source template: `games-source/cpp/onegodian-wasm-demo/`. Activate Emscripten SDK then run `bash scripts/build-cpp-games.sh`. CMake generates local `index.html`, `index.js`, `index.wasm` in the plugin game directory. The example is a canvas animation demonstrator; not a published playable game. Do not treat a compiled asset as production acceptance.

WordPress gate: add a reviewed catalog entry only after build, package, security and ownership checks. Existing `[onegodian_game game="slug"]` uses a local entry and `sandbox="allow-scripts"` without `allow-same-origin`. Validate WordPress static JS/WASM HTTP 200, preferably WASM MIME `application/wasm`, CSP/fetch compliance, real iframe canvas animation, status `data-wasm-ready="true"`, browser console and network, iPhone/Android and desktop, PHP shortcode smoke tests, ZIP installation, and rollback. Keep third-party CDNs, accounts, payments, ODC and persistent game state out of the template.


## Sandboxed WebAssembly delivery requirement
The WordPress iframe intentionally uses `sandbox="allow-scripts"` without `allow-same-origin`. Its JavaScript executes with an opaque origin (`Origin: null`), even when the script and WASM are served from the same hostname. For non-credentialed, public game assets, configure the web server or CDN to respond to requests for `/wp-content/plugins/onegodian-games/games/*/*.wasm` with `Access-Control-Allow-Origin: *`. Do not add `allow-same-origin` to the player iframe and do not send cookies or credentials with these fetches. A WordPress PHP action does **not** automatically add headers to static .wasm files served directly by Apache/nginx/CDN.

Staging must confirm WASM response status 200, appropriate MIME (`application/wasm`), `Access-Control-Allow-Origin: *`, and successful initialization from the actual shortcode iframe. The isolated Chromium test models that expected static server behavior but is not a substitute for staging evidence.
