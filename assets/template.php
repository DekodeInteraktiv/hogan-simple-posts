<?php
/**
 * Template for text module
 *
 * $this is an instace of the Text object. Ex. use: $this->content to output content value.
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) || ! ( $this instanceof Simple_Posts ) ) {
	return; // Exit if accessed directly.
}

if ( $this->query->have_posts() ) : ?>
	<ul class="list-items card-look-<?php echo $this->card_look; ?>">
		<?php
		while ( $this->query->have_posts() ) :
			$this->query->the_post(); ?>
			<li class="list-item">
				<a href="<?php echo get_the_permalink(); ?>">
					<?php if ( 'no_image' !== $this->card_look ) : ?>
						<div class="column">
							<?php if ( ! empty( get_the_post_thumbnail() ) ) {
								printf( '<p class="featured-image">%s</p>', get_the_post_thumbnail() );
							}
							?>
						</div>
					<?php endif; ?>
					<div class="column">
						<?php
						if ( ! empty( get_the_title() ) ) {
							printf( '<h2>%s</h2>', esc_html( get_the_title() ) );
						}
						?>
						<?php the_excerpt(); ?>
					</div>
				</a>
			</li>
			<?php
		endwhile; ?>
	</ul>
<?php endif;
