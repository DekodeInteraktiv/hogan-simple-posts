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

foreach ( $this->list as $item ) : ?>
	<li class="list-item">
		<a href="<?php echo $item->url; ?>">

			<?php if ( 'no_image' !== $this->card_look ) : ?>
				<div class="column">
					<?php if ( ! empty( $item->featured_image ) ) {
						printf( '<p class="featured-image">%s</p>', $item->featured_image );
					}
					?>
				</div>
			<?php endif; ?>
			<div class="column">
				<?php
				if ( ! empty( $item->title ) ) {
					printf( '<h2>%s</h2>', esc_html( $item->title ) );
				}
				?>
				<?php
				if ( ! empty( $item->title ) ) {
					printf( '<p class="entry-summary">%s</p>', wp_kses_post( $item->excerpt ) );
				}
				?>
			</div>
		</a>
	</li>
<?php endforeach;
