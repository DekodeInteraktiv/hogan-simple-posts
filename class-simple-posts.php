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
		 * List of post objects.
		 *
		 * @var $query
		 */
		public $query;

		/**
		 * Card Type (Small, Medium, Large)
		 *
		 * @var $card_type
		 */
		public $card_type;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label    = __( 'Simple Posts', 'hogan-simple-posts' );
			$this->template = __DIR__ . '/assets/template.php';

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
					'key'           => $this->field_key . '_card_type',
					'label'         => __( 'Card Type', 'hogan-simple-posts' ),
					'name'          => 'card_type',
					'instructions'  => __( 'Choose card type', 'hogan-simple-posts' ),
					'choices'       => [
						'small'  => __( 'Small', 'hogan-simple-posts' ),
						'medium' => __( 'Medium', 'hogan-simple-posts' ),
						'large'  => __( 'Large', 'hogan-simple-posts' ),
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
			$this->card_type = $content['card_type'];

			if ( 'manual' === $this->list_type ) :
				$this->query = $this->populate_manual_list( $content['manual_list'] );
			elseif ( 'automatic' === $this->list_type ) :
				$this->query = $this->populate_automatic_list( $content['automatic_list'], $content['number_of_items'] );
			endif;

			add_filter( 'the_excerpt', [ $this, 'on_the_excerpt' ] ); // add filter for custom excerpt.

			parent::load_args_from_layout_content( $content );
		}

		/**
		 * Filter hook for custom excerpt.
		 *
		 * @param string $excerpt .
		 *
		 * @return string
		 */
		public function on_the_excerpt( $excerpt ) {
			return apply_filters( 'hogan/module/simple_post/the_excerpt', $excerpt, $this );
		}

		/**
		 * Validate module content before template is loaded.
		 */
		public function validate_args() {
			return ! empty( $this->query->have_posts() );
		}

		/**
		 * Use super class method to reset post data.
		 */
		protected function render_closing_template_wrappers() {
			parent::render_closing_template_wrappers();
			remove_filter( 'the_excerpt', [ $this, 'on_the_excerpt' ] ); // remove filter for custom excerpt.
			\wp_reset_postdata();
		}

		/**
		 * Create list of post objects from post ids picked in manual list.
		 *
		 * @param array $category Category to fetch posts from.
		 * @param int $number_of_posts Number of posts to fetch.
		 *
		 * @return List of posts to loop in the template.
		 */
		protected function populate_automatic_list( $category, $number_of_posts ) {

			$args = [
				'post_type'      => 'post',
				'posts_per_page' => $number_of_posts,
				'no_found_rows'  => true,
				'tax_query'      => [
					[
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $category,
					],
				],
			];

			return new \WP_Query( $args );
		}

		/**
		 * Create list of post objects from post ids picked in manual list.
		 *
		 * @param array $post_ids List of ids.
		 *
		 * @return List of posts to loop in the template.
		 */
		protected function populate_manual_list( $post_ids ) {

			$args = [
				'post_type'              => [ 'post', 'page' ],
				'posts_per_page'         => count( $post_ids ),
				'orderby'                => 'post__in',
				'post__in'               => $post_ids,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			];

			return new \WP_Query( $args );
		}

	}
} // End if().

