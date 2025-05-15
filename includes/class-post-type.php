<?php
class Filmhanterare_PostType {
    public function __construct() {
        add_action('init', [$this, 'registrera_posttyp']);
    }
    
    public function registrera_posttyp() {
        $labels = [
            'name'                  => _x('Films', 'Post Type General Name', 'filmhanterare'),
            'singular_name'         => _x('Film', 'Post Type Singular Name', 'filmhanterare'),
            'menu_name'             => __('Films', 'filmhanterare'),
            'name_admin_bar'        => __('Film', 'filmhanterare'),
            'archives'              => __('Film Archives', 'filmhanterare'),
            'attributes'            => __('Film Attributes', 'filmhanterare'),
            'parent_item_colon'    => __('Parent Film:', 'filmhanterare'),
            'all_items'             => __('All Films', 'filmhanterare'),
            'add_new_item'         => __('Add New Film', 'filmhanterare'),
            'add_new'              => __('Add New', 'filmhanterare'),
            'new_item'             => __('New Film', 'filmhanterare'),
            'edit_item'            => __('Edit Film', 'filmhanterare'),
            'update_item'          => __('Update Film', 'filmhanterare'),
            'view_item'            => __('View Film', 'filmhanterare'),
            'view_items'           => __('View Films', 'filmhanterare'),
            'search_items'         => __('Search Films', 'filmhanterare'),
            'not_found'           => __('Not found', 'filmhanterare'),
            'not_found_in_trash'   => __('Not found in Trash', 'filmhanterare'),
            'featured_image'       => __('Featured Image', 'filmhanterare'),
            'set_featured_image'   => __('Set featured image', 'filmhanterare'),
            'remove_featured_image' => __('Remove featured image', 'filmhanterare'),
            'use_featured_image'   => __('Use as featured image', 'filmhanterare'),
            'insert_into_item'     => __('Insert into film', 'filmhanterare'),
            'uploaded_to_this_item' => __('Uploaded to this film', 'filmhanterare'),
            'items_list'           => __('Films list', 'filmhanterare'),
            'items_list_navigation' => __('Films list navigation', 'filmhanterare'),
            'filter_items_list'    => __('Filter films list', 'filmhanterare'),
        ];
        
        $args = [
            'label'               => __('Film', 'filmhanterare'),
            'description'         => __('Film database', 'filmhanterare'),
            'labels'              => $labels,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'taxonomies'         => ['film_genre'],
            'hierarchical'       => false,
            'public'             => true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'menu_position'     => 5,
            'menu_icon'         => 'dashicons-video-alt3',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export'        => true,
            'has_archive'      => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type'   => 'post',
            'show_in_rest'     => true,
        ];
        
        register_post_type('film', $args);
    }
}