<?php

function as_get_prefix() {
	global $animateslider;

	if($animateslider) {
		return $animateslider->prefix;
	} else {
		return 'as';
	}

}
function as_get_meta( $post_id = '' ) {

	global $animateslider;

	if( empty($post_id) ) { return; }

	$supports = $animateslider->supports;
	$meta = array();
	$prefix = as_get_prefix();


	foreach( $supports as $support ) {

		if( $support == 'link') {
			$meta[$support] =  esc_url( get_post_meta( $post_id, "{$prefix}-{$support}", true ) );
		} else {
			$meta[$support] =  esc_attr( get_post_meta( $post_id, "{$prefix}-{$support}", true ) );
		}
	}

	if( in_array( 'link', $supports ) && in_array( 'background', $supports ) )
		$meta['bg-link'] = esc_attr( get_post_meta( $post_id, "{$prefix}-bg-link", true ) );

	return $meta;
}

function as_get_default_settings() {

	$settings = array(
		'mode' => 'fade',
		'duration'  => 1100,
		'pause'	=> 4000,
		'pager' => false,
		'controls' => true,
		'auto' => false,
	);

	return $settings;
}