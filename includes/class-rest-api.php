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
            'speltid' => (int) get_post_meta($post_id, '_film_speltid', true),
            'aldersgrans' => get_post_meta($post_id, '_film_aldersgrans', true),
            'visningstider' => get_post_meta($post_id, '_film_visningstider', true),
            'beraknad_speltid' => $this->berakna_speltid(get_post_meta($post_id, '_film_speltid', true)),
        ];
    }
    
    private function berakna_speltid($total_minuter) {
        $total_minuter = (int) $total_minuter;
        return [
            'timmar' => floor($total_minuter / 60),
            'minuter' => $total_minuter % 60,
            'total_minuter' => $total_minuter,
        ];
    }
    
    public function uppdatera_film_info($värde, $objekt, $fältnamn) {
        $post_id = $objekt->ID;
        
        if (!is_array($värde)) return;
        
        $fält = [
            'synopsis' => 'wp_kses_post',
            'speltid' => 'absint',
            'aldersgrans' => 'sanitize_text_field',
        ];
        
        foreach ($fält as $fält => $saneringsfunktion) {
            if (isset($värde[$fält])) {
                $rensat_värde = call_user_func($saneringsfunktion, $värde[$fält]);
                // Extra validering för speltid
                if ($fält === 'speltid') {
                    $rensat_värde = max(1, min(1440, $rensat_värde)); // 1-1440 minuter (24 timmar)
                }
                update_post_meta($post_id, '_film_' . $fält, $rensat_värde);
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
                    'description' => 'Filmens handling',
                    'context' => ['view', 'edit'],
                ],
                'speltid' => [
                    'type' => 'integer',
                    'description' => 'Total speltid i minuter',
                    'context' => ['view', 'edit'],
                    'minimum' => 1,
                    'maximum' => 1440,
                ],
                'aldersgrans' => [
                    'type' => 'string',
                    'enum' => ['', 'B', '7', '11', '15'],
                    'description' => 'Åldersgräns för filmen',
                    'context' => ['view', 'edit'],
                ],
                'visningstider' => [
                    'type' => 'array',
                    'description' => 'Lista över visningstider',
                    'context' => ['view', 'edit'],
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'datum' => [
                                'type' => 'string',
                                'format' => 'date',
                                'context' => ['view', 'edit'],
                            ],
                            'tid' => [
                                'type' => 'string',
                                'pattern' => '^([01]?[0-9]|2[0-3]):[0-5][0-9]$',
                                'context' => ['view', 'edit'],
                            ],
                            'språk' => [
                                'type' => 'string',
                                'context' => ['view', 'edit'],
                            ],
                        ],
                    ],
                ],
                'beraknad_speltid' => [
                    'type' => 'object',
                    'description' => 'Beräknad speltid i timmar och minuter',
                    'context' => ['view'],
                    'readonly' => true,
                    'properties' => [
                        'timmar' => [
                            'type' => 'integer',
                        ],
                        'minuter' => [
                            'type' => 'integer',
                        ],
                        'total_minuter' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ];
    }
}