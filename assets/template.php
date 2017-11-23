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
	<ul class="list-items card-type-<?php echo $this->card_type; ?>">
		<?php
		while ( $this->query->have_posts() ) :
			$this->query->the_post(); ?>
			<li class="list-item">
				<a href="<?php echo get_the_permalink(); ?>">
					<?php if ( true === apply_filters( 'hogan/module/simple_post/show_image_column', 'small' !== $this->card_type, $this ) ) : ?>
						<div class="column">
							<?php if ( ! empty( get_the_post_thumbnail() ) ) {
								printf( '<p class="featured-image">%s</p>', get_the_post_thumbnail() );
							} ?>
						</div>
					<?php endif; ?>
					<div class="column">
						<h2 class="entry-title"><?php the_title_attribute(); ?></h2>
						<?php the_excerpt(); ?>
					</div>
				</a>
			</li>
			<?php
		endwhile; ?>
	</ul>
<?php endif;
