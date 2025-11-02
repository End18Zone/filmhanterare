<?php
namespace Filmhanterare;

class Filmhanterare_MetaBoxes {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'lägg_till_metaboxes']);
        add_action('save_post', [$this, 'spara_metaboxes'], 10, 2);
    }
    
    public static function ladda_skript($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php'])) return;
        
        global $post;
        if ('film' !== $post->post_type) return;
        
    // Date picker: use bundled jQuery UI theme (local)
    wp_register_style('filmhanterare-jquery-ui-style', FILMHANTERARE_PLUGIN_URL . 'assets/vendor/jquery-ui-smoothness.css', [], '1.12.1');
    wp_enqueue_style('filmhanterare-jquery-ui-style');
    // datepicker script is provided by WP core
    wp_enqueue_script('jquery-ui-datepicker');

    // Time picker: use bundled library under assets/vendor
    wp_register_style('filmhanterare-timepicker-style', FILMHANTERARE_PLUGIN_URL . 'assets/vendor/jquery-timepicker.min.css', [], '1.3.5');
    wp_register_script('filmhanterare-timepicker', FILMHANTERARE_PLUGIN_URL . 'assets/vendor/jquery-timepicker.min.js', ['jquery'], '1.3.5', true);
    wp_enqueue_style('filmhanterare-timepicker-style');
    wp_enqueue_script('filmhanterare-timepicker');

        // Plugin CSS
        wp_register_style('filmhanterare-admin', FILMHANTERARE_PLUGIN_URL . 'assets/css/admin.css', [], FILMHANTERARE_VERSION);
        wp_enqueue_style('filmhanterare-admin');

        // Plugin JS
        wp_register_script('filmhanterare-admin', FILMHANTERARE_PLUGIN_URL . 'assets/js/admin.js', ['jquery', 'jquery-ui-datepicker', 'filmhanterare-timepicker'], FILMHANTERARE_VERSION, true);
        wp_enqueue_script('filmhanterare-admin');

        // Localize script safely
        wp_localize_script('filmhanterare-admin', 'filmhanterare', [
            'add_showtime' => __('Add Showtime', 'filmhanterare'),
            'remove'      => __('Remove', 'filmhanterare'),
            'date'        => __('Date', 'filmhanterare'),
            'time'        => __('Time', 'filmhanterare'),
            'language'    => __('Language', 'filmhanterare')
        ]);
    }
    
    public function lägg_till_metaboxes() {
        add_meta_box(
            'filmhanterare_filminfo',
            __('Film Information', 'filmhanterare'),
            [$this, 'rendera_filminfo_metabox'],
            'film',
            'normal',
            'high'
        );
    }
    
    public function rendera_filminfo_metabox($post) {
        wp_nonce_field('filmhanterare_spara_filmdata', 'filmhanterare_film_nonce');
        
        // Get existing values
        $synopsis = get_post_meta($post->ID, '_film_synopsis', true);
        $total_minuter = (int) get_post_meta($post->ID, '_film_speltid', true);
        $aldersgrans = get_post_meta($post->ID, '_film_aldersgrans', true);
        $visningstider = get_post_meta($post->ID, '_film_visningstider', true);
        $visningstider = is_array($visningstider) ? $visningstider : [];
        
        // Calculate hours and minutes
        $timmar = floor($total_minuter / 60);
        $minuter = $total_minuter % 60;
        
        // Age limits
        $aldersgranser = [
            '' => __('Select age limit', 'filmhanterare'),
            'B' => __('Child Approved', 'filmhanterare'),
            '7' => __('From 7 years', 'filmhanterare'),
            '11' => __('From 11 years', 'filmhanterare'),
            '15' => __('From 15 years', 'filmhanterare')
        ];
        ?>
        
        <div class="filmhanterare-grid">
            <!-- Left Column -->
            <div class="filmhanterare-kolumn">
                <div class="filmhanterare-falt">
                    <label for="film_synopsis"><?php _e('Synopsis', 'filmhanterare'); ?></label>
                    <textarea id="film_synopsis" name="film_synopsis" rows="6"><?php echo esc_textarea($synopsis); ?></textarea>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="filmhanterare-kolumn">
                <div class="filmhanterare-falt">
                    <label for="film_speltid"><?php _e('Runtime (minutes)', 'filmhanterare'); ?></label>
                    <input type="number" id="film_speltid" name="film_speltid" 
                           value="<?php echo esc_attr($total_minuter); ?>" 
                           min="1" max="1440" placeholder="<?php esc_attr_e('Total minutes', 'filmhanterare'); ?>">
                    <p class="description">
                        <?php printf(__('Calculated runtime: %d hours and %d minutes', 'filmhanterare'), $timmar, $minuter); ?>
                    </p>
                </div>
                
                <div class="filmhanterare-falt">
                    <label for="film_aldersgrans"><?php _e('Age Limit', 'filmhanterare'); ?></label>
                    <select id="film_aldersgrans" name="film_aldersgrans">
                        <?php foreach ($aldersgranser as $värde => $text) : ?>
                            <option value="<?php echo esc_attr($värde); ?>" <?php selected($aldersgrans, $värde); ?>>
                                <?php echo esc_html($text); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filmhanterare-falt">
                    <label><?php _e('Showtimes', 'filmhanterare'); ?></label>
                    <div class="visningstider-container">
                        <?php foreach ($visningstider as $index => $visning) :
                            $datum = isset($visning['datum']) ? $visning['datum'] : '';
                            $tid = isset($visning['tid']) ? $visning['tid'] : '';
                            $sprak = isset($visning['språk']) ? $visning['språk'] : '';
                        ?>
                            <div class="visningstid-post">
                                <input type="text" name="film_visningstider[<?php echo $index; ?>][datum]" 
                                       value="<?php echo esc_attr($datum); ?>" 
                                       class="visning-datum film-datumväljare" 
                                       placeholder="<?php esc_attr_e('Date', 'filmhanterare'); ?>">
                                <input type="text" name="film_visningstider[<?php echo $index; ?>][tid]" 
                                       value="<?php echo esc_attr($tid); ?>" 
                                       class="visning-tid tidväljare" 
                                       placeholder="<?php esc_attr_e('HH:MM', 'filmhanterare'); ?>">
                                <input type="text" name="film_visningstider[<?php echo $index; ?>][språk]" 
                                       value="<?php echo esc_attr($sprak); ?>" 
                                       class="visning-språk" 
                                       placeholder="<?php esc_attr_e('Language', 'filmhanterare'); ?>">
                                <button type="button" class="button ta-bort-visning">
                                    <?php _e('Remove', 'filmhanterare'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button lägg-till-visning">
                        <?php _e('Add Showtime', 'filmhanterare'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function spara_metaboxes($post_id, $post) {
        if (!isset($_POST['filmhanterare_film_nonce']) || !wp_verify_nonce($_POST['filmhanterare_film_nonce'], 'filmhanterare_spara_filmdata')) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ('film' !== $post->post_type) return;
        
        // Fields to save
        $fält = [
            'film_synopsis' => 'wp_kses_post',
            'film_speltid' => 'absint',
            'film_aldersgrans' => 'sanitize_text_field',
        ];
        
        foreach ($fält as $fältnamn => $saneringsfunktion) {
            if (isset($_POST[$fältnamn])) {
                $värde = call_user_func($saneringsfunktion, $_POST[$fältnamn]);
                // Validate runtime
                if ($fältnamn === 'film_speltid') {
                    $värde = max(1, min(1440, $värde)); // Ensure 1-1440 minutes (24 hours)
                }
                update_post_meta($post_id, '_' . $fältnamn, $värde);
            }
        }
        
        // Handle showtimes
        $visningstider = [];
        if (!empty($_POST['film_visningstider'])) {
            foreach ($_POST['film_visningstider'] as $visning) {
                if (!empty($visning['datum']) && !empty($visning['tid'])) {
                    $visningstider[] = [
                        'datum' => sanitize_text_field($visning['datum']),
                        'tid' => sanitize_text_field($visning['tid']),
                        'språk' => sanitize_text_field($visning['språk'] ?? ''),
                    ];
                }
            }
        }
        
        update_post_meta($post_id, '_film_visningstider', $visningstider);
    }
}