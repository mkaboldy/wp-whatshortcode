<?php
/**
 * WP-WhatShortCode plugin class.
 *
 * @package WordPress
 */

namespace WP_WhatShortCode;

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_WhatShortCode' ) ) {
	/**
	 * Main class to implement template columns in admin.
	 * Do not instantiate, static only.
	 */
	class WP_WhatShortCode {
		/**
		 * Column identifier in admin view
		 *
		 * @var string
		 */
		private static $column_id = 'shortcode';

		/**
		 * Configure hooks.
		 *
		 * @return void
		 */
		public static function init() {

			foreach ( $GLOBALS['_wp_post_type_features'] as $post_type => $features ) {

				if ( isset( $features['editor'] ) ) {

					add_filter( "manage_{$post_type}_posts_columns", array( __NAMESPACE__ . '\WP_WhatShortCode', 'manage_posts_columns' ) );
					add_action( "manage_{$post_type}_posts_custom_column", array( __NAMESPACE__ . '\WP_WhatShortCode', 'manage_posts_custom_column' ), 10, 2 );

				}
			}
		}

		/**
		 * Adds shortcode column header, callback to hook 'manage_{$post_type}_posts_columns'
		 *
		 * @param array $columns existing admin columns.
		 * @return array the updated columns.
		 */
		public static function manage_posts_columns( $columns ) {
			$columns[ self::$column_id ] = __( 'Shortcode' );
			return $columns;
		}

		/**
		 * Adds content to shortcode column, callback of hook "manage_{$post_type}_posts_custom_column"
		 *
		 * @param string $column column id.
		 * @param int    $post_id the id of the current post.
		 *
		 * @return void
		 */
		public static function manage_posts_custom_column( $column, $post_id ) {
			global $post;
			if ( self::$column_id === $column ) {

				$pattern = get_shortcode_regex();
 
				if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )) {
					$shortcodes = array();
					foreach ( $matches[2] as $tag) {
						$display_title = '';
						$display_class = '';

						if ( shortcode_exists($tag)) {
					
						} else {
							$display_title = __( 'Shortcode doesn\'t exist' );
							$display_class = 'notice notice-error';
						}
						
						$shortcodes[] = sprintf(
							'<span class="%1$s" title="%2$s"> %3$s </span>',
							esc_html( $display_class ),
							esc_html( $display_title ),
							esc_html($tag)
						);		
					}
					echo implode(', ', $shortcodes);
				}
			}
		}

	}
	WP_WhatShortCode::init();
}
