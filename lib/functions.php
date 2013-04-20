<?php

/**
 * Attempt to find featured image.
 * If this fails, return filter hook output.
 */
function wdsb_get_image ($post_id=false, $size='medium') {
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-image', '', $size
	);
	
	// Try to find featured image
	$thumb_id = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($post_id) : false;
	if ($thumb_id) {
		$image = wp_get_attachment_image_src($thumb_id, $size);
		if ($image) return apply_filters(
			'wdsb-media-image',
			apply_filters('wdsb-media-image-featured_image', $image[0], $size), $size
		);
	}
	
	// Aw shucks, we're still here.
	return apply_filters(
		'wdsb-media-image', '', $size
	);
}

/**
 * Attempt to create link description.
 */
function wdsb_get_description ($post_id=false) {
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-title', get_bloginfo('name')
	);
	
	return apply_filters(
		'wdsb-media-title', 
		apply_filters('wdsb-media-title-post_title', get_the_title($post_id))
	);
}

/**
 * Attempt to get fully qualified URL.
 */
function wdsb_get_url ($post_id=false) {
	$url = (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-url', $url
	);
	
	return apply_filters(
		'wdsb-media-url',
		apply_filters('wdsb-media-url-post_url', get_permalink($post_id))
	);
}

/**
 * Permalink wrapper for shortlink processing.
 */
function wdsb_get_permalink ($post_id=null, $skip_shortlink=false) {
	if ($skip_shortlink) return apply_filters('wdsb-permalink-url', wdsb_get_url($post_id), $post_id);
	
	global $wp;
	$data = new Wdsb_Options;
	$use_shortlink_service = $data->get_option('shortlink');
	$url = false;

	if (!$use_shortlink_service) return apply_filters('wdsb-permalink-url', wdsb_get_url($post_id), $post_id);

	// From here on, we're dealing with shortlinks

	// Get default
	$url = wp_get_shortlink($post_id, 'query');
	$url = $url ? $url : site_url($wp->request);
	
	if ("is.gd" == $use_shortlink_service) {
		// First off, check cache - do *not* consult remote service unless we have to
		$cache = get_option('_wdsb_shortlinks-is.gd');
		$cache = $cache ? $cache : array();
		$url_key = md5($url);

		if ($cache && array_key_exists($url_key, $cache)) $url = $cache[$url_key];
		else {
			// No cache, consult is.gd and see what do they say
			$service = sprintf('http://is.gd/create.php?format=simple&url=%s', urlencode($url));
			$page = wp_remote_request($service, array(
				"method" => "GET",
				"timeout" => 5,
				"redirection" => 5,
				"user-agent" => "wdsb",
				"sslverify" => false,
			));
			if (!is_wp_error($page) && wp_remote_retrieve_response_code($page) == 200) {
				$short = wp_remote_retrieve_body($page);
				if (!preg_match('/error/i', $short)) { // Make double-sure we have a proper URL here
					$url = $cache[$url_key] = $short;
					update_option('_wdsb_shortlinks-is.gd', $cache);
				} // Done caching and post-processing
			} // Done processing successful request
		}

	}

	return apply_filters('wdsb-permalink-url', $url, $post_id);
}
