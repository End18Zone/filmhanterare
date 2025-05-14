<?php
class Filmhanterare_PostType {
    public function __construct() {
        add_action('init', [$this, 'registrera_posttyp']);
        add_filter('use_block_editor_for_post_type', [$this, 'inaktivera_gutenberg'], 10, 2);
    }
    
    public function registrera_posttyp() {
        $etiketter = [
            'name'                  => 'Filmer',
            'singular_name'         => 'Film',
            'menu_name'             => 'Filmer',
            'name_admin_bar'        => 'Film',
            'add_new'               => 'Lägg till ny',
            'add_new_item'          => 'Lägg till ny film',
            'new_item'              => 'Ny film',
            'edit_item'             => 'Redigera film',
            'view_item'             => 'Visa film',
            'all_items'             => 'Alla filmer',
            'search_items'          => 'Sök filmer',
            'not_found'             => 'Inga filmer hittades',
            'not_found_in_trash'    => 'Inga filmer hittades i papperskorgen'
        ];
        
        $args = [
            'labels'             => $etiketter,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'film'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-video-alt3',
            'supports'           => ['title', 'thumbnail'],
            'show_in_rest'       => true,
        ];
        
        register_post_type('film', $args);
    }
    
    public function inaktivera_gutenberg($anvand_blockeditor, $posttyp) {
        return 'film' === $posttyp ? false : $anvand_blockeditor;
    }
}