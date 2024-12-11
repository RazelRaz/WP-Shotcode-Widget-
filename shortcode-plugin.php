<?php
/*
 * Plugin Name:       Shortcode Widget Plugin
 * Description:       Shortcode Widget Plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Razel Ahmed
 * Author URI:        https://razelahmed.com
 */
if ( ! defined('ABSPATH') ) {
  exit;
}

class R_Shortcode_plugin {
    public function __construct() {
      add_action( 'init', [ $this, 'initialize' ] );
    }

    public function initialize() {
        add_shortcode( 'message', [ $this, 'r_show_static_message' ] );
        add_shortcode( 'students', [ $this, 'manage_student_data' ] );
        add_shortcode( 'list-post', [ $this, 'ra_list_posts' ] );
        add_shortcode( 'list-post-two', [ $this, 'ra_list_posts_wp_query' ] );
    }

    // Basic Shortcode
    public function r_show_static_message() {
      return '<p style="color:red;">This is A Basic TEXT</p>';
    }

    // Shortcode with parameters
    public function manage_student_data( $attributes ) {
      $attributes = shortcode_atts( [
        'name' => 'John Doe',
        'email' => 'example@mail.com',
      ], [
        $attributes
      ], 'students' );

      return "<h3>Name - {$attributes['name']}, Email - {$attributes['email']} </h3>";
    }

    // render dynamic data from database
    // Shortcode with DB operation
    public function ra_list_posts() {

      global $wpdb;

      $table_prefix = $wpdb->prefix; // wp_
      $table_name = $table_prefix . "posts"; // wp_posts

      // Get post_type = post & post_status = publish
      $posts = $wpdb->get_results (
        "SELECT post_title from {$table_name} WHERE post_type = 'post' AND post_status = 'publish' "
      );

      // echo '<pre>';
      // print_r($posts);
      // echo '</pre>';

      if( count( $posts ) > 0 ) {

        $outputHtml = '<ul>';
        foreach ( $posts as $post ) {
          $outputHtml .= '<li>' . $post->post_title . '</li>';
        }
        $outputHtml .= '</ul>';

        return $outputHtml;
      }
      return 'No Posts Found';
    }


    // render posts with wp_query
    // [list-post-two number="10"]
    public function ra_list_posts_wp_query( $attributes ) {
      $attributes = shortcode_atts( [
          'number' => 5, 
      ], $attributes, 'list-post-two' );

      $query = new WP_Query([
        'posts_per_page' => $attributes['number'],
        'post_status' => 'publish'
      ]);

      if( $query->have_posts() ) {
        $htmlOutput = '<ul>';
        while( $query->have_posts() ) {
          $query->the_post();
          $htmlOutput .= '<li><a href="'.get_the_permalink().'">'. get_the_title() .'</a></li>';
        }
        $htmlOutput .= '</ul>';

        return $htmlOutput;
      }

      return 'No Post Found';

    }

}

new R_Shortcode_plugin();