<?php
/**
 * Plugin Name: Filmhanterare
 * Beskrivning: Ett avancerat system för att hantera filmer som anpassad posttyp
 * Version: 2.0.0
 * Författare: Ditt Namn
 */

defined('ABSPATH') || exit;

// Definiera konstanter
define('FILMHANTERARE_VERSION', '2.0.0');
define('FILMHANTERARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FILMHANTERARE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Inkludera nödvändiga filer
$required_files = [
    'includes/class-post-type.php',
    'includes/class-metaboxes.php',
    'includes/class-admin-columns.php',
    'includes/class-rest-api.php'
];

foreach ($required_files as $file) {
    require_once FILMHANTERARE_PLUGIN_DIR . $file;
}

// Initiera plugin
add_action('plugins_loaded', 'filmhanterare_init');

function filmhanterare_init() {
    new Filmhanterare_PostType();
    new Filmhanterare_MetaBoxes();
    new Filmhanterare_AdminColumns();
    new Filmhanterare_RestAPI();
    
    // Ladda skript och stilar
    add_action('admin_enqueue_scripts', ['Filmhanterare_MetaBoxes', 'ladda_skript']);
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