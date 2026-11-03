# OneGodian Games — C++/WebAssembly adapter

Source template: `games-source/cpp/onegodian-wasm-demo/`. Activate Emscripten SDK then run `bash scripts/build-cpp-games.sh`. CMake generates local `index.html`, `index.js`, `index.wasm` in the plugin game directory. The example is a canvas animation demonstrator; not a published playable game. Do not treat a compiled asset as production acceptance.

WordPress gate: add a reviewed catalog entry only after build, package, security and ownership checks. Existing `[onegodian_game game="slug"]` uses a local entry and `sandbox="allow-scripts"` without `allow-same-origin`. Validate WordPress static JS/WASM HTTP 200, preferably WASM MIME `application/wasm`, CSP/fetch compliance, real iframe canvas animation, status `data-wasm-ready="true"`, browser console and network, iPhone/Android and desktop, PHP shortcode smoke tests, ZIP installation, and rollback. Keep third-party CDNs, accounts, payments, ODC and persistent game state out of the template.
