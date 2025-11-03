<?php
/**
 * Film Template for GeneratePress
 */
if (!defined('ABSPATH')) exit;

// Get film metadata
$speltid = (int) get_post_meta(get_the_ID(), '_film_speltid', true);
$aldersgrans = get_post_meta(get_the_ID(), '_film_aldersgrans', true);
$synopsis = get_post_meta(get_the_ID(), '_film_synopsis', true);
$visningstider = get_post_meta(get_the_ID(), '_film_visningstider', true);
$genres = get_the_terms(get_the_ID(), 'film_genre');
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="https://schema.org/Movie">
    <div class="film-header-backdrop" style="background-image: url('<?php echo esc_url($featured_image); ?>');">
        <div class="film-header-overlay"></div>
        <div class="film-header-blurred" style="background-image: url('<?php echo esc_url($featured_image); ?>');"></div>
        
        <div class="gp-container">
            <div class="film-header-content grid-container">
                <div class="film-poster-col">
                    <div class="film-poster-wrapper">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', [
                                'class' => 'film-poster',
                                'alt' => get_the_title(),
                                'loading' => 'lazy'
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="film-info-col">
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        
                        <div class="film-meta">
                            <?php if ($aldersgrans) : ?>
                                <?php $age_label = Filmhanterare\Filmhanterare_PostType::get_age_rating_label($aldersgrans); ?>
                                <?php if ($age_label) : ?>
                                    <span class="film-certification"><?php echo esc_html($age_label); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($genres && !is_wp_error($genres)) : ?>
                                <span class="film-genres">
                                    <span class="screen-reader-text"><?php _e('Film Genres:', 'filmhanterare'); ?></span>
                                    <?php foreach ($genres as $genre) : ?>
                                        <a href="<?php echo esc_url(get_term_link($genre)); ?>"><?php echo esc_html($genre->name); ?></a>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($speltid > 0) : ?>
                                <?php
                                $timmar = floor($speltid / 60);
                                $minuter = $speltid % 60;
                                ?>
                                <span class="film-runtime" title="<?php printf(_n('%d minute', '%d minutes', $speltid, 'filmhanterare'), $speltid); ?>">
                                    <?php printf('%dh %02dm', $timmar, $minuter); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>
                    
                    <div class="film-synopsis" itemprop="description">
                        <h3><?php esc_html_e('Synopsis', 'generatepress'); ?></h3>
                        <div class="entry-content">
                            <?php echo $synopsis ? wpautop(wp_kses_post($synopsis)) : ''; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($visningstider) && is_array($visningstider)) : ?>
                        <?php do_action('filmhanterare_before_showtimes', get_the_ID(), $visningstider); ?>
                        <div class="film-showtimes-section">
                            <h3><?php esc_html_e('Showtimes', 'filmhanterare'); ?></h3>
                            <div class="showtimes-grid">
                                <?php 
                                // Sort showtimes by date
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
                                        <div class="showtime-card" itemscope itemtype="https://schema.org/ScreeningEvent">
                                            <div class="showtime-date">
                                                <time class="showtime-full" datetime="<?php echo esc_attr(date('Y-m-d\TH:i:s', $timestamp)); ?>" itemprop="startDate">
                                                    <div class="showtime-day"><?php echo esc_html(wp_date('D', $timestamp)); ?></div>
                                                    <div class="showtime-number"><?php echo esc_html(wp_date('j', $timestamp)); ?></div>
                                                    <div class="showtime-month"><?php echo esc_html(wp_date('M', $timestamp)); ?></div>
                                                </time>
                                            </div>
                                            <div class="showtime-info">
                                                <span class="showtime-time" itemprop="startDate"><?php echo esc_html(wp_date('H:i', $timestamp)); ?></span>
                                                <?php if (!empty($visning['språk'])) : ?>
                                                    <span class="showtime-language" itemprop="description">(<?php echo esc_html($visning['språk']); ?>)</span>
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
                        <?php do_action('filmhanterare_after_showtimes', get_the_ID(), $visningstider); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
</article>