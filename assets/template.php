<?php
/**
 * Simple Posts template
 *
 * $this is an instace of the Simple_Posts object. Ex. use: $this->content to output content value.
 *
 * @package Hogan
 */

declare( strict_types = 1 );
namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) || ! ( $this instanceof Simple_Posts ) ) {
	return; // Exit if accessed directly.
}

if ( $this->query->have_posts() ) : ?>
	<ul class="list-items card-type-<?php echo esc_attr( $this->card_type ); ?>">
		<?php
		while ( $this->query->have_posts() ) :
			$this->query->the_post();
			?>
			<li class="list-item">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php if ( true === apply_filters( 'hogan/module/simple_post/show_image_column', 'small' !== $this->card_type, $this ) ) : ?>
						<div class="column">
							<div class="featured-image"><?php echo get_the_post_thumbnail( null, apply_filters( 'hogan/module/simple_posts/image_size', 'post-thumbnail', $this ) ); ?></div>
						</div>
					<?php endif; ?>
					<div class="column">
						<h3 class="entry-title"><?php the_title_attribute(); ?></h3>
						<?php the_excerpt(); ?>
					</div>
				</a>
			</li>
			<?php
		endwhile;
		?>
	</ul>
<?php
endif;
