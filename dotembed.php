<?php
/*
Plugin Name: Dot Embed
Plugin URI: http://dot.vu
Description: Embed Dot pages and apps using this code: <code>[dotembed url="" title="" dotext="" dotid="" pageext="" pageid="" width="" height="" ratio="" loading="" loadingcolor=""]</code>
Version: 2.1.4
Author: Pedro Figueiredo
Author URI: http://dot.vu
License: GPL2

Dot Embed is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Dot Embed is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Dot Embed. If not, visit http://dot.vu and reach out to
us for any questions regarding licensing.
*/

class DotEmbed {

  /*******************************************************************
   *
   * Properties
   *
   *******************************************************************/

  // PUBLIC

  // PRIVATE
  private $defaults;

  /*******************************************************************
   *
   * Constructs
   *
   *******************************************************************/

  public function __construct() {
    $this->defaults = array(
      'url' => '',
      'dotext' => '',
      'dotid' => '',
      'pageext' => '',
      'pageid' => '',
      'width' => '100%',
      'height' => 'auto',
      'ratio' => '',
      'loading' => 'no',
      'loadingcolor' => ''
    );
    add_shortcode('dotembed', array(&$this, 'shortcode'));
  }

  /*******************************************************************
   *
   * Public
   *
   *******************************************************************/

  /**
   * The wp shortcode handler to be added with add_shortcode
   * @param Array $attrs
   * @return return
   */
  public function shortcode( $attrs, $content = null, $code = '' ) {
    return $this->render($attrs);
  }

  /*******************************************************************
   *
   * Private
   *
   *******************************************************************/

  /**
   * Adds defaults, validates and renders the iframe with given args
   * @param Array $args
   * @return String html content from buffer
   */
  private function render( $args ) {
    // make sure arguments are valid
    $this->applyDefaults($args);
    // fix broken values
    $args['width']  = preg_replace( '/[^0-9%px]/', '', $args['width'] );
    if($args['height'] != 'auto') {
      $args['height'] = preg_replace( '/[^0-9%px]/', '', $args['height'] );      
    }
    if(!in_array( $args['scroll'], array( 'auto', 'yes', 'no' ))) $args['scroll'] = 'no';
    // extract variables from args
    extract($args);

    // uuid to correlate classe
    $dotembedId = uniqid();

    // prep output
    ob_start();    
?>

<style type="text/css">
      
<?php if($ratio): ?>
    
    .dotembed-<?php echo esc_attr($dotembedId); ?> .dotembed-iframe-container {
      height: 0;
      overflow: hidden;
      padding-bottom: <?php echo esc_attr($ratio); ?>;
    }

    .dotembed-<?php echo esc_attr($dotembedId); ?> .dotembed-iframe-container iframe {
      position: absolute;
      top: 0; 
      left: 0;
      width: 100%;
      height: 100%;
    }

<?php endif; ?>

<?php if($loading === "yes" && $loadingcolor): ?>

  .dotembed-<?php echo esc_attr($dotembedId); ?> .spinner > div {
    background-color: <?php echo esc_attr($loadingcolor); ?>;
  }

<?php endif; ?>

</style>
<div class="dotembed dotembed-<?php echo esc_attr($dotembedId); ?>"
     data-dot-height="<?php echo esc_attr($height); ?>"
     data-dot-width="<?php echo esc_attr($width); ?>"
     data-dot-ratio="<?php echo esc_attr($ratio); ?>">
  <div class="dotembed-loading">
    <div class="spinner">
      <div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div>
    </div>
  </div>
  <div class="dotembed-iframe-container"
    data-dot-ext="<?php echo esc_attr($dotext); ?>"
    data-dot-id="<?php echo esc_attr($dotid); ?>"
    data-page-ext="<?php echo esc_attr($pageext); ?>"
    data-page-id="<?php echo esc_attr($pageid); ?>"></div>
</div>

<?php

    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }

  /**
   * Fixes and applies the defaults to a given array
   * @param &$r array to assure defaults
   */
  private function applyDefaults( &$args ) {
    // invalid, fix it
    if(!is_array($args)) $args = array();
    // apply missing defaults
    foreach($this->defaults as $key => $default) {
      if(!isset($args[$key])) $args[$key] = $default;
    }
  }

}

function dotembed_load_deps() {
  // style
  wp_register_style('dotembed_style', plugins_url('css/style.css',__FILE__ ));
  wp_enqueue_style('dotembed_style');
  // javascript (w/ jquery dependency)
  wp_register_script( 'dotmsg_js', plugins_url('js/dotmsg.js',__FILE__ ), array('jquery'));
  wp_enqueue_script('dotmsg_js');
  wp_register_script( 'dotembed_js', plugins_url('js/dotembed.js',__FILE__ ), array('jquery', 'dotmsg_js'));
  wp_enqueue_script('dotembed_js');
}

function register_dotembed() {
  global $dotembed;
  $dotembed = new DotEmbed();
}

add_action( 'init', 'dotembed_load_deps');
add_action( 'init', 'register_dotembed' );

?>