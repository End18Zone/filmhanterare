<?php
class Filmhanterare_AdminColumns {
    public function __construct() {
        add_filter('manage_film_posts_columns', [$this, 'lägg_till_kolumner']);
        add_action('manage_film_posts_custom_column', [$this, 'rendera_kolumner'], 10, 2);
        add_filter('manage_edit-film_sortable_columns', [$this, 'gör_kolumner_sorterbara']);
        add_action('admin_head', [$this, 'anpassa_admin_css']);
    }
    
    public function lägg_till_kolumner($kolumner) {
        $nya_kolumner = [
            'cb' => $kolumner['cb'],
            'title' => $kolumner['title'],
            'film_genre' => 'Genrer',
            'utgivning' => 'Utgivning',
            'speltid' => 'Speltid',
            'aldersgrans' => 'Åldersgräns',
            'visningstider' => 'Visningstider',
            'date' => $kolumner['date'],
        ];
        return $nya_kolumner;
    }
    
    public function rendera_kolumner($kolumn, $post_id) {
        switch ($kolumn) {
            case 'film_genre':
                echo get_the_term_list($post_id, 'film_genre', '', ', ', '');
                break;
                
            case 'utgivning':
                $datum = get_post_meta($post_id, '_film_utgivningsdatum', true);
                $land = get_post_meta($post_id, '_film_utgivningsland', true);
                if ($datum) {
                    echo date_i18n('Y-m-d', strtotime($datum));
                    if ($land) echo '<br><small>' . esc_html($land) . '</small>';
                }
                break;
                
            case 'speltid':
                $timmar = (int) get_post_meta($post_id, '_film_speltid_timmar', true);
                $minuter = (int) get_post_meta($post_id, '_film_speltid_minuter', true);
                if ($timmar || $minuter) {
                    printf('%dh %02dm', $timmar, $minuter);
                }
                break;
                
            case 'aldersgrans':
                echo esc_html(get_post_meta($post_id, '_film_aldersgrans', true));
                break;
                
            case 'visningstider':
                $visningstider = get_post_meta($post_id, '_film_visningstider', true);
                if (!empty($visningstider)) {
                    $antal = count($visningstider);
                    echo $antal . ' visning' . ($antal !== 1 ? 'ar' : '');
                }
                break;
        }
    }
    
    public function gör_kolumner_sorterbara($kolumner) {
        $kolumner['utgivning'] = 'utgivning';
        $kolumner['speltid'] = 'speltid';
        return $kolumner;
    }
    
    public function anpassa_admin_css() {
        global $post_type;
        if ('film' === $post_type) {
            echo '<style>
                .column-film_genre, .column-utgivning, 
                .column-speltid, .column-aldersgrans {
                    width: 10%;
                }
                .column-visningstider {
                    width: 8%;
                }
                .column-title {
                    width: 20%;
                }
            </style>';
        }
    }
}