<div class="wrap">
	<h2><?php _e('Floating Social Stats', 'wdsb');?></h2>
	<form action="index.php?page=wdsb-stats" method="post">
		
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="wdsb-post_type">
				<?php foreach ($post_types as $type => $label) { ?>
					<option value="<?php esc_attr_e($type); ?>" <?php echo selected($current_post_type, $type); ?>><?php echo $label; ?></option>
				<?php } ?>
				</select>
				<input type="submit" class="button" value="<?php esc_attr_e(__('Go', 'wdsb')); ?>" />
			</div>


			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo $total; ?> items</span>
				<span class="pagination-links">
					<a href="?page=wdsb-stats&amp;wdsb-post_type=<?php echo $current_post_type; ?>&amp;paged=1" title="<?php _e('Go to the first page'); ?>" class="first-page">«</a>
					<a href="?page=wdsb-stats&amp;wdsb-post_type=<?php echo $current_post_type; ?>&amp;paged=<?php echo ($current_page > 1 ? $current_page - 1 : 1); ?>" title="<?php _e('Go to the previous page'); ?>" class="prev-page">‹</a>
					<span class="paging-input">
						<input type="text" size="1" value="<?php echo $current_page; ?>" name="paged" title="<?php _e('Current page'); ?>" class="current-page"> 
						of 
						<span class="total-pages"><?php echo $last_page; ?></span>
					</span>
					<a href="?page=wdsb-stats&amp;wdsb-post_type=<?php echo $current_post_type; ?>&amp;paged=<?php echo ($current_page < $last_page ? $current_page + 1 : $last_page); ?>" title="<?php _e('Go to the next page'); ?>" class="next-page">›</a>
					<a href="?page=wdsb-stats&amp;wdsb-post_type=<?php echo $current_post_type; ?>&amp;paged=<?php echo $last_page; ?>" title="<?php _e('Go to the last page'); ?>" class="last-page">»</a>
				</span>
			</div>

			
		</div>
	</form>

<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<script type="text/javascript" src="<?php echo WDSB_PROTOCOL; ?>platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="<?php echo WDSB_PROTOCOL; ?>platform.linkedin.com/in.js"></script>
<!--
<script type="text/javascript" src="<?php echo WDSB_PROTOCOL; ?>static.bufferapp.com/js/button.js"></script>
<script type="text/javascript" src="<?php echo WDSB_PROTOCOL; ?>assets.pinterest.com/js/pinit.js"></script>
-->
	
	<table class="widefat" id="wdsb-stats">
		<thead>
			<tr>
			<?php foreach ($columns as $column) { ?>
				<th><?php echo $column; ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<?php foreach ($columns as $column) { ?>
				<th><?php echo $column; ?></th>
			<?php } ?>
			</tr>
		</tfoot>

		<tbody>
		<?php foreach ($posts as $post) { ?>
			<tr>
			<?php 
			$post_url = wdsb_get_permalink($post->ID);
			foreach ($columns as $service => $column) { 
				if ('_post_id' == $service) { // Post instance
					echo '<td><b><a href="' . admin_url('/edit.php?post=' . $post->ID . '&action=edit') . '">' . esc_html($post->post_title) . '</a></b></td>';
					continue;
				} else if ('_post_url' == $service) { // Post shared URL
					echo '<td><a href="' . $post_url . '">' . $post_url . '</a></td>';
					continue;
				}
				echo '<td>';
				switch ($service) {
					case "google":
						echo '<g:plusone size="tall" href="' . $post_url . '"></g:plusone>';
						break;
					case "facebook":
						echo '<iframe src="' . WDSB_PROTOCOL . 'www.facebook.com/plugins/like.php?href=' .
							rawurlencode($post_url) .
							'&amp;send=false&amp;layout=box_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" ' .
							'scrolling="no" frameborder="0" style="border:none; width:58px; height:62px;" allowTransparency="true"></iframe>';
						break;
					case "twitter":
						echo '<a href="' . WDSB_PROTOCOL . 'twitter.com/share" class="twitter-share-button" data-count="vertical" data-url="' . $post_url . '">Tweet</a>';
						break;
					case "linkedin":
						echo '<script type="IN/Share" data-counter="top" data-url="' . $post_url . '"></script>';
						break;
					case "pinterest":
						$atts = array('url=' => rawurlencode($post_url));
						
						$image = wdsb_get_image($post->ID);
						if ($image) $atts['media'] = 'media=' . rawurlencode($image);
						
						//$description = rawurlencode(wdsb_get_description($post_id));
						//if ($description) $atts['description'] = 'description=' . $description;

						//$show = apply_filters('wdsb-buttons-pinterest', !empty($image), $atts);
						//if ($show) {
							$atts = join('&', $atts); 
							echo '<a ' .
								'href="' . WDSB_PROTOCOL . 'pinterest.com/pin/create/button/?' . $atts . '" ' . 
								'class="pin-it-button" count-layout="vertical">Pin It</a>' .
							'';	
						//}
						break;
					case "buffer":
						//$post_id = is_singular() ? get_the_ID() : false;
						$atts = array();

						//$url = wdsb_get_url($post_id);
						//$url = wdsb_get_permalink($post_id);
						$atts['data-url'] = 'data-url="' . $post_url . '"';
						$atts['data-url'] = 'data-url="http://bufferapp.com/extras/button"';
						
						//$image = wdsb_get_image($post_id);
						//if ($image) $atts['data-picture'] = 'data-picture="' . $image . '"';
						
						//$description = wdsb_get_description($post_id);
						//if ($description) $atts['data-text'] = 'data-text="' . $description . '"';

						//$atts = apply_filters('wdsb-buttons-buffer-render_attributes', $atts);
						$atts = join(" ", $atts);

						echo '<a href="' . WDSB_PROTOCOL . 'bufferapp.com/add" class="buffer-add-button" ' . $atts . ' data-count="vertical" >Buffer</a>';
						break;
				}
				echo '</td>';
			}
			?>
			</tr>
		<?php } ?>
		</tbody>
</div>