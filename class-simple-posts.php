<?php
/**
 * Simple Posts module class
 *
 * @package Hogan
 */

declare( strict_types = 1 );

namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\\Dekode\\Hogan\\Simple_Posts' ) && class_exists( '\\Dekode\\Hogan\\Module' ) ) {

	/**
	 * Simple Posts module class
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
		 *
		 * @return array $fields Fields for this module
		 */
		public function get_fields() : array {

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
					'required'          => 0,
					'conditional_logic' => [
						[
							[
								'field'    => $this->field_key . '_list_type',
								'operator' => '==',
								'value'    => 'manual',
							],
						],
					],
					'post_type'         => apply_filters( 'hogan/module/simple_posts/post_types', [ 'post', 'page' ] ),
					'taxonomy'          => [],
					'filters'           => [
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					],
					'elements'          => [
						0 => 'featured_image',
					],
					'min'               => 0,
					'max'               => apply_filters( 'hogan/module/simple_posts/manual_list/max_count', '' ),
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
					'required'          => 0,
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
					'max'               => apply_filters( 'hogan/module/simple_posts/automatic_list/max_count', '' ),
					'step'              => 1,
					'wrapper'           => [
						'width' => '50',
					],
				],
			];
		}

		/**
		 * Map raw fields from acf to object variable.
		 *
		 * @param array $raw_content Content values.
		 * @param int   $counter Module location in page layout.
		 *
		 * @return void
		 */
		public function load_args_from_layout_content( array $raw_content, int $counter = 0 ) {

			$this->list_type = $raw_content['list_type'];
			$this->card_type = $raw_content['card_type'];

			if ( 'manual' === $this->list_type ) :
				$this->query = $this->populate_manual_list( $raw_content['manual_list'] ?: [] );
			elseif ( 'automatic' === $this->list_type ) :
				$this->query = $this->populate_automatic_list( (int) $raw_content['automatic_list'], (int) $raw_content['number_of_items'] );
			endif;

			add_filter( 'post_type_link', [ $this, 'on_post_type_link' ], 10, 2 ); // add filter for post link - custom type.
			add_filter( 'the_title', [ $this, 'on_the_title' ], 10, 2 ); // add filter for custom title.
			add_filter( 'the_excerpt', [ $this, 'on_the_excerpt' ], 10, 1 ); // add filter for custom excerpt.
			add_filter( 'get_post_metadata', [ $this, 'on_get_post_metadata' ], 10, 3 ); // add filter for custom image id.

			parent::load_args_from_layout_content( $raw_content, $counter );
		}

		/**
		 * Filter hook for post link.
		 *
		 * @param string   $permalink The post's permalink.
		 * @param \WP_Post $post The post in question.
		 *
		 * @return string
		 */
		public function on_post_type_link( string $permalink, \WP_Post $post ) : string {
			return apply_filters( 'hogan/module/simple_posts/post_type_link', $permalink, $post, $this );
		}

		/**
		 * Filter hook for custom title.
		 *
		 * @param string $title The post title.
		 * @param int    $id The post ID.
		 *
		 * @return string
		 */
		public function on_the_title( string $title, int $id ) : string {
			return apply_filters( 'hogan/module/simple_posts/the_title', $title, $id, $this );
		}

		/**
		 * Filter hook for custom excerpt.
		 *
		 * @param string $excerpt Post excerpt.
		 *
		 * @return string
		 */
		public function on_the_excerpt( string $excerpt ) : string {
			return apply_filters( 'hogan/module/simple_posts/the_excerpt', $excerpt, $this );
		}

		/**
		 * Filter hook for returning custom image id.
		 *
		 * @param null   $check The value received from the filter.
		 * @param int    $post_id WP Post id.
		 * @param string $meta_key Meta key.
		 *
		 * @return mixed Null or custom post meta id for attachment.
		 */
		public function on_get_post_metadata( $check, $post_id, $meta_key ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
			if ( '_thumbnail_id' === $meta_key ) {
				return apply_filters( 'hogan/module/simple_posts/the_image_metadata_value', $check, $post_id, $this );
			}

			return $check;
		}

		/**
		 * Validate module content before template is loaded.
		 *
		 * @return bool Whether validation of the module is successful / filled with content.
		 */
		public function validate_args() : bool {
			return ! empty( $this->query ) && ! empty( $this->query->have_posts() );
		}

		/**
		 * Use super class method to reset post data.
		 *
		 * @return void
		 */
		protected function render_closing_template_wrappers() {
			parent::render_closing_template_wrappers();
			remove_filter( 'post_type_link', [ $this, 'on_post_type_link' ] ); // remove filter for post link.
			remove_filter( 'the_title', [ $this, 'on_the_title' ] ); // remove filter for custom title.
			remove_filter( 'the_excerpt', [ $this, 'on_the_excerpt' ] ); // remove filter for custom excerpt.
			remove_filter( 'get_post_metadata', [ $this, 'on_get_post_metadata' ] ); // remove filter for custom image id.
			\wp_reset_postdata();
		}

		/**
		 * Create list of post objects from post ids picked in manual list.
		 *
		 * @param int $category Category to fetch posts from.
		 * @param int $number_of_posts Number of posts to fetch.
		 *
		 * @return \WP_Query Posts to loop in the template.
		 */
		protected function populate_automatic_list( int $category, int $number_of_posts ) : \WP_Query {

			$args = [
				'post_type'      => 'post',
				'posts_per_page' => $number_of_posts ?? absint( get_option( 'posts_per_page' ) ),
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
		 * @return \WP_Query Posts to loop in the template.
		 */
		protected function populate_manual_list( array $post_ids ) : \WP_Query {

			// Check if there are any posts in the list. Array with a zero value will return empty WP Query object.
			$post_ids = ! empty( $post_ids ) ? $post_ids : [ 0 ];
			$args     = [
				'post_type'              => apply_filters( 'hogan/module/simple_posts/post_types', [ 'post', 'page' ] ),
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
}
