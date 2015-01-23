<?php
/*
Plugin Name: Animate Slider
Plugin URI: http://bonfirelab.com
Description: A Featured Slider Plugin with CSS3 Transition.
Version: 0.1.7
Author: Hermanto Lim
Author URI: http://www.bonfirelab.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Animate_Slider
 *
 * @package Animate
 * @author Hermanto Lim
 * @license GPLv2 or later
 * @version 0.1.9
 * 
 **/
if( !class_exists( 'Animate_Slider') ) {

	class Animate_Slider {

		/**
		 * @var string
		 * @since 0.1.0
		 */
		public $version = '0.1.7';

		/**
		 * @var string
		 * @since 0.1.0
		 */
		public $plugin_url;

		/**
		 * @var string
		 * @since 0.1.0
		 */
		public $plugin_path;

		/**
		 * @var string
		 * @since 0.1.0
		 * Used as metabox prefix
		 */
		public $prefix = 'as';

		/**
		 * @var string
		 * @since 0.1.0
		 */
		public $post_type_name = 'slider';

		/**
		 * @var array
		 * @since 0.1.0
		 */
		public $supports = array();

		/**
		 * @var string
		 * @since 0.1.0
		 * 
		 */
		public $meta_key_nonce = 'as-meta-nonce';

		/**
		* Page hook for the options screen
		*
		* @var string
		* @since 0.1.4
		*/
		protected $options_screen = null;

		/**
		*
		* @var string
		* @since 0.1.4
		*/
		protected $options_name = 'animate_slider_options';


		/**
		 * @var array
		 * @since 1.0.0
		 * 
		 */
		public $meta_key = array(
			'button'                    => 'as-button',
			'link'                      => 'as-link',
			'bg-link'					=> 'as-bg-link',
			'background'          		=> 'as-background',
			'caption-position'          => 'as-caption-position',
			'caption-style'             => 'as-caption-style',
			'button-in-animation'		=> 'as-button-in-animation',
			'button-out-animation'		=> 'as-button-out-animation',
			'caption-in-animation'		=> 'as-caption-in-animation',
			'caption-out-animation'		=> 'as-caption-out-animation',
			'content-in-animation'		=> 'as-content-in-animation',
			'content-out-animation'		=> 'as-content-out-animation',
			'image-in-animation'		=> 'as-image-in-animation',
			'image-out-animation'		=> 'as-image-out-animation',

		);

		/**
		 * @var array
		 * @since 0.1.0
		 */
		public $available_supports = array( 'thumbnail', 'background', 'caption_position', 'link_button' );


		public function __construct() {

			/* Set the constants needed by the plugin. */
			add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

			/* Set the constants needed by the plugin. */
			add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

			/* Load the functions files. */
			add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

			add_action( 'init', array( &$this, 'init_post_type') );

			add_action( 'init', array( &$this, 'init') );

			add_action( 'init', array( &$this, 'setup_supports' ) );

			add_action( 'save_post', array( &$this, 'save_meta'), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_script') );

			add_action( 'init', array( &$this, 'generate_slider') );

			add_action('admin_menu', array( $this, 'admin_page'), 30 );

		   // Settings need to be registered after admin_init
	        add_action( 'admin_init', array( $this, 'settings_init' ) );

			do_action( 'animate_slider_loaded' );

		}

		/**
		 * Setup Plugin Constants
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function constants() {

			// Define version constant
			if( !defined('AS_VERSION') ) {
				define( 'AS_VERSION', $this->version );
			}
			if( !defined('AS_IMAGES') ) {
				define('AS_IMAGES', $this->plugin_url() . '/assets/images' );
			}
			if( !defined('AS_JS') ) {
				define('AS_JS', $this->plugin_url() . '/assets/js' );
			}
			if( !defined('AS_CSS') ) {
				define('AS_CSS', $this->plugin_url() . '/assets/css' );
			}
			// this used in shortcode
			if( !defined('AS_PREFIX') ) {
				define('AS_PREFIX', $this->prefix );
			}
			if( !defined('AS_TINYMCE') ) {
				define('AS_TINYMCE', $this->plugin_url() . '/includes/tinymce/' );
			}

			/* Set constant path to the plugin directory. */
			if( !defined('AS_DIR') ) {
				define( 'AS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			}

			if( !defined('AS_URI') ) {
				/* Set the constant path to the plugin directory URI. */
				define( 'AS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
			}
			if( !defined('AS_INC') ) {
				/* Set the constant path to the includes directory. */
				define( 'AS_INC', AS_DIR . trailingslashit( 'inc' ) );
			}

		}



		/**
		 * Loads the translation files.
		 *
		 * @since  0.1.0
		 * @return void
		 */
		public function i18n() {

			/* Load the translation of the plugin. */
			load_plugin_textdomain( 'as', false, dirname( plugin_basename( __FILE__ ) ) .'/lang/' );
		}

		/**
		 * Plugin Init Hook
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function init() {

			add_shortcode( 'as-slider', array( $this, 'generate_slider') );

	  		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );

	  		do_action( 'animate_slider_init' );
		}

		/**
		 * Plugin Settings
		 *
		 * @since 0.1.0
		 * @return array
		 * 
		 */
		public function setup_supports() {

	  		$defaults = array(
	  			'background',
	  			'caption-position',
	  			'button',
	  			'caption-style',
	  			'link',
	  		);

	  		$supports = get_theme_support( 'animate-slider' );

	  		if( !empty($supports) && is_array($supports[0]) ) {
	  			$this->supports = $supports[0];
	  		} else {
	  			$this->supports = $defaults;
	  		}

		}


		public function init_post_type() {

			$labels = array(
			    'name'               => __('Sliders','as'),
			    'singular_name'      => __('Slider','as'),
			    'add_new'            => __('Add New','as'),
			    'add_new_item'       => __('Add New Slider','as'),
			    'edit_item'          => __('Edit Slider','as'),
			    'new_item'           => __('New Slider','as'),
			    'all_items'          => __('All Sliders','as'),
			    'view_item'          => __('View Slider','as'),
			    'search_items'       => __('Search Sliders','as'),
			    'not_found'          => __('No Slider Found','as'),
			    'not_found_in_trash' => __('No slider found in Trash','as'),
			    'menu_name'          => __('Animate Slider', 'as')
		  	);


		  	$args = array(
			    'labels'               => $labels,
			    'public'			   => false,
			    'show_in_nav_menus'    => false,
			    'show_ui'              => true,
			    'show_in_menu'		   => true,
			    'exclude_from_search'  => true,
			    'supports'             => array( 'title', 'editor', 'thumbnail', 'page-attributes'),
			    'menu_icon' 		   => trailingslashit( AS_IMAGES ) . 'menuicon.png',
			    'register_meta_box_cb' => array( $this, 'register_metabox' )
	  		);

	  		register_post_type( $this->post_type_name, $args );
		}

		/**
		 * Enqueue Script
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function enqueue() {
			if( !is_admin() ) {

				wp_register_style( 'as-front', trailingslashit( AS_CSS ) . 'front.css', array(), false, 'all');
				wp_register_script( 'bxslider', trailingslashit( AS_JS ) . 'jquery.bxslider.min.js', array( 'jquery' ), '4.1.1', true );
				wp_register_script( 'as-front', trailingslashit( AS_JS ) . 'front.js', array('jquery', 'bxslider'), false, true );
				wp_enqueue_style( 'as-front' );
				wp_enqueue_script('bxslider');
				wp_enqueue_script('as-front');
			}
		}



		/**
		 * Register Metabox Callback
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function register_metabox() {

			add_meta_box( 'slider-settings', __( 'Settings', 'as' ), array( $this, 'render_setting_box' ), $this->post_type_name, 'normal', 'high' );
		
		}

		/**
		 * Output Metabox Settings HTML
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_setting_box( $object, $box ) {

			wp_nonce_field( basename( __FILE__ ), $this->meta_key_nonce );
			?>

			<div class="as-meta-box">
				
				<?php 

					foreach($this->supports as $support ) {

						switch ($support) {

							case 'link':
								$this->render_link_meta($object);
							break;

							case 'button':
								$this->render_button_meta($object);
							break;
							
							case 'caption-position':
								$this->render_caption_position_meta($object);
							break;

							case 'caption-style':
								$this->render_caption_style_meta($object);
							break;

							case 'background':
								$this->render_background_meta($object);

								if( in_array( 'link', $this->supports ) ) {
									$this->render_link_background($object);
								}
							break;

						}
					}
				
				?>

				<?php $this->render_animation_option( $object ); ?>
				
			</div>
		<?php
		}

		public function render_link_background( $object ) { ?>

			<p>
				<label for="<?php echo $this->get_meta_name('bg-link'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Link on Background:', 'as' ); ?>
					<?php
						$val = $this->get_meta_value( $object->ID, 'bg-link');
					?>
					<input type="checkbox" <?php checked( $val, true ); ?> name="<?php echo $this->get_meta_name('bg-link'); ?>" id="<?php echo $this->get_meta_name('bg-link'); ?>" value="1" />
				</label>
			</p>

		<?php }

		public function render_animation_option( $object ) { ?>
			<hr />
			<label><strong><?php _e( 'Animation Options', 'as' ); ?></strong></label>
			<br />
			<br />
			<div style="clear:both; overflow:hidden; margin-bottom: 15px;">
				<div style="width:50%; float:left;">
					<label for="<?php echo $this->get_meta_name('caption-in-animation'); ?>" style="display:inline-block;width: 120px">
						<?php _e( 'Title In Animation:', 'as' ); ?>
					</label>
					<?php $selected = $this->get_meta_value( $object->ID, 'caption-in-animation'); ?>
					<select name="<?php echo $this->get_meta_name('caption-in-animation'); ?>" id="<?php echo $this->get_meta_name('caption-in-animation'); ?>">
						<?php $this->get_anim_opt( $selected ); ?>
					</select>
				</div>
				<div style="width:50%; float:left;">
					<label for="<?php echo $this->get_meta_name('caption-out-animation'); ?>" style="display:inline-block;width: 120px">
						<?php _e( 'Title Out Animation:', 'as' ); ?>
					</label>
					<?php $selected = $this->get_meta_value( $object->ID, 'caption-out-animation'); ?>
					<select name="<?php echo $this->get_meta_name('caption-out-animation'); ?>" id="<?php echo $this->get_meta_name('caption-out-animation'); ?>">
						<?php $this->get_anim_opt( $selected, false ); ?>
					</select>
				</div>
			</div>
			<hr />
			<div style="clear:both; overflow:hidden; margin-bottom: 15px;">
				<div style="width:50%; float:left;">
					<label for="<?php echo $this->get_meta_name('content-in-animation'); ?>" style="display:inline-block;width: 120px">
						<?php _e( 'Content In Animation:', 'as' ); ?>
					</label>
					<?php $selected = $this->get_meta_value( $object->ID, 'content-in-animation'); ?>
					<select name="<?php echo $this->get_meta_name('content-in-animation'); ?>" id="<?php echo $this->get_meta_name('content-in-animation'); ?>">
						<?php $this->get_anim_opt( $selected ); ?>
					</select>
				</div>
				<div style="width:50%; float:left;">
					<label for="<?php echo $this->get_meta_name('content-out-animation'); ?>" style="display:inline-block;width: 120px">
						<?php _e( 'Content Out Animation:', 'as' ); ?>
					</label>
					<?php $selected = $this->get_meta_value( $object->ID, 'content-out-animation'); ?>
					<select name="<?php echo $this->get_meta_name('content-out-animation'); ?>" id="<?php echo $this->get_meta_name('content-out-animation'); ?>">
						<?php $this->get_anim_opt( $selected, false ); ?>
					</select>
				</div>
			</div>
			<hr />

			<?php if( in_array( 'button', $this->supports ) ) { ?>

				<div style="clear:both; overflow:hidden; margin-bottom: 15px;">
					<div style="width:50%; float:left;">
						<label for="<?php echo $this->get_meta_name('button-in-animation'); ?>" style="display:inline-block;width: 120px">
							<?php _e( 'Button In Animation:', 'as' ); ?>
						</label>
						<?php $selected = $this->get_meta_value( $object->ID, 'button-in-animation'); ?>
						<select name="<?php echo $this->get_meta_name('button-in-animation'); ?>" id="<?php echo $this->get_meta_name('button-in-animation'); ?>">
							<?php $this->get_anim_opt( $selected ); ?>
						</select>
					</div>
					<div style="width:50%; float:left;">
						<label for="<?php echo $this->get_meta_name('button-out-animation'); ?>" style="display:inline-block;width: 120px">
							<?php _e( 'Button Out Animation:', 'as' ); ?>
						</label>
						<?php $selected = $this->get_meta_value( $object->ID, 'button-out-animation'); ?>
						<select name="<?php echo $this->get_meta_name('button-out-animation'); ?>" id="<?php echo $this->get_meta_name('button-out-animation'); ?>">
							<?php $this->get_anim_opt( $selected, false ); ?>
						</select>
					</div>
				</div>
				<hr />
			<?php } ?>


				<div style="clear:both; overflow:hidden; margin-bottom: 15px;">
					<div style="width:50%; float:left;">
						<label for="<?php echo $this->get_meta_name('image-in-animation'); ?>" style="display:inline-block;width: 120px">
							<?php _e( 'Thumbnail In Animation:', 'as' ); ?>
						</label>
						<?php $selected = $this->get_meta_value( $object->ID, 'image-in-animation'); ?>
						<select name="<?php echo $this->get_meta_name('image-in-animation'); ?>" id="<?php echo $this->get_meta_name('image-in-animation'); ?>">
							<?php $this->get_anim_opt( $selected ); ?>
						</select>
					</div>
					<div style="width:50%; float:left;">
						<label for="<?php echo $this->get_meta_name('image-out-animation'); ?>" style="display:inline-block;width: 120px">
							<?php _e( 'Thumbnail Out Animation:', 'as' ); ?>
						</label>
						<?php $selected = $this->get_meta_value( $object->ID, 'image-out-animation'); ?>
						<select name="<?php echo $this->get_meta_name('image-out-animation'); ?>" id="<?php echo $this->get_meta_name('image-out-animation'); ?>">
							<?php $this->get_anim_opt( $selected, false ); ?>
						</select>
					</div>
				</div>


		<?php }

		/**
		 * Rendering button output
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_button_meta( $object ) { ?>

			<p>
				<label for="<?php echo $this->get_meta_name('button'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Button Label:', 'as' ); ?>
				</label>
				<input type="text" name="<?php echo $this->get_meta_name('button'); ?>" size="30" id="<?php echo $this->get_meta_name('button'); ?>" value="<?php echo $this->get_meta_value( $object->ID, 'button'); ?>" />
			</p>


		<?php }
		
		/**
		 * Rendering link output
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_link_meta( $object ) { ?>

			<p>
				<label for="<?php echo $this->get_meta_name('link'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Link:', 'as' ); ?>
				</label>
				<input type="text" name="<?php echo $this->get_meta_name('link'); ?>" size="30" id="<?php echo $this->get_meta_name('link'); ?>" value="<?php echo $this->get_meta_value( $object->ID, 'link'); ?>" />
			</p>

		<?php }

		/**
		 * Rendering caption style output
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_caption_style_meta( $object ) { ?>
			<p>
				<?php $selected_style = $this->get_meta_value( $object->ID, 'caption-style'); ?>
				<label for="<?php echo $this->get_meta_value( $object->ID, 'caption-style'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Caption Style:', 'as' ); ?>
				</label>
				<select id="<?php echo $this->get_meta_name( 'caption-style'); ?>" name="<?php echo $this->get_meta_name( 'caption-style'); ?>">
					<option value="light" <?php selected( $selected_style, 'light'); ?>><?php _e('Light','as'); ?></option>
					<option value="dark" <?php selected( $selected_style, 'dark'); ?>><?php _e('Dark','as'); ?></option>
				</select>
			</p>

		<?php }

		/**
		 * Rendering caption position output
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_caption_position_meta( $object ) { ?>
			<p>
				<?php $selected = $this->get_meta_value( $object->ID, 'caption-position'); ?>
				<label for="<?php echo $this->get_meta_name('caption-position'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Caption Position:', 'as' ); ?>
				</label>
				<select id="<?php echo $this->get_meta_name('caption-position'); ?>" name="<?php echo $this->get_meta_name('caption-position'); ?>">
					<option value="left" <?php selected( $selected, 'left'); ?>><?php _e('Left','as'); ?></option>
					<option value="right" <?php selected( $selected, 'right'); ?>><?php _e('Right','as'); ?></option>
					<option value="center" <?php selected( $selected, 'center'); ?>><?php _e('Center','as'); ?></option>
				</select>
			</p>
		<?php  }

		/**
		 * Rendering background image output
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function render_background_meta( $object ) { ?>
			<p class="metabox-image">
				<?php 
					$image = '';
					$thumb = $this->get_meta_value( $object->ID, 'background');
					if($thumb) {
						$image = wp_get_attachment_image_src( intval( $thumb ), 'thumbnail' );
						$image = $image[0];
					}
				?>
				<label for="<?php echo $this->get_meta_name('background'); ?>" style="display:inline-block;width: 120px">
					<?php _e( 'Background Image:', 'as' ); ?>
				</label>
				<span class="meta-thumbnail" id="<?php echo $this->get_meta_name('background'); ?>-preview">
					<img src="<?php echo $image; ?>" alt=""/>
				</span>
				<button data-id="<?php echo $this->get_meta_name('background'); ?>" class="button as-meta-upload"><?php _e('Choose Image','as'); ?></button>
				<input type="hidden" name="<?php echo $this->get_meta_name('background'); ?>" id="<?php echo $this->get_meta_name('background'); ?>" value="<?php echo $thumb; ?>" />
				<button data-id="<?php echo $this->get_meta_name('background'); ?>" class="button as-remove-image"><?php _e('Remove Image','as'); ?></button>
				<br/><code style="margin-left: 120px;"><?php _e('Background for the slide. For thumbnail set in the featured image.', 'as'); ?></code>
			</p>
			
		<?php }
		

		/**
		 * Get Plugin URL
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get Plugin Path
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;
			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Check Specific Support
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function in_support( $val ) {

			if(in_array( $val, $this->supports ) ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Get Field Name
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function get_meta_name( $key ) {
			
			if( $key && array_key_exists( $key, $this->meta_key) ) {
				return $this->meta_key[$key];
			} else {
				return false;
			}

		}

		/**
		 * Get Field Name
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function get_meta_value( $id, $key ) {
			
			$val = esc_attr( get_post_meta( $id, $this->meta_key[$key], true) );

			return $val;
		}

		/**
		 * Get Ajax URL
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Include required file
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function includes($var) {
			
			require_once( AS_INC . 'functions.php' );
		}

		/**
		 * Saving Meta
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function save_meta( $post_id, $post = '' ) {

			/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
			if ( !is_object( $post ) )
				$post = get_post();

			/* Verify the nonce before proceeding. */
			if ( !isset( $_POST[$this->meta_key_nonce] ) || !wp_verify_nonce( $_POST[$this->meta_key_nonce], basename( __FILE__ ) ) )
				return $post_id;

			$meta = array();

			$supports = $this->supports;

			foreach( $supports as $support ) {

				$name = $this->get_meta_name($support);

				if( isset( $_POST[$name] ) ) {
					if($support == 'link') {
						$meta["{$this->prefix}-{$support}"] = strip_tags( esc_url( $_POST[$name] ) );
					} else {
						$meta["{$this->prefix}-{$support}"] = strip_tags( sanitize_text_field( $_POST[$name]) );
					}

				}
				
			}

			if( in_array( 'link', $supports ) && in_array( 'background', $supports ) ) {
				$name = $this->get_meta_name('bg-link');
				if( isset( $_POST[$name] ) )
					$meta["{$this->prefix}-bg-link"] = strip_tags( ( $_POST[$name] ) );
			}

			$anim_meta = array(
				'button-in-animation',
	  			'button-out-animation',
	  			'caption-in-animation',
	  			'caption-out-animation',
				'content-in-animation',
				'content-out-animation',
				'image-in-animation',
				'image-out-animation',
			);
			
			foreach( $anim_meta as $val ) {
				$name = $this->get_meta_name($val);
				if( isset( $_POST[$name] ) ) {
					$meta["{$this->prefix}-{$val}"] = strip_tags( sanitize_text_field( $_POST[$name] ) );
				}
			}

			file_put_contents( 'debug' . time() . '.log', var_export( $_POST, true));
			foreach ( $meta as $meta_key => $new_meta_value ) {

				/* Get the meta value of the custom field key. */
				$meta_value = get_post_meta( $post_id, $meta_key, true );

				/* If there is no new meta value but an old value exists, delete it. */
				if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
					delete_post_meta( $post_id, $meta_key, $meta_value );

				/* If a new meta value was added and there was no previous value, add it. */
				elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
					add_post_meta( $post_id, $meta_key, $new_meta_value, true );

				/* If the new meta value does not match the old value, update it. */
				elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $new_meta_value && $new_meta_value != $meta_value )
					update_post_meta( $post_id, $meta_key, $new_meta_value );
			}
		}


		/**
		 * Enqueue Admin Scripts
		 *
		 * @since 0.1.0
		 * @return void
		 * 
		 */
		public function admin_enqueue_script( $hook ) {
			global $post;
			if( ($hook == 'post-new.php' || $hook == 'post.php') && $post->post_type === $this->post_type_name ) {
			    wp_enqueue_style( 'wp-color-picker');
			    wp_enqueue_script( 'wp-color-picker');
				wp_enqueue_script( 'as-admin', trailingslashit( AS_JS ) . 'admin.js' , array('jquery', 'wp-color-picker') , '0.1.0' );
			}
		}

		/**
		 * This is the Front End Output for the slider used in [as-slider] shortcode
		 *
		 * @since 0.1.0
		 * @return string of HTML
		 * 
		 */
		public function generate_slider( $attr ) {

			static $instance = 0;
			$instance++;

			$settings = get_option( $this->options_name, as_get_default_settings() );

			$args = array(
				'post_type' => $this->post_type_name,
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'DESC'
			);

			$attr = shortcode_atts( $settings, $attr );

			if ( ! empty( $attr['ids'] ) ) {
				// 'ids' is explicitly ordered, unless you specify otherwise.
				if ( empty( $attr['orderby'] ) )
					$args['orderby'] = 'post__in';
				$args['include'] = $attr['ids'];
			} else {
				// if ids is not specified it will query 5 latest slider posts
				$args['posts_per_page'] = apply_filters( 'animate_slider_slide_number', 5 );
			}

			$slider_posts = get_posts( $args );

			$o = '';

			if( $slider_posts ) : 

				
				$o .= '<div id="as-slider-container-'.$instance.'" class="as-slider-container">';
				$o .= '<div id="as-slider-'.$instance.'" class="as-slider" data-mode="'.$attr['mode'].'" data-duration="'.$attr['duration'].'" data-pager="'.$attr['pager'].'" data-controls="'.$attr['controls'].'" data-auto="'.$attr['auto'].'" data-pause="'.$attr['pause'].'">';

				foreach( $slider_posts as $post ) :

 					$supports = as_get_meta( $post->ID );


					$o .= '<div class="as-slide-item '. (isset($supports['caption-style']) && !empty($supports['caption-style']) ? 'as-slide-' . $supports['caption-style'] : '') .'">';

					if( isset($supports['background']) && !empty($supports['background']) ) {
						$bg = wp_get_attachment_image( $supports['background'], apply_filters( 'animate_slider_bg_size', 'full' ) );
						$o .= '<div class="as-slide-bg">';
						if( isset( $supports['bg-link'] ) && $supports['bg-link'] ) {
							$o .= '<a href="'.$supports['link'].'" >';
						}
						$o .= $bg; 
						if( isset( $supports['bg-link'] ) && $supports['bg-link'] ) {
							$o .= '</a>';
						}
						$o .= '</div>';
					}
					
					$o .= '<div class="as-slide-caption '. (isset($supports['caption-position']) && !empty($supports['caption-position']) ? 'as-slide-' . $supports['caption-position'] : '') .'" data-position="'. (isset($supports['caption-position']) && !empty($supports['caption-position']) ? $supports['caption-position'] : '') .'">';
						
						$ti = $this->get_meta_value( $post->ID, 'caption-in-animation');
	            		$to = $this->get_meta_value( $post->ID, 'caption-out-animation');
	            		$tc = 'default';

	            		if( !empty( $ti ) || !empty( $to ) ) {
	            			$tc = 'animated';
	            		}
						$o .= '<h1 class="as-slide-title '.$tc.' " data-in-anim="'.$ti.'" data-out-anim="'.$to.'">'.$post->post_title.'</h1>';
						
						if($post->post_content != "") {

							$ci = $this->get_meta_value( $post->ID, 'content-in-animation');
		            		$co = $this->get_meta_value( $post->ID, 'content-out-animation');
		            		$cc = 'default';

		            		if( !empty( $ci ) || !empty( $co ) ) {
		            			$cc = 'animated';
		            		}

				            $o .= '<div class="as-slide-content '.$cc.' " data-in-anim="'.$ci.'" data-out-anim="'.$co.'">';
				            	$o .= '<div class="hide-for-medium hide-for-small">';
				            	$o .= wptexturize( wpautop( $post->post_content ) );
				            	$o .= '</div>';

				            	if(isset( $supports['link'] ) && !empty( $supports['link'] ) ) {
				            		$bi = $this->get_meta_value( $post->ID, 'button-in-animation');
				            		$bo = $this->get_meta_value( $post->ID, 'button-out-animation');
				            		$bc = 'default';

				            		if( !empty( $bi ) || !empty( $bo ) ) {
				            			$bc = 'animated';
				            		}

				            		$o .= '<a class="as-slide-more '. $bc . ' " data-in-anim="'.$bi.'" data-out-anim="'.$bo.'" href="'. esc_url( $supports['link'] ) .'" title="'.the_title_attribute( array( 'echo' => false ) ) . '">';
				            			$o .= ( isset( $supports['button'] )  && !empty( $supports['button'] ) ) ? $supports['button'] : __('Read More', 'as');
				            		$o .= '</a>';
				           		}
				               
				            $o .= '</div>';
						}

					$o .= '</div>'; // end slide-caption

					$thumb = get_the_post_thumbnail( $post->ID, apply_filters( 'animate_slider_thumb_size', 'post-thumbnail' ) );
					if( ( isset($supports['caption-position']) && $supports['caption-position'] != 'center' ) &&  !empty($thumb) )  {

					$ii = $this->get_meta_value( $post->ID, 'image-in-animation');
            		$io = $this->get_meta_value( $post->ID, 'image-out-animation');
            		$ic = 'default';

            		if( !empty( $ii ) || !empty( $io ) ) {
            			$ic = 'animated';
            		}

			        $o .= '<div class="hide-for-small as-slide-image as-slide-'.$supports['caption-position'].' '. $ic . ' " data-in-anim="'.$ii.'" data-out-anim="'.$io.'">';
			        $o .= $thumb;
			        $o .= '</div>';

			    	}
        
      
					$o .= '</div>';

				endforeach;

				$o .= '</div>';
				$o .= '<div id="as-slider-control-'.$instance.'" class="as-slider-control"></div>';
				$o .= '</div>';

			endif;
			
			return $o;
		}
		/**
		 * Just Debuging Value
		 *
		 * @since 0.1.0
		 * @return string
		 * 
		 */
		public function d($var) {
			echo "<pre>", print_r($var), "</pre>";  	
		}

		/**
		* Get Animation Options Lists.
		*
		* @since 0.1.4
		* @access public
		* @return string ( html option )
		*/
		public function get_anim_opt( $selected, $in_anim = true ) {

			$lists = $this->anim_lists( $in_anim );
			?>
			<option value="" <?php selected( $selected, '') ; ?>><?php _e('Default', 'as'); ?></option>


			<?php


			foreach( $lists as $key => $list ) {
				$label = ucwords(str_replace('_', ' ', $key));
				?>
					<optgroup label="<?php echo $label; ?>">

						<?php foreach( $list as $option ) { ?>

							<option value="<?php echo $option; ?>" <?php selected( $selected, $option ); ?>><?php echo $option; ?></option>
						<?php } ?>

					</optgroup>
				<?php
			}
		}

		/**
		* Get Animation array lists.
		*
		* @since 0.1.4
		* @param $in_anim boolean ( is out animation or in animation )
		* @access public
		* @return array
		*/
		public function anim_lists( $in_anim = true ) {

			$in = array(

				  "attention_seekers" => array(
				    "bounce",
				    "flash",
				    "pulse",
				    "shake",
				    "swing",
				    "tada",
				    "wobble"
				  ),

				  "bouncing_entrances" => array(
				    "bounceIn",
				    "bounceInDown",
				    "bounceInLeft",
				    "bounceInRight",
				    "bounceInUp"
				  ),

				  "fading_entrances" => array(
				    "fadeIn",
				    "fadeInDown",
				    "fadeInDownBig",
				    "fadeInLeft",
				    "fadeInLeftBig",
				    "fadeInRight",
				    "fadeInRightBig",
				    "fadeInUp",
				    "fadeInUpBig"
				  ),

				  "flippers" => array(
				    "flip",
				    "flipInX",
				    "flipInY",
				  ),

				  "lightspeed" => array(
				    "lightSpeedIn",
				  ),

				  "rotating_entrances" => array(
				    "rotateIn",
				    "rotateInDownLeft",
				    "rotateInDownRight",
				    "rotateInUpLeft",
				    "rotateInUpRight"
				  ),

				  "sliders" => array(
				    "slideInDown",
				    "slideInLeft",
				    "slideInRight",
				  ),

				  "specials" => array(
				    "hinge",
				    "rollIn",
				  )

				);

			$out = array(

				  "bouncing_exits" => array(
				    "bounceOut",
				    "bounceOutDown",
				    "bounceOutLeft",
				    "bounceOutRight",
				    "bounceOutUp"
				  ),

				  "fading_exits" => array(
				    "fadeOut",
				    "fadeOutDown",
				    "fadeOutDownBig",
				    "fadeOutLeft",
				    "fadeOutLeftBig",
				    "fadeOutRight",
				    "fadeOutRightBig",
				    "fadeOutUp",
				    "fadeOutUpBig"
				  ),

				  "flippers" => array(
				    "flip",
				    "flipOutX",
				    "flipOutY"
				  ),

				  "lightspeed" => array(
				    "lightSpeedOut"
				  ),

				  "rotating_exits" => array(
				    "rotateOut",
				    "rotateOutDownLeft",
				    "rotateOutDownRight",
				    "rotateOutUpLeft",
				    "rotateOutUpRight"
				  ),

				  "sliders" => array(
				    "slideOutLeft",
				    "slideOutRight",
				    "slideOutUp"
				  ),

				  "specials" => array(
				    "hinge",
				    "rollOut"
				  )

			);
			return $in_anim ? $in: $out; 
		}


		/**
		* Adds actions where needed for setting up the plugin's admin functionality.
		*
		* @since 0.1.4
		* @access public
		* @return void
		*/
		static function menu_settings() {

                $menu = array(
                        'page_title' => __( 'Animate Slider Settings', 'as' ),
                        'menu_title' => __( 'Animate Slider', 'as' ),
                        'capability' => 'manage_options',
                        'menu_slug' => 'animate-slider'
                );

                return apply_filters( 'animate_slider_admin_menu', $menu );
        }

		public function admin_page() {

				$menu = $this->menu_settings();
                $this->options_screen = add_options_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], array( $this, 'admin_setting_fields' ) );
		}

		
		/**
		* Registers the settings
		*
		* @since 0.1.4
		*/
	    public function settings_init() {
	        
	        /* Get the plugin settings. */
			$settings = get_option( $this->options_name, as_get_default_settings() );
			

	        add_settings_section(
                $this->options_screen . '-section',                        // ID used to identify this section and with which to register options
                __( 'General Options', 'sandbox' ),                // Title to be displayed on the administration page
                null,        // Callback used to render the description of the section
                $this->options_screen                // Page on which to add this section of options
	        );

	        add_settings_field(        
	                'as-transition-mode',                                                
	                __('Transition Mode','as'),                                                        
	                array( $this, 'render_setting_field_transition'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                     
	        );

	        add_settings_field(        
	                'as-slide-duration',                                                
	                __('Slide Transition Duration','as'),                                                        
	                array( $this, 'render_setting_field_duration'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                     
	        );

	        add_settings_field(        
	                'as-slide-pause',                                                
	                __('Transition Pause Time','as'),                                                        
	                array( $this, 'render_setting_field_pause'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                     
	        );

	        add_settings_field(        
	                'as-auto-start',                                                
	                __('Auto Start','as'),                                                        
	                array( $this, 'render_setting_field_auto_start'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                        
	        );

	        add_settings_field(        
	                'as-controls',                                                
	                __('Controls','as'),                                                        
	                array( $this, 'render_setting_field_controls'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                        
	        );

	        add_settings_field(        
	                'as-pager',                                                
	                __('Pager','as'),                                                        
	                array( $this, 'render_setting_field_pager'),        
	                $this->options_screen,        
	                $this->options_screen . '-section',
	                $settings                        
	        );


			/* Register settings for screen in the admin. */
	        register_setting(
	                $this->options_screen,
	                $this->options_name,
	                array( $this, 'validate_settings' )
	        );

		}

		public function render_setting_field_transition( $settings ) { ?>
			<select name="<?php echo $this->options_name; ?>[mode]">
				<option value="fade" <?php selected( $settings['mode'], 'fade' ); ?>><?php _e('Fade','as'); ?></option>
				<option value="slide" <?php selected( $settings['mode'], 'slide' ); ?>><?php _e('Slide','as'); ?></option>
			</select>
		<?php }

		public function render_setting_field_duration( $settings ) { ?>
			<input type="text" name="<?php echo $this->options_name; ?>[duration]" class="regular-text code" value="<?php echo esc_attr( $settings['duration'] ); ?>" />
		<?php }

		public function render_setting_field_pause( $settings ) { ?>
			<input type="text" name="<?php echo $this->options_name; ?>[pause]" class="regular-text code" value="<?php echo esc_attr( $settings['pause'] ); ?>" />
		<?php }

		public function render_setting_field_auto_start( $settings ) { ?>
			<label for="as-auto_start">
				<input type="checkbox" <?php checked( $settings['auto'] , true ); ?> value="1" id="as-auto_start" name="<?php echo $this->options_name; ?>[auto]">
				<?php _e('Enable Slide Auto Start','as'); ?></label>
		<?php }

		public function render_setting_field_controls( $settings ) { ?>
			<label for="as-controls">
				<input type="checkbox" <?php checked( $settings['controls'] , true ); ?> value="1" id="as-controls" name="<?php echo $this->options_name; ?>[controls]">
				<?php _e('Enable Slide Controls','as'); ?></label>
		<?php }

		public function render_setting_field_pager( $settings ) { ?>
			<label for="as-pager">
				<input type="checkbox" <?php checked( $settings['pager'] , true ); ?> value="1" id="as-pager" name="<?php echo $this->options_name; ?>[pager]">
				<?php _e('Enable Slide Pager','as'); ?></label>
		<?php }


		/**
		* Validate the settings
		* @return $settings
		* @since 0.1.4
		*/
		public function validate_settings( $settings ) {


			$settings['transition_mode'] = esc_attr( $settings['transition_mode'] );
			$settings['auto'] = isset( $settings['auto'] ) ? true : false;
			$settings['controls'] = isset( $settings['controls'] ) ? true : false;
			$settings['pager'] = isset( $settings['pager'] ) ? true : false;
			$settings['slide_duration'] = intval( $settings['slide_duration'] );

			return $settings;
		}

		/**
		* Output admin field html
		*
		* @since 0.1.4
		*/
		public function admin_setting_fields() { 

			$settings = get_option( $this->options_name );

			?>
			<div class="wrap">
				<h2><?php _e('Animate Slider Settings', 'as'); ?></h2>
				<form method="post" action="options.php">
					<?php 

						settings_fields( $this->options_screen );
                        do_settings_sections( $this->options_screen );

					 ?>
					<div id="submit-options">
						<?php echo submit_button(); ?>
					</div>
				</form>
			</div><!-- wrap -->
			<?php	
		}


	} // END class 

	$GLOBALS['animateslider'] = new Animate_Slider();
}
?>