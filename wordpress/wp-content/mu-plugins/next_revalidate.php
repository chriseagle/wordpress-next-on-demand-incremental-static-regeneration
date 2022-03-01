<?php

/**
 * Prevent direct access
 */
defined('ABSPATH') or die();

/**
 * On save_post schedule a one-time event
 * Priority @ 99 to run after other events on same hook
 */
add_action('save_post', function( int $post_ID, WP_Post $post, bool $update ) {

  // whitelist of post types to check against
  $type_whitelist = array(
    'post',
    'page',
    'custom-type'
  );

  // check if post is one we want to invalidate and check status
  if( false === in_array( $post->post_type, $type_whitelist ) || 'publish' !== $post->post_status ) {
    return;
  }

  // schedule one time event
  wp_schedule_single_event( time(), 'next_revalidate', array( 'post_ID' => $post_ID ) );

}, 99, 3);

/**
 * One-time event to request our api endpoint in Next.js
 */
add_action('next_revalidate', function( int $post_ID ) {
  $secret = 'SuperSecretSquirrel';
  $parts = wp_parse_url( get_permalink( $post_ID ) );
  $path = urlencode( $parts['path'] );
  wp_remote_get( 
    "https://nextapp.com/api/revalidate?secret={$secret}&path={$path}",
  );
}, 10, 1);

