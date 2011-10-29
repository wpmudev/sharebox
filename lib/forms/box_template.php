<?php
	$url = (is_home() || is_front_page()) ? site_url() : get_permalink();
?>
<?php if ($css) { ?>
<style type="text/css">
	<?php echo $css; ?>
</style>
<?php } ?>

<div id="wdsb-share-box" style="<?php echo $style;?>">
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
							echo '<iframe src="http://www.facebook.com/plugins/like.php?href=' .
								rawurlencode($url) .
								'&amp;send=false&amp;layout=box_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=60" ' .
								'scrolling="no" frameborder="0" style="border:none; width:58px; height:62px;" allowTransparency="true"></iframe>';
							break;
						case "twitter":
							if (!in_array('twitter', $skip_script)) echo '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
							echo '<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a>';
							break;
						case "stumble_upon":
							echo '<script src="http://www.stumbleupon.com/hostedbadge.php?s=5"></script>';
							break;
						case "delicious":
							echo '<a href="http://www.delicious.com/save" onclick="window.open(' .
								"'http://www.delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title), 'delicious','toolbar=no,width=550,height=550'); return false;".
								'">' .
									'<img src="http://l.yimg.com/hr/15213726/img/delicious.48px.gif" alt="Delicious" />' .
								'</a>';
							break;
						case "reddit":
							echo '<script type="text/javascript" src="http://www.reddit.com/static/button/button2.js"></script>';
							break;
						case "linkedin":
							if (!in_array('linkedin', $skip_script)) echo '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>';
							echo '<script type="IN/Share" data-counter="top"></script>';
							break;
					}
				}
				?>
			</div>
		</li>
	<?php } ?>
	</ul>
</div>