<?php
/**
 * Plugin Name: OneGodian Games
 * Description: Self-contained, accessible OneGodian browser games for WordPress.
 * Version: 0.1.0
 * Requires at least: 6.3
 * Requires PHP: 8.2
 * Author: ONEGODIAN, LLC
 * Text Domain: onegodian-games
 */

defined('ABSPATH') || exit;

final class OneGodian_Games {
    private const GAME_ID = 'rise-of-civilization';

    public static function register(): void {
        add_shortcode('onegodian_games', [self::class, 'render_game']);
        add_shortcode('onegodian_game', [self::class, 'render_game']);
        add_action('admin_menu', [self::class, 'register_admin_menu']);
    }

    /**
     * [onegodian_games] or [onegodian_game game="rise-of-civilization"]
     * The game runs in an isolated iframe, without WordPress authentication,
     * cookies, wallet operations, or persistent player data.
     */
    public static function render_game($attributes = []): string {
        $attributes = shortcode_atts(
            ['game' => self::GAME_ID, 'title' => 'OneGodian: Rise of Civilization'],
            (array) $attributes,
            'onegodian_game'
        );

        if (sanitize_key((string) $attributes['game']) !== self::GAME_ID) {
            return '<p class="onegodian-games-error">' .
                esc_html__('That OneGodian game is not available.', 'onegodian-games') .
                '</p>';
        }

        $title = sanitize_text_field((string) $attributes['title']);
        $src = plugins_url('games/rise-of-civilization/index.html', __FILE__);

        return '<section class="onegodian-games-embed" aria-label="' .
            esc_attr($title) .
            '" style="max-width:1100px;margin:1.5rem auto;color:#f5deb1">' .
            '<h2 style="margin:0 0 12px;font-size:clamp(1.25rem,3vw,2rem)">' .
            esc_html($title) .
            '</h2>' .
            '<iframe title="' . esc_attr($title) .
            '" src="' . esc_url($src) .
            '" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts" ' .
            'style="display:block;width:100%;height:650px;border:1px solid #b9954c;border-radius:14px;background:#090e1d" ' .
            'allowfullscreen></iframe>' .
            '<p style="font-size:.85rem;margin:8px 0">Arrow keys or WASD to move; W, Up or Space to jump twice. Touch controls are included.</p>' .
            '</section>';
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
            esc_html__('Available game: OneGodian: Rise of Civilization — three-level 2D platformer.', 'onegodian-games') .
            '</p><p>' .
            esc_html__('Embed on a WordPress page using either shortcode:', 'onegodian-games') .
            '</p><p><code>[onegodian_games]</code> or <code>[onegodian_game game="rise-of-civilization"]</code></p>' .
            '<p>' .
            esc_html__('Coins are game-only points. No digital currency, token, wallet, purchase, or account integration is enabled.', 'onegodian-games') .
            '</p></div>';
    }
}

OneGodian_Games::register();
