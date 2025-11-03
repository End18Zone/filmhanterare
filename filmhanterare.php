<?php
/**
 * Plugin Name: Filmhanterare
 * Description: Ett avancerat system för att hantera filmer som anpassad posttyp
 * Version: 2.0.0
 * Author: Ditt Namn
 * Text Domain: filmhanterare
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

// Define constants (only if not defined already)
if (! defined('FILMHANTERARE_VERSION')) {
    define('FILMHANTERARE_VERSION', '2.0.0');
}

if (! defined('FILMHANTERARE_PLUGIN_DIR')) {
    define('FILMHANTERARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (! defined('FILMHANTERARE_PLUGIN_URL')) {
    define('FILMHANTERARE_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Load translations
add_action('plugins_loaded', 'filmhanterare_load_textdomain');
function filmhanterare_load_textdomain() {
    load_plugin_textdomain(
        'filmhanterare',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}

// Attempt to use composer autoload if available, otherwise require the files directly
if (file_exists(FILMHANTERARE_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once FILMHANTERARE_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    $required_files = [
        'includes/class-post-type.php',
        'includes/class-metaboxes.php',
        'includes/class-admin-columns.php',
        'includes/class-rest-api.php'
    ];

    foreach ($required_files as $file) {
        require_once FILMHANTERARE_PLUGIN_DIR . $file;
    }
}

// Initialize plugin
add_action('plugins_loaded', 'filmhanterare_init');
function filmhanterare_init() {
    new \Filmhanterare\Filmhanterare_PostType();
    new \Filmhanterare\Filmhanterare_MetaBoxes();
    new \Filmhanterare\Filmhanterare_AdminColumns();
    new \Filmhanterare\Filmhanterare_RestAPI();
    
    // Load scripts and styles
    add_action('admin_enqueue_scripts', [\Filmhanterare\Filmhanterare_MetaBoxes::class, 'ladda_skript']);
    add_action('wp_enqueue_scripts', 'filmhanterare_ladda_frontend_skript');
}

function filmhanterare_ladda_frontend_skript() {
    if (is_singular('film')) {
        wp_enqueue_style(
            'filmhanterare-frontend',
            FILMHANTERARE_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            FILMHANTERARE_VERSION
        );
    }
}

/**
 * Activation hook: register post type/taxonomy (by triggering init) and flush rewrite rules.
 */
function filmhanterare_activate() {
    // Ensure classes are loaded
    if (file_exists(FILMHANTERARE_PLUGIN_DIR . 'includes/class-post-type.php')) {
        require_once FILMHANTERARE_PLUGIN_DIR . 'includes/class-post-type.php';
    }

    if (class_exists('Filmhanterare_PostType')) {
        $pt = new Filmhanterare_PostType();
        // Call registration methods to ensure rewrite rules exist
        $pt->registrera_posttyp();
        if (method_exists($pt, 'registrera_taxonomi')) {
            $pt->registrera_taxonomi();
        }
        flush_rewrite_rules();
    }
}
register_activation_hook(__FILE__, 'filmhanterare_activate');

/**
 * Deactivation hook: flush rewrite rules.
 */
function filmhanterare_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'filmhanterare_deactivate');