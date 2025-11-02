<?php
/**
 * Film Archive Template
 *
 * @package GeneratePress
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div <?php generate_do_attr('content'); ?>>
    <main <?php generate_do_attr('main'); ?>>
        <?php
        do_action('generate_before_main_content');

        if (generate_has_default_loop()) {
            if (have_posts()) :
                
                // Custom archive header
                echo '<header class="film-archive-header">';
                echo '<h1 class="film-archive-title">' . get_the_archive_title() . '</h1>';
                
                // Optional: Add film archive description
                if ($description = get_the_archive_description()) {
                    echo '<div class="film-archive-description">' . $description . '</div>';
                }
                
                // Optional: Add film filter controls
                echo '<div class="film-archive-filters">';
                echo '<div class="film-filter-group">';
                echo '<label for="film-genre-filter">' . __('Filter by Genre', 'generatepress') . '</label>';
                wp_dropdown_categories([
                    'taxonomy' => 'film_genre',
                    'show_option_all' => __('All Genres', 'generatepress'),
                    'id' => 'film-genre-filter',
                    'class' => 'film-filter-select'
                ]);
                echo '</div>';
                echo '</div>';
                echo '</header>';

                do_action('generate_before_loop', 'archive');
                
                echo '<div class="film-archive-grid">';
                while (have_posts()) : the_post();
                    ?>
                    <article <?php post_class('film-archive-item'); ?>>
                        <div class="film-archive-poster">
                            <a href="<?php the_permalink(); ?>">
                                <?php 
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium', [
                                        'class' => 'film-archive-thumbnail',
                                        'alt' => get_the_title()
                                    ]);
                                } else {
                                    echo '<div class="film-archive-placeholder"></div>';
                                }
                                ?>
                            </a>
                        </div>
                        <div class="film-archive-details">
                            <h2 class="film-archive-item-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="film-archive-meta">
                                <?php
                                $speltid = (int) get_post_meta(get_the_ID(), '_film_speltid', true);
                                if ($speltid > 0) {
                                    $timmar = floor($speltid / 60);
                                    $minuter = $speltid % 60;
                                    printf(
                                        '<span class="film-runtime" title="%s">%dh %02dm</span>',
                                        sprintf(
                                            _n('%d minute', '%d minutes', $speltid, 'filmhanterare'),
                                            $speltid
                                        ),
                                        $timmar,
                                        $minuter
                                    );
                                }
                                
                                $aldersgrans = get_post_meta(get_the_ID(), '_film_aldersgrans', true);
                                if ($aldersgrans) {
                                    $labels = [
                                        'B' => __('Child Approved', 'filmhanterare'),
                                        '7' => __('From 7 years', 'filmhanterare'),
                                        '11' => __('From 11 years', 'filmhanterare'),
                                        '15' => __('From 15 years', 'filmhanterare')
                                    ];
                                    if (isset($labels[$aldersgrans])) {
                                        printf(
                                            '<span class="film-age-limit">%s</span>',
                                            esc_html($labels[$aldersgrans])
                                        );
                                    }
                                }
                                ?>
                            </div>
                            <div class="film-archive-genres">
                                <?php
                                $genres = get_the_terms(get_the_ID(), 'film_genre');
                                if ($genres && !is_wp_error($genres)) {
                                    foreach ($genres as $genre) {
                                        echo '<a href="' . esc_url(get_term_link($genre)) . '" class="film-genre-tag">' . esc_html($genre->name) . '</a>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                echo '</div>';

                do_action('generate_after_loop', 'archive');

            else :
                generate_do_template_part('none');
            endif;
        }

        do_action('generate_after_main_content');
        ?>
    </main>
</div>

<?php
do_action('generate_after_primary_content_area');
generate_construct_sidebars();
get_footer();