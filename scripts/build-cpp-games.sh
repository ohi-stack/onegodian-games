#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
command -v emcmake >/dev/null || { echo 'Activate emsdk' >&2; exit 1; }
source_dir="games-source/cpp/onegodian-wasm-demo"
build_dir="build/cpp/onegodian-wasm-demo"
destination="plugin/onegodian-games/games/onegodian-wasm-demo"
emcmake cmake -S "$source_dir" -B "$build_dir" -DCMAKE_BUILD_TYPE=Release
cmake --build "$build_dir" --parallel
mkdir -p "$destination"
for file in index.html index.js index.wasm; do
  test -s "$build_dir/dist/$file"
  cp "$build_dir/dist/$file" "$destination/$file"
done
node scripts/verify-wasm-assets.mjs
