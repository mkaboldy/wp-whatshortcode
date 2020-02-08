<?php
/**
Plugin Name: WP-WhatShortCode
Plugin URI: https://github.com/mkaboldy/wp-whatshortcode
Description: Adds a shortcode column to page/post admin
Version: 1.0.0
Author: Miklos Kaboldy
Author URI: https://github.com/mkaboldy

 * @package WordPress
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	echo 'This file should not be accessed directly!';
	exit; // Exit if accessed directly.
}

if ( ! is_admin() ) {
	return;
}

require 'classes/class-wp-whatshortcode.php';
