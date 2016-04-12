<?php

/**
 * Plugin Name: Unfeature
 * Description: Choose whether or not to show the featured image on the single post page
 * Version: 0.1
 * Author: Grant Mangham
 * Author URI: http://begtodiffer.ca
 * License: GPLv2 or later
 */

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

new Unfeature();

class Unfeature {

	public function __construct() {
		add_filter( 'admin_post_thumbnail_html', array( $this, 'unfeature_add_checkbox' ), 10, 2 );
		add_action( 'post_updated', array( $this, 'unfeature_update_meta' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'unfeature_image' ) );
	}

	function unfeature_add_checkbox( $content, $post_id ) {
		$unfeature_image = ( get_post_meta( absint( $post_id ), 'unfeature_image', true ) );
		$content .= '<div class="unfeature-image"><p class="description"><label><input type="checkbox" name="unfeature_image" value="1" ' . checked( $unfeature_image, true, false ) . ' /> Don\'t show on single post</label></p></div>';
		return $content;
	}

	public function unfeature_update_meta( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( post_type_supports( get_post_type(), 'thumbnail' ) ) {
			if ( isset( $_POST['unfeature_image'] ) && $_POST['unfeature_image'] ) {
				update_post_meta( $post_id, 'unfeature_image', 1 );
			} else {
				delete_post_meta( $post_id, 'unfeature_image' );
			}
		}
	}

	function unfeature_image( $img ) {
		if ( is_single() ) {
			global $post;
			$unfeature_image = ( get_post_meta( absint( $post->ID ), 'unfeature_image', true ) );
			if ( $unfeature_image ) {
				$img = '<!-- Unfeatured image -->';
			}
		}
		return $img;
	}

}