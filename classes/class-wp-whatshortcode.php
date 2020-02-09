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

				// phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar
				// phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound
				// phpcs:disable Squiz.Strings.DoubleQuoteUsage.NotRequired
				$pattern =
						'\\['                            // Opening bracket
						. '(\\[?)'                       // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
					. "([a-zA-Z0-9_-]*)"                 // 2: Shortcode name
					. '(?![\\w-])'                       // Not followed by word character or hyphen
					. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
					.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
					.     '(?:'
					.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
					.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
					.     ')*?'
					. ')'
					. '(?:'
					.     '(\\/)'                        // 4: Self closing tag ...
					.     '\\]'                          // ... and closing bracket
					. '|'
					.     '\\]'                          // Closing bracket
					.     '(?:'
					.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
					.             '[^\\[]*+'             // Not an opening bracket
					.             '(?:'
					.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
					.                 '[^\\[]*+'         // Not an opening bracket
					.             ')*+'
					.         ')'
					.         '\\[\\/\\2\\]'             // Closing shortcode tag
					.     ')?'
					. ')'
					. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

				if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) ) {
					$shortcodes = array();
					foreach ( $matches[2] as $tag ) {
						$display_title = '';
						$display_class = '';

						if ( ! shortcode_exists( $tag ) ) {
							$display_title = __( 'Shortcode doesn\'t exist' );
							$display_class = 'notice notice-error';
						}

						$shortcodes[] = sprintf(
							'<span id="%4s" class="%1$s" title="%2$s"> %3$s </span>',
							$display_class,
							esc_html( $display_title ),
							esc_html( $tag ),
							$post_id
						);
					}

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo implode( ', ', $shortcodes );
				}
			}
		}

	}
	WP_WhatShortCode::init();
}
