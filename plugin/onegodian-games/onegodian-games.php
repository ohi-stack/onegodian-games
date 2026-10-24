<?php
/**
 * Plugin Name: OneGodian Games
 * Description: OneGodian 2D and 3D browser-game catalog, including the ONEGODIA 2D platformer.
 * Version: 0.2.0
 * Requires at least: 6.3
 * Requires PHP: 8.2
 * Author: ONEGODIAN, LLC
 * Text Domain: onegodian-games
 */

defined('ABSPATH') || exit;

/**
 * Catalog authority: games/catalog.json.
 * The only launchable games are explicitly marked playable, include a safe
 * relative entry path, and have a bundled local HTML asset.
 *
 * No currency, wallet, purchase, login, telemetry, persistent account state,
 * or remote arbitrary iframe URL is registered by this plugin.
 */
final class OneGodian_Games {
    private const DEFAULT_GAME = 'rise-of-civilization';
    private const MANIFEST_PATH = __DIR__ . '/games/catalog.json';

    public static function register(): void {
        add_shortcode('onegodian_games', [self::class, 'render_game']);
        add_shortcode('onegodian_game', [self::class, 'render_game']);
        add_shortcode('onegodian_games_catalog', [self::class, 'render_catalog']);
        add_action('admin_menu', [self::class, 'register_admin_menu']);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private static function catalog(): array {
        if (!is_readable(self::MANIFEST_PATH)) {
            return [];
        }
        $contents = file_get_contents(self::MANIFEST_PATH);
        if (!is_string($contents)) {
            return [];
        }
        $document = json_decode($contents, true);
        if (!is_array($document) || !isset($document['games']) || !is_array($document['games'])) {
            return [];
        }
        return array_values(array_filter($document['games'], 'is_array'));
    }

    /**
     * Never launch remote URLs, traversal paths, or items that have not
     * reached explicitly documented playable status.
     *
     * @return array<string,mixed>|null
     */
    private static function playable_game(string $id): ?array {
        foreach (self::catalog() as $game) {
            if (($game['id'] ?? null) !== $id || ($game['status'] ?? null) !== 'playable') {
                continue;
            }
            if (!in_array(($game['type'] ?? ''), ['2d', '3d'], true)) {
                continue;
            }
            $entry = $game['entry'] ?? null;
            if (!is_string($entry) || !preg_match('~^games/[a-z0-9-]+/index\.html$~D', $entry)) {
                continue;
            }
            if (!is_file(__DIR__ . '/' . $entry)) {
                continue;
            }
            return $game;
        }
        return null;
    }

    /**
     * [onegodian_games] — launch default playable platformer
     * [onegodian_game game="rise-of-civilization"] — launch a named game
     */
    public static function render_game($attributes = []): string {
        $attributes = shortcode_atts(
            ['game' => self::DEFAULT_GAME, 'title' => ''],
            (array) $attributes,
            'onegodian_game'
        );

        $id = sanitize_key((string) $attributes['game']);
        $game = self::playable_game($id);
        if ($game === null) {
            return '<p class="onegodian-games-error">' .
                esc_html__('That OneGodian game is not available.', 'onegodian-games') .
                '</p>';
        }

        $title = sanitize_text_field((string) $attributes['title']);
        if ($title === '') {
            $title = sanitize_text_field((string) ($game['title'] ?? 'OneGodian Game'));
        }
        $src = plugins_url((string) $game['entry'], __FILE__);
        $type = (string) $game['type'];

        return '<section class="onegodian-games-embed" aria-label="' .
            esc_attr($title) .
            '" style="max-width:1100px;margin:1.5rem auto;color:#f5deb1">' .
            '<h2 style="margin:0 0 12px;font-size:clamp(1.25rem,3vw,2rem)">' .
            esc_html($title) .
            '</h2>' .
            '<iframe title="' . esc_attr($title) .
            '" src="' . esc_url($src) .
            '" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts" ' .
            'data-onegodian-game-type="' . esc_attr($type) . '" ' .
            'style="display:block;width:100%;height:650px;border:1px solid #b9954c;border-radius:14px;background:#090e1d" ' .
            'allowfullscreen></iframe>' .
            '<p style="font-size:.85rem;margin:8px 0">' .
            esc_html__('ONEGODIA controls: Arrow keys or A/D to move, W/Up/Space to jump and double-jump, P/Esc to pause, and R to restart. Touch controls are available.', 'onegodian-games') .
            '</p></section>';
    }

    /**
     * [onegodian_games_catalog] — a public catalog of verified playable games
     * and clearly designated roadmap concepts.
     */
    public static function render_catalog(): string {
        $html = '<section class="onegodian-games-catalog"><h2>' .
            esc_html__('OneGodian Games Catalog', 'onegodian-games') . '</h2>';
        foreach (self::catalog() as $game) {
            $id = sanitize_key((string) ($game['id'] ?? ''));
            $title = sanitize_text_field((string) ($game['title'] ?? $id));
            $type = (string) ($game['type'] ?? 'unknown');
            if (!in_array($type, ['2d', '3d'], true)) {
                continue;
            }
            $playable = self::playable_game($id) !== null;
            $html .= '<article style="margin:1rem 0;padding:1rem;border:1px solid #7d6742;border-radius:12px">' .
                '<h3>' . esc_html($title) . '</h3><p>' . esc_html(strtoupper($type)) . ' · ' .
                ($playable ? esc_html__('Playable', 'onegodian-games') :
                    esc_html__('Planned — not playable', 'onegodian-games')) . '</p>';
            if ($playable) {
                $html .= self::render_game(['game' => $id, 'title' => $title]);
            } else {
                $html .= '<p>' . esc_html__('This experience is on the roadmap and is not active.', 'onegodian-games') . '</p>';
            }
            $html .= '</article>';
        }
        return $html . '</section>';
    }

    public static function register_admin_menu(): void {
        add_menu_page(
            __('OneGodian Games', 'onegodian-games'),
            __('OneGodian Games', 'onegodian-games'),
            'manage_options',
            'onegodian-games',
            [self::class, 'render_admin'],
            'dashicons-games',
            65
        );
    }

    public static function render_admin(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>' .
            esc_html__('OneGodian Games', 'onegodian-games') .
            '</h1><p>' .
            esc_html__('WordPress 2D/3D game catalog and sandboxed game embeds.', 'onegodian-games') .
            '</p><p><code>[onegodian_games]</code> · <code>[onegodian_games_catalog]</code> · ' .
            '<code>[onegodian_game game="rise-of-civilization"]</code></p><ul>';
        foreach (self::catalog() as $game) {
            $id = sanitize_key((string) ($game['id'] ?? ''));
            $title = sanitize_text_field((string) ($game['title'] ?? 'Untitled'));
            echo '<li>' . esc_html($title) . ' — ' .
                (self::playable_game($id) === null
                    ? esc_html__('Planned / not launchable', 'onegodian-games')
                    : esc_html__('Playable / local asset verified', 'onegodian-games')) .
                '</li>';
        }
        echo '</ul><p>' .
            esc_html__('Gold coins are non-monetary in-game collectibles. No wallets, tokens or payments are connected.', 'onegodian-games') .
            '</p></div>';
    }
}

OneGodian_Games::register();
