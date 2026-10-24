<?php
declare(strict_types=1);

define('ABSPATH', __DIR__);
$GLOBALS['shortcodes'] = [];
$GLOBALS['actions'] = [];

function add_shortcode(string $tag, callable $callback): void {
    $GLOBALS['shortcodes'][$tag] = $callback;
}
function add_action(string $hook, callable $callback): void {
    $GLOBALS['actions'][$hook] = $callback;
}
function shortcode_atts(array $defaults, array $attributes, string $shortcode): array {
    return array_merge($defaults, array_intersect_key($attributes, $defaults));
}
function sanitize_key(string $value): string {
    return preg_replace('/[^a-z0-9_\-]/', '', strtolower($value));
}
function sanitize_text_field(string $value): string {
    return strip_tags($value);
}
function esc_html(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function esc_attr(string $value): string {
    return esc_html($value);
}
function esc_url(string $value): string {
    return esc_html($value);
}
function esc_html__(string $value, string $domain): string {
    return esc_html($value);
}
function __(string $value, string $domain): string {
    return $value;
}
function plugins_url(string $path, string $file): string {
    return 'https://example.test/wp-content/plugins/onegodian-games/' . $path;
}
function current_user_can(string $capability): bool {
    return $capability === 'manage_options';
}
function add_menu_page(...$args): void {}

require dirname(__DIR__) . '/onegodian-games.php';

function check(bool $condition, string $message): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
        exit(1);
    }
}

check(isset($GLOBALS['shortcodes']['onegodian_games']), 'catalog shortcode registered');
check(isset($GLOBALS['shortcodes']['onegodian_game']), 'single-game shortcode registered');
check(isset($GLOBALS['actions']['admin_menu']), 'admin menu registered');

$render = $GLOBALS['shortcodes']['onegodian_games'];
$html = $render([]);
check(str_contains($html, '<iframe'), 'shortcode renders iframe');
check(str_contains($html, 'sandbox="allow-scripts"'), 'iframe is sandboxed');
check(!str_contains($html, 'allow-same-origin'), 'iframe does not inherit WordPress origin');
check(str_contains($html, 'games/rise-of-civilization/index.html'), 'game asset path is correct');
check(str_contains($html, 'data-onegodian-game-type="2d"'), 'manifest drives 2D game type');
check(str_contains($html, 'P/Esc to pause'), 'shortcode documents new keyboard controls');
check(str_contains($html, 'ONEGODIA: Rise of Civilization — 2D Platformer'), 'title derives from manifest');
check(str_contains($render(['game' => 'onegodian-3d-exploration']), 'not available'), 'planned 3D game cannot execute');
check(str_contains($render(['game' => '../unsafe']), 'not available'), 'path traversal cannot select a game');
check(!str_contains($html, 'allow-top-navigation'), 'iframe cannot navigate parent window');
$catalogHtml = $GLOBALS['shortcodes']['onegodian_games_catalog']([]);
check(str_contains($catalogHtml, 'OneGodian Games Catalog'), 'public catalog shortcode registered');
check(str_contains($catalogHtml, 'OneGodian 3D Exploration'), 'public catalog includes 3D roadmap item');
check(str_contains($catalogHtml, 'Planned — not playable'), '3D game is accurately classified');
check(substr_count($catalogHtml, '<iframe') === 1, 'only the playable 2D game is launchable');

check(str_contains($render(['game' => 'missing']), 'not available'), 'unknown games fail closed');
check(!str_contains($render(['title' => '<script>alert(1)</script>']), '<script>'), 'title cannot inject HTML');

ob_start();
OneGodian_Games::render_admin();
$admin = ob_get_clean();
check(str_contains($admin, '[onegodian_games]'), 'admin displays shortcode');
echo "OneGodian Games: WordPress plugin smoke checks passed\n";
