<?php
namespace Filmhanterare;

class Filmhanterare_Blocks {
    public function __construct() {
        add_action('init', [$this, 'register_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Register custom blocks for the film post type
     */
    public function register_blocks() {
        // Register Film Details Block
        register_block_type('filmhanterare/film-details', [
            'editor_script' => 'filmhanterare-blocks',
            'editor_style'  => 'filmhanterare-blocks-editor',
            'render_callback' => [$this, 'render_film_details']
        ]);

        // Register Showtimes Block
        register_block_type('filmhanterare/showtimes', [
            'editor_script' => 'filmhanterare-blocks',
            'editor_style'  => 'filmhanterare-blocks-editor',
            'render_callback' => [$this, 'render_showtimes']
        ]);

        // Register block pattern category
        register_block_pattern_category('filmhanterare', [
            'label' => __('Film Templates', 'filmhanterare')
        ]);

        // Register block pattern
        register_block_pattern('filmhanterare/film-template', [
            'title' => __('Film Layout', 'filmhanterare'),
            'categories' => ['filmhanterare'],
            'content' => $this->get_film_pattern_content()
        ]);
    }

    /**
     * Enqueue block editor assets
     */
    public function enqueue_editor_assets() {
        // Only enqueue on film post type
        if (get_post_type() !== 'film') {
            return;
        }

        wp_enqueue_script(
            'filmhanterare-blocks',
            FILMHANTERARE_PLUGIN_URL . 'assets/js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
            FILMHANTERARE_VERSION,
            true
        );

        wp_enqueue_style(
            'filmhanterare-blocks-editor',
            FILMHANTERARE_PLUGIN_URL . 'assets/css/blocks-editor.css',
            [],
            FILMHANTERARE_VERSION
        );
    }

    /**
     * Render the film details block
     */
    public function render_film_details($attributes) {
        if (!is_singular('film')) {
            return '';
        }

        $post_id = get_the_ID();
        $aldersgrans = get_post_meta($post_id, '_film_aldersgrans', true);
        $speltid = (int) get_post_meta($post_id, '_film_speltid', true);
        $genres = get_the_terms($post_id, 'film_genre');

        ob_start();
        ?>
        <div class="film-details">
            <?php if ($aldersgrans) : ?>
                <?php $age_label = Filmhanterare_PostType::get_age_rating_label($aldersgrans); ?>
                <?php if ($age_label) : ?>
                    <span class="film-certification"><?php echo esc_html($age_label); ?></span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($speltid > 0) : ?>
                <?php
                $timmar = floor($speltid / 60);
                $minuter = $speltid % 60;
                ?>
                <span class="film-runtime">
                    <?php printf('%dh %02dm', $timmar, $minuter); ?>
                </span>
            <?php endif; ?>

            <?php if ($genres && !is_wp_error($genres)) : ?>
                <div class="film-genres">
                    <?php foreach ($genres as $genre) : ?>
                        <a href="<?php echo esc_url(get_term_link($genre)); ?>" class="film-genre-tag">
                            <?php echo esc_html($genre->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render the showtimes block
     */
    public function render_showtimes($attributes) {
        if (!is_singular('film')) {
            return '';
        }

        $post_id = get_the_ID();
        $visningstider = get_post_meta($post_id, '_film_visningstider', true);

        if (empty($visningstider) || !is_array($visningstider)) {
            return '';
        }

        ob_start();
        ?>
        <div class="film-showtimes">
            <h2><?php esc_html_e('Showtimes', 'filmhanterare'); ?></h2>
            <div class="showtimes-grid">
                <?php
                usort($visningstider, function($a, $b) {
                    $date_a = strtotime($a['datum'] . ' ' . $a['tid']);
                    $date_b = strtotime($b['datum'] . ' ' . $b['tid']);
                    return $date_a - $date_b;
                });

                foreach ($visningstider as $visning) :
                    if (!empty($visning['datum']) && !empty($visning['tid'])) :
                        $timestamp = strtotime($visning['datum'] . ' ' . $visning['tid']);
                        if ($timestamp !== false) :
                        ?>
                        <div class="showtime-card">
                            <div class="showtime-date">
                                <time class="showtime-full" datetime="<?php echo esc_attr(date('Y-m-d\TH:i:s', $timestamp)); ?>">
                                    <div class="showtime-day"><?php echo esc_html(wp_date('D', $timestamp)); ?></div>
                                    <div class="showtime-number"><?php echo esc_html(wp_date('j', $timestamp)); ?></div>
                                    <div class="showtime-month"><?php echo esc_html(wp_date('M', $timestamp)); ?></div>
                                </time>
                            </div>
                            <div class="showtime-info">
                                <span class="showtime-time"><?php echo esc_html(wp_date('H:i', $timestamp)); ?></span>
                                <?php if (!empty($visning['språk'])) : ?>
                                    <span class="showtime-language">(<?php echo esc_html($visning['språk']); ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                        endif;
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get the content for the film block pattern
     */
    private function get_film_pattern_content() {
        return '<!-- wp:group {"className":"film-header"} -->
<div class="wp-block-group film-header">
    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column {"width":"33.33%"} -->
        <div class="wp-block-column" style="flex-basis:33.33%">
            <!-- wp:image {"className":"film-poster"} -->
            <figure class="wp-block-image film-poster">
                <img src="" alt=""/>
            </figure>
            <!-- /wp:image -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"66.66%"} -->
        <div class="wp-block-column" style="flex-basis:66.66%">
            <!-- wp:heading {"level":1,"className":"film-title"} -->
            <h1 class="film-title"></h1>
            <!-- /wp:heading -->

            <!-- wp:filmhanterare/film-details /-->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"film-content"} -->
<div class="wp-block-group film-content">
    <!-- wp:heading -->
    <h2>Synopsis</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p></p>
    <!-- /wp:paragraph -->

    <!-- wp:filmhanterare/showtimes /-->
</div>
<!-- /wp:group -->';
    }
}