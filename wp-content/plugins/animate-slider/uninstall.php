<?php
/**
 * Uninstall procedure for the plugin.
 *
 * @package    Animate Slider
 * @since      0.1.0
 * @author     Hermanto Lim <hermanto@bonfirelab.com>
 * @copyright  Copyright (c) 2013, Hermanto Lim
 * @link       http://bonfirelab.com/plugins/animate-slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Make sure we're actually uninstalling the plugin. */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	wp_die( sprintf( __( '%s should only be called when uninstalling the plugin.', 'custom-content-portfolio' ), '<code>' . __FILE__ . '</code>' ) );

/* === Delete plugin posts. === */

$args = array (
	'post_type' => 'slider',
	'nopaging' => true
);
	$query = new WP_Query ($args);
		while ($query->have_posts ()) {
			$query->the_post ();
			$id = get_the_ID ();
			wp_delete_post ($id, true);
		}
	wp_reset_postdata ();



?>