<?php
class Filmhanterare_RestAPI {
    public function __construct() {
        add_action('rest_api_init', [$this, 'registrera_rest_fält']);
    }
    
    public function registrera_rest_fält() {
        register_rest_field('film', 'film_info', [
            'get_callback' => [$this, 'hämta_film_info'],
            'update_callback' => [$this, 'uppdatera_film_info'],
            'schema' => $this->få_film_info_schema(),
        ]);
    }
    
    public function hämta_film_info($objekt) {
        $post_id = $objekt['id'];
        
        return [
            'synopsis' => get_post_meta($post_id, '_film_synopsis', true),
            'utgivningsdatum' => get_post_meta($post_id, '_film_utgivningsdatum', true),
            'utgivningsland' => get_post_meta($post_id, '_film_utgivningsland', true),
            'speltid_timmar' => (int) get_post_meta($post_id, '_film_speltid_timmar', true),
            'speltid_minuter' => (int) get_post_meta($post_id, '_film_speltid_minuter', true),
            'skadespelare' => get_post_meta($post_id, '_film_skadespelare', true),
            'aldersgrans' => get_post_meta($post_id, '_film_aldersgrans', true),
            'visningstider' => get_post_meta($post_id, '_film_visningstider', true),
        ];
    }
    
    public function uppdatera_film_info($värde, $objekt, $fältnamn) {
        $post_id = $objekt->ID;
        
        if (!is_array($värde)) return;
        
        $fält = [
            'synopsis' => 'wp_kses_post',
            'utgivningsdatum' => 'sanitize_text_field',
            'utgivningsland' => 'sanitize_text_field',
            'speltid_timmar' => 'absint',
            'speltid_minuter' => 'absint',
            'skadespelare' => 'sanitize_textarea_field',
            'aldersgrans' => 'sanitize_text_field',
        ];
        
        foreach ($fält as $fält => $saneringsfunktion) {
            if (isset($värde[$fält])) {
                update_post_meta($post_id, '_film_' . $fält, call_user_func($saneringsfunktion, $värde[$fält]));
            }
        }
        
        if (isset($värde['visningstider']) && is_array($värde['visningstider'])) {
            $visningstider = [];
            foreach ($värde['visningstider'] as $visning) {
                if (!empty($visning['datum']) && !empty($visning['tid'])) {
                    $visningstider[] = [
                        'datum' => sanitize_text_field($visning['datum']),
                        'tid' => sanitize_text_field($visning['tid']),
                        'språk' => sanitize_text_field($visning['språk'] ?? ''),
                    ];
                }
            }
            update_post_meta($post_id, '_film_visningstider', $visningstider);
        }
    }
    
    public function få_film_info_schema() {
        return [
            'type' => 'object',
            'properties' => [
                'synopsis' => [
                    'type' => 'string',
                    'description' => 'Filmens synopsis',
                ],
                'utgivningsdatum' => [
                    'type' => 'string',
                    'format' => 'date',
                ],
                'utgivningsland' => [
                    'type' => 'string',
                    'enum' => ['', 'SE', 'US', 'UK', 'DE', 'FR', 'JP', 'KR', 'IN'],
                ],
                'speltid_timmar' => [
                    'type' => 'integer',
                    'description' => 'Speltid i timmar',
                ],
                'speltid_minuter' => [
                    'type' => 'integer',
                    'description' => 'Speltid i minuter',
                ],
                'skadespelare' => [
                    'type' => 'string',
                    'description' => 'Skådespelare i filmen',
                ],
                'aldersgrans' => [
                    'type' => 'string',
                    'enum' => ['', 'B', '7', '11', '15', 'PG', 'R'],
                ],
                'visningstider' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'datum' => [
                                'type' => 'string',
                                'format' => 'date',
                            ],
                            'tid' => [
                                'type' => 'string',
                                'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
                            ],
                            'språk' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}