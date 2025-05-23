<?php
class Filmhanterare_AdminColumns {
    public function __construct() {
        add_filter('manage_film_posts_columns', [$this, 'lägg_till_kolumner']);
        add_action('manage_film_posts_custom_column', [$this, 'rendera_kolumner'], 10, 2);
        add_filter('manage_edit-film_sortable_columns', [$this, 'gör_kolumner_sorterbara']);
        add_action('pre_get_posts', [$this, 'hantera_sortering']);
        add_action('admin_head', [$this, 'anpassa_admin_css']);
    }
    
    public function lägg_till_kolumner($kolumner) {
        $nya_kolumner = [
            'cb' => $kolumner['cb'],
            'title' => $kolumner['title'],
            'film_genre' => __('Genres', 'filmhanterare'),
            'speltid' => __('Runtime', 'filmhanterare'),
            'aldersgrans' => __('Age Limit', 'filmhanterare'),
            'visningstider' => __('Showtimes', 'filmhanterare'),
            'date' => $kolumner['date'],
        ];
        return $nya_kolumner;
    }
    
    public function rendera_kolumner($kolumn, $post_id) {
        switch ($kolumn) {
            case 'film_genre':
                $genres = get_the_term_list($post_id, 'film_genre', '', ', ', '');
                echo $genres ?: '<span aria-hidden="true">—</span>';
                break;
                
            case 'speltid':
                $total_minuter = (int) get_post_meta($post_id, '_film_speltid', true);
                if ($total_minuter > 0) {
                    $timmar = floor($total_minuter / 60);
                    $minuter = $total_minuter % 60;
                    printf(
                        '<span title="%s">%dh %02dm</span>',
                        sprintf(_n('%d minute', '%d minutes', $total_minuter, 'filmhanterare'), $total_minuter),
                        $timmar,
                        $minuter
                    );
                } else {
                    echo '<span aria-hidden="true">—</span>';
                }
                break;
                
            case 'aldersgrans':
                $aldersgrans = get_post_meta($post_id, '_film_aldersgrans', true);
                $labels = [
                    'B' => __('Child Approved', 'filmhanterare'),
                    '7' => __('7 years', 'filmhanterare'),
                    '11' => __('11 years', 'filmhanterare'),
                    '15' => __('15 years', 'filmhanterare')
                ];
                echo isset($labels[$aldersgrans]) ? esc_html($labels[$aldersgrans]) : '<span aria-hidden="true">—</span>';
                break;
                
            case 'visningstider':
                $visningstider = get_post_meta($post_id, '_film_visningstider', true);
                if (!empty($visningstider) && is_array($visningstider)) {
                    $antal = count($visningstider);
                    printf(
                        '<a href="%s" title="%s">%s</a>',
                        esc_url(get_edit_post_link($post_id)),
                        esc_attr__('Edit showtimes', 'filmhanterare'),
                        sprintf(
                            _n('%d showtime', '%d showtimes', $antal, 'filmhanterare'),
                            $antal
                        )
                    );
                } else {
                    echo '<span aria-hidden="true">—</span>';
                }
                break;
        }
    }
    
    public function gör_kolumner_sorterbara($kolumner) {
        $kolumner['speltid'] = 'speltid';
        $kolumner['aldersgrans'] = 'aldersgrans';
        return $kolumner;
    }
    
    public function hantera_sortering($query) {
        if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'film') {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        switch ($orderby) {
            case 'speltid':
                $query->set('meta_key', '_film_speltid');
                $query->set('orderby', 'meta_value_num');
                break;
                
            case 'aldersgrans':
                $query->set('meta_key', '_film_aldersgrans');
                $query->set('orderby', 'meta_value');
                break;
        }
    }
    
    public function anpassa_admin_css() {
        echo '<style>
            .post-type-film .wp-list-table {
                table-layout: auto;
            }
            .column-film_genre {
                width: 15%;
            }
            .column-speltid, 
            .column-aldersgrans {
                width: 10%;
            }
            .column-visningstider {
                width: 12%;
                text-align: center;
            }
            .column-title {
                width: 25%;
            }
            .column-visningstider a {
                text-decoration: none;
            }
            .column-visningstider a:hover {
                text-decoration: underline;
            }
        </style>';
    }
}