<?php
declare(strict_types=1);
// Validates compiled WASM game registration in an isolated temporary plugin copy.
$root = dirname(__DIR__);
$source = $root . '/games/onegodian-wasm-demo';
if (!is_file($source . '/index.html') || !is_file($source . '/index.wasm') || !is_file($source . '/index.js')) {
    fwrite(STDERR, "Build C++ assets first\n");
    exit(1);
}
$tmp = sys_get_temp_dir() . '/og-wasm-' . bin2hex(random_bytes(6));
mkdir($tmp . '/games/onegodian-wasm-demo', 0700, true);
foreach (['index.html', 'index.js', 'index.wasm'] as $name) {
    copy($source . '/' . $name, $tmp . '/games/onegodian-wasm-demo/' . $name);
}
$catalog = json_decode(file_get_contents($root . '/games/catalog.json'), true, 512, JSON_THROW_ON_ERROR);
$catalog['games'][] = [
    'id' => 'onegodian-wasm-demo', 'title' => 'OneGodian C++ WASM Test',
    'type' => '2d', 'category' => 'technical-demo', 'status' => 'playable',
    'entry' => 'games/onegodian-wasm-demo/index.html',
    'controls' => 'C++ canvas demo; no player controls.', 'minimum_height' => 500
];
file_put_contents($tmp . '/games/catalog.json', json_encode($catalog, JSON_THROW_ON_ERROR));
copy($root . '/onegodian-games.php', $tmp . '/onegodian-games.php');
define('ABSPATH', $tmp);
$GLOBALS['shortcodes'] = [];
function add_shortcode($tag, $callback) { $GLOBALS['shortcodes'][$tag] = $callback; }
function add_action(...$args) {}
function shortcode_atts($defaults, $attrs, $tag) { return array_merge($defaults, array_intersect_key($attrs, $defaults)); }
function sanitize_key($value) { return preg_replace('/[^a-z0-9_-]/', '', strtolower($value)); }
function sanitize_text_field($value) { return strip_tags($value); }
function esc_html($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function esc_attr($value) { return esc_html($value); }
function esc_url($value) { return esc_html($value); }
function esc_html__($value, $domain) { return $value; }
function __($value, $domain) { return $value; }
function plugins_url($path, $file) { return 'https://example.test/wp-content/plugins/onegodian-games/' . $path; }
require $tmp . '/onegodian-games.php';
$html = ($GLOBALS['shortcodes']['onegodian_game'])(['game' => 'onegodian-wasm-demo']);
$checks = [
    str_contains($html, 'games/onegodian-wasm-demo/index.html'),
    str_contains($html, 'sandbox="allow-scripts"'),
    !str_contains($html, 'allow-same-origin'),
    str_contains($html, 'height:500px'),
    str_contains($html, 'C++ canvas demo; no player controls.')
];
foreach (['index.html', 'index.js', 'index.wasm'] as $name) unlink($tmp . '/games/onegodian-wasm-demo/' . $name);
rmdir($tmp . '/games/onegodian-wasm-demo');
unlink($tmp . '/games/catalog.json');
rmdir($tmp . '/games');
unlink($tmp . '/onegodian-games.php');
rmdir($tmp);
if (in_array(false, $checks, true)) { fwrite(STDERR, "WASM WordPress shortcode test failed\n"); exit(1); }
echo "WASM WordPress sandboxed shortcode contract passed\n";
