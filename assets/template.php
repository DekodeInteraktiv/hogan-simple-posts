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
write_log('template');
if ( $this->query->have_posts() ) : ?>
	<ul class="list-items card-type-<?php echo esc_attr( $this->card_type ); ?>">
		<?php
		while ( $this->query->have_posts() ) :
			$this->query->the_post();
			?>
			<li class="list-item">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php if ( true === apply_filters( 'hogan/module/simple_posts/show_image_column', 'small' !== $this->card_type, $this ) ) : ?>
						<div class="column">
							<div class="featured-image">
								<?php
								$size    = apply_filters( 'hogan/module/simple_posts/image_size', 'post-thumbnail', $this );
								$post_id = get_the_ID();
								echo apply_filters( 'hogan/module/simple_posts/featured_image', get_the_post_thumbnail( $post_id, $size ), get_post_thumbnail_id( $post_id ), $post_id, $this );
								?>
							</div>
						</div>
					<?php endif; ?>
					<div class="column">
						<?php printf( '<%1$s class="entry-title">%2$s</%1$s>', apply_filters( 'hogan/module/simple_posts/card_heading', 'h2' ), the_title_attribute( [ 'echo' => false ] ) ); ?>
						<?php the_excerpt(); ?>
						<?php do_action( 'hogan/module/simple_posts/after_the_excerpt' ); ?>
					</div>
				</a>
			</li>
		<?php
		endwhile;
		?>
	</ul>
<?php
endif;
