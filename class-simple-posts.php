<?php
/**
 * Simple Posts module class
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\\Dekode\\Hogan\\Simple_Posts' ) && class_exists( '\\Dekode\\Hogan\\Module' ) ) {

	/**
	 * Simple Posts module class (WYSIWYG).
	 *
	 * @extends Modules base class.
	 */
	class Simple_Posts extends Module {

		/**
		 * List type (Automatic or Manual).
		 *
		 * @var $list_type
		 */
		public $list_type;

		/**
		 * List of item objects
		 *
		 * @var $list
		 */
		public $list;

		/**
		 * Card look (Automatic or Manual).
		 *
		 * @var $card_look
		 */
		public $card_look;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label    = __( 'Simple Posts', 'hogan-simple-posts' );
			$this->template = __DIR__ . '/assets/template.php';

			add_filter( 'hogan/module/simple_posts/inner_wrapper_tag', function () {
				return 'ul';
			} );

			parent::__construct();
		}

		/**
		 * Field definitions for module.
		 */
		public function get_fields() {

			return [
				[
					'type'          => 'button_group',
					'key'           => $this->field_key . '_list_type',
					'label'         => __( 'List Type', 'hogan-simple-posts' ),
					'name'          => 'list_type',
					'instructions'  => __( 'Choose between automatic or manually populated list', 'hogan-simple-posts' ),
					'choices'       => [
						'automatic' => __( 'Automatic', 'hogan-simple-posts' ),
						'manual'    => __( 'Manual', 'hogan-simple-posts' ),
					],
					'allow_null'    => 0,
					'default_value' => 'automatic',
					'layout'        => 'horizontal',
					'return_format' => 'value',
					'wrapper'       => [
						'width' => '50',
					],
				],
				[
					'type'          => 'button_group',
					'key'           => $this->field_key . '_card_look',
					'label'         => __( 'Card Look', 'hogan-simple-posts' ),
					'name'          => 'card_look',
					'instructions'  => __( 'Choose card look', 'hogan-simple-posts' ),
					'choices'       => [
						'standard' => __( 'Standard', 'hogan-simple-posts' ),
						'slim'     => __( 'Slim', 'hogan-simple-posts' ),
						'no_image' => __( 'No Image', 'hogan-simple-posts' ),
					],
					'allow_null'    => 0,
					'default_value' => 'automatic',
					'layout'        => 'horizontal',
					'return_format' => 'value',
					'wrapper'       => [
						'width' => '50',
					],
				],
				[
					'type'              => 'relationship',
					'key'               => $this->field_key . '_manual_list',
					'label'             => __( 'Manual List', 'hogan-simple-posts' ),
					'name'              => 'manual_list',
					'value'             => null,
					'instructions'      => __( 'Add items to the list by clicking the items on the left side', 'hogan-simple-posts' ),
					'required'          => 1,
					'conditional_logic' => [
						[
							[
								'field'    => $this->field_key . '_list_type',
								'operator' => '==',
								'value'    => 'manual',
							],
						],
					],
					'post_type'         => [
						0 => 'post',
						1 => 'page',
					],
					'taxonomy'          => [],
					'filters'           => [
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					],
					'elements'          => [
						0 => 'featured_image',
					],
					'min'               => 1,
					'max'               => 10,
					'return_format'     => 'id',
				],
				[
					'type'              => 'taxonomy',
					'key'               => $this->field_key . '_automatic_list',
					'label'             => __( 'Automatic List', 'hogan-simple-posts' ),
					'name'              => 'automatic_list',
					'instructions'      => __( 'Choose category', 'hogan-simple-posts' ),
					'required'          => 1,
					'conditional_logic' => [
						[
							[
								'field'    => $this->field_key . '_list_type',
								'operator' => '==',
								'value'    => 'automatic',
							],
						],
					],
					'taxonomy'          => 'category',
					'field_type'        => 'select',
					'return_format'     => 'id',
					'wrapper'           => [
						'width' => '50',
					],
				],
				[
					'type'              => 'number',
					'key'               => $this->field_key . '_number_of_items',
					'label'             => __( 'Number of items', 'hogan-simple-posts' ),
					'name'              => 'number_of_items',
					'instructions'      => __( 'Choose the number of items for the list', 'hogan-simple-posts' ),
					'required'          => 1,
					'conditional_logic' => [
						[
							[
								'field'    => $this->field_key . '_list_type',
								'operator' => '==',
								'value'    => 'automatic',
							],
						],
					],
					'default_value'     => 5,
					'min'               => 1,
					'max'               => 10,
					'step'              => 1,
					'wrapper'           => [
						'width' => '50',
					],
				],
			];
		}

		/**
		 * Map fields to object variable.
		 *
		 * @param array $content The content value.
		 */
		public function load_args_from_layout_content( $content ) {

			$this->list_type = $content['list_type'];
			$this->card_look = $content['card_look'];

			if ( 'manual' === $this->list_type ) :
				$this->list = $this->populate_manual_list( $content['manual_list'] );
			elseif ( 'automatic' === $this->list_type ) :
				$this->list = $this->populate_automatic_list( $content['automatic_list'], $content['number_of_items'] );
			endif;

			add_filter( 'hogan/module/simple_posts/inner_wrapper_classes', function ( $classes ) {
				$classes[] = 'list-items';
				$classes[] = 'card-look-' . $this->card_look;

				return $classes;
			} );


			parent::load_args_from_layout_content( $content );
		}

		/**
		 * Validate module content before template is loaded.
		 */
		public function validate_args() {
			return ! empty( $this->list );
		}

		/**
		 * Create list of post objects from post ids picked in manual list.
		 *
		 * @param array $category Category to fetch posts from
		 * @param int $number_of_posts Number of posts to fetch
		 *
		 * @return array List of post items for the template
		 */
		protected function populate_automatic_list( $category, $number_of_posts ) {


			/*$args = array(
				'post_type' => 'post',
				'posts_per_page' => $number_of_posts,
				'tax_query' => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $category,
					),
				),
			);*/

			$args = [
				'post_type' => [ 'post', 'page' ],
				'orderby' => 'post__in',
				'post__in'  => [ 126, 28, 1 ],
			];

			$query = new \WP_Query( $args );

			$list = [];

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					// display content
					$post_id = get_the_id();
					$excerpt = empty( $query->post_excerpt ) ? wp_trim_words( get_the_content(), 20 ) : $query->post_excerpt;
					$item    = [
						'title'          => get_the_title( $post_id ),
						'excerpt'        => $excerpt,
						'featured_image' => get_the_post_thumbnail( $post_id ),
						'url'            => get_the_permalink( $post_id ),
					];

					$list[] = (object) $item;
				}
			}

			wp_reset_postdata();

			return $list;
		}

		/**
		 * Create list of post objects from post ids picked in manual list.
		 *
		 * @param array $post_ids List of ids.
		 *
		 * @return array List of post items for the template
		 */
		protected function populate_manual_list( $post_ids ) {
			$list = [];

			foreach ( $post_ids as $post_id ) {
				$post    = get_post( $post_id );
				$excerpt = empty( $post->post_excerpt ) ? wp_trim_words( $post->post_content, 20 ) : $post->post_excerpt;
				$item    = [
					'title'          => get_the_title( $post_id ),
					'excerpt'        => $excerpt,
					'featured_image' => get_the_post_thumbnail( $post_id ),
					'url'            => get_the_permalink( $post_id ),
				];

				$list[] = (object) $item;
			}

			return $list;
		}

	}
} // End if().

