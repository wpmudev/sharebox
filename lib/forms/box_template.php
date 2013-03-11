<?php
	global $wp;
	$url = (is_home() || is_front_page()) 
		? site_url() 
		: wdsb_get_permalink()//($use_shortlink_service ? get_permalink()
	;
	$url = apply_filters('wdsb-url-current_url', ($url ? $url : site_url($wp->request))); // Fix for empty URLs
?>
<?php if ($css) { ?>
<style type="text/css">
	<?php echo $css; ?>
</style>
<?php } ?>

<div id="wdsb-share-box" style="<?php echo $style;?>">
	<?php if (!empty($message)) { ?>
		<div class="wdsb-text_message" style="display:none"><?php echo $message; ?></div>
	<?php } ?>
	<ul>
	<?php foreach ($services as $key=>$service) { ?>
		<li>
			<?php $idx = is_array($service) ? strtolower(preg_replace('/[^-a-zA-Z0-9_]/', '', $service['name'])) : $key;?>
			<div class="wdsb-item" id="wdsb-service-<?php echo $idx;?>">
				<?php if (is_array($service)) {
					echo $service['code'];
				} else {
					switch ($key) {
						case "google":
							if (!in_array('google', $skip_script)) echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
							echo '<g:plusone size="tall"></g:plusone>';
							break;
						case "facebook":
							$width = !empty($custom_widths['facebook']) ? (int)$custom_widths['facebook'] : 48;
							echo '<iframe src="' . WDSB_PROTOCOL . 'www.facebook.com/plugins/like.php?href=' .
								rawurlencode($url) .
								'&amp;send=false&amp;layout=box_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" ' .
								'scrolling="no" frameborder="0" style="border:none; width:' . $width . 'px; height:61px;" allowTransparency="true"></iframe>';
							break;
						case "twitter":
							if (!in_array('twitter', $skip_script)) echo '<script type="text/javascript" src="' . WDSB_PROTOCOL . 'platform.twitter.com/widgets.js"></script>';
							$url = wdsb_get_permalink(get_the_ID());
							$size = !empty($custom_widths['twitter']) 
								? 'data-size="' . (int)$custom_widths['twitter'] . '"'
								: ''
							;
							echo '<a href="' . WDSB_PROTOCOL . 'twitter.com/share" class="twitter-share-button" data-count="vertical" data-url="' . $url . '" ' . $size . '>Tweet</a>';
							break;
						case "stumble_upon":
							echo '<script src="' . WDSB_PROTOCOL . 'www.stumbleupon.com/hostedbadge.php?s=5"></script>';
							break;
						case "delicious":
							$url = $use_shortlink_service ? "'" . wdsb_get_permalink(get_the_ID()) . "'" : 'location.href';
							echo '<a href="' . WDSB_PROTOCOL . 'www.delicious.com/save" onclick="window.open(' .
								"'" . WDSB_PROTOCOL . "www.delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url='+encodeURIComponent({$url})+'&amp;title='+encodeURIComponent(document.title), 'delicious','toolbar=no,width=550,height=550'); return false;".
								'">' .
									'<img src="' . WDSB_PLUGIN_URL . '/img/delicious.48px.gif" alt="Delicious" />' .
								'</a>';
							break;
						case "reddit":
							echo '<script type="text/javascript" src="' . WDSB_PROTOCOL . 'www.reddit.com/static/button/button2.js"></script>';
							break;
						case "linkedin":
							if (!in_array('linkedin', $skip_script)) echo '<script src="' . WDSB_PROTOCOL . 'platform.linkedin.com/in.js" type="text/javascript"></script>';
							echo '<script type="IN/Share" data-counter="top"></script>';
							break;
						case "post_voting":
							if (function_exists('wdpv_get_vote_up_ms') && is_singular()) {
								global $blog_id;
								$post_id = get_the_ID();
								if ($post_id) {
									echo wdpv_get_vote_up_ms(false, $blog_id, $post_id);
									echo wdpv_get_vote_result_ms(true, $blog_id, $post_id);
								}
							}
							break;
						case "pinterest":
							$post_id = is_singular() ? get_the_ID() : false;
							$atts = array();
							
							//$url = wdsb_get_url($post_id);
							$url = wdsb_get_permalink($post_id);
							if ($url) $atts['url'] = 'url=' . rawurlencode($url);
							
							$image = wdsb_get_image($post_id);
							if ($image) $atts['media'] = 'media=' . rawurlencode($image);
							
							$description = rawurlencode(wdsb_get_description($post_id));
							if ($description) $atts['description'] = 'description=' . $description;

							$show = apply_filters('wdsb-buttons-pinterest', !empty($image), $atts);
							if ($show) {
								$atts = join('&', $atts); 
								echo '<a ' .
									'href="' . WDSB_PROTOCOL . 'pinterest.com/pin/create/button/?' . $atts . '" ' . 
									'class="pin-it-button" count-layout="vertical" data-pin-config="above" data-pin-do="buttonPin"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>' .
								'';	
								if (!in_array('pinterest', $skip_script)) echo '<script type="text/javascript" src="' . WDSB_PROTOCOL . 'assets.pinterest.com/js/pinit.js"></script>';
							}
							break;
						case "pin_any":
							echo '' .
								'<a data-pin-config="above" href="//pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" ><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>' .
							'';
							if (!in_array('pin_any', $skip_script)) echo '<script type="text/javascript" src="' . WDSB_PROTOCOL . 'assets.pinterest.com/js/pinit.js"></script>';
							break;
						case "buffer":
							$post_id = is_singular() ? get_the_ID() : false;
							$atts = array();

							//$url = wdsb_get_url($post_id);
							$url = wdsb_get_permalink($post_id);
							$atts['data-url'] = 'data-url="' . $url . '"';
							
							$image = wdsb_get_image($post_id);
							if ($image) $atts['data-picture'] = 'data-picture="' . $image . '"';
							
							$description = wdsb_get_description($post_id);
							if ($description) $atts['data-text'] = 'data-text="' . $description . '"';

							$atts = apply_filters('wdsb-buttons-buffer-render_attributes', $atts);
							$atts = join(" ", $atts);

							echo '<a href="' . WDSB_PROTOCOL . 'bufferapp.com/add" class="buffer-add-button" ' . $atts . ' data-count="vertical" >Buffer</a>';
							if (!in_array('buffer', $skip_script)) echo '<script type="text/javascript" src="' . WDSB_PROTOCOL . 'static.bufferapp.com/js/button.js"></script>';
							break;
					}
				}
				?>
			</div>
		</li>
	<?php } ?>
	</ul>
</div>