<?php
/**
 * Handles all Admin access functionality.
 */
class Wdsb_AdminPages {

	var $data;

	//function Wdsb_AdminPages () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdsb_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	public static function serve () {
		$me = new Wdsb_AdminPages;
		$me->add_hooks();
	}

	function create_site_admin_menu_entry () {
		$perms = is_network_admin() ? 'manage_network_options' : 'manage_options';
		if (current_user_can($perms) && !empty($_POST) && isset($_POST['option_page']) && 'wdsb' == $_POST['option_page']) {
			if (isset($_POST['wdsb'])) {
				$services = !empty($_POST['wdsb']['services']) ? $_POST['wdsb']['services'] : array();
				$services = is_array($services) ? $services : array();
				if (!empty($_POST['wdsb']['new_service']['name']) && !empty($_POST['wdsb']['new_service']['code'])) {
					$services[] = $_POST['wdsb']['new_service'];
					unset($_POST['wdsb']['new_service']);
				}
				foreach ($services as $key=>$service) {
					if (!empty($service['code'])) {
						$services[$key]['code'] = stripslashes($service['code']);
					}
				}
				$_POST['wdsb']['services'] = $services;
				$this->data->set_options($_POST['wdsb']);
			}
			$goback = add_query_arg('settings-updated', 'true',  wp_get_referer());
			wp_redirect($goback);
			die;
		}
		$page = is_network_admin() ? 'settings.php' : 'options-general.php';
		add_submenu_page($page, __('Floating Social', 'wdsb'), __('Floating Social', 'wdsb'), $perms, 'wdsb', array($this, 'create_admin_page'));

		//if (!is_network_admin()) add_dashboard_page(__('Social Stats', 'wdsb'), __('Social Stats', 'wdsb'), $perms, 'wdsb-stats', array($this, 'create_stats_page'));
	}

	function register_settings () {
		$form = new Wdsb_AdminFormRenderer;

		register_setting('wdsb', 'wdsb');
		add_settings_section('wdsb_settings', __('Floating social settings', 'wdsb'), create_function('', ''), 'wdsb_options_page');
		add_settings_field('wdsb_services', __('Services', 'wdsb'), array($form, 'create_services_box'), 'wdsb_options_page', 'wdsb_settings');
		add_settings_field('wdsb_custom_service', __('Add new Custom Service', 'wdsb'), array($form, 'create_custom_service_box'), 'wdsb_options_page', 'wdsb_settings');
		add_settings_field('wdsb_appearance', __('Appearance', 'wdsb'), array($form, 'create_appearance_box'), 'wdsb_options_page', 'wdsb_settings');
		add_settings_field('wdsb_min_width', __('Minimum dimensions', 'wdsb'), array($form, 'create_min_dimensions_box'), 'wdsb_options_page', 'wdsb_settings');
		add_settings_field('wdsb_top_offset', __('Top offset', 'wdsb'), array($form, 'create_top_offset_box'), 'wdsb_options_page', 'wdsb_settings');
		add_settings_field('wdsb_horizontal_offset', __('Horizontal offset', 'wdsb'), array($form, 'create_horizontal_offset_box'), 'wdsb_options_page', 'wdsb_settings');

		add_settings_section('wdsb_advanced', __('Advanced settings', 'wdsb'), create_function('', ''), 'wdsb_options_page');
		add_settings_field('wdsb_shortlinks_box', __('Shortlinks', 'wdsb'), array($form, 'create_shortlinks_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_advanced_box', __('Advanced', 'wdsb'), array($form, 'create_advanced_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_vertical_limits', __('Vertical limits', 'wdsb'), array($form, 'create_vertical_limits_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_css', __('Additional CSS', 'wdsb'), array($form, 'create_css_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_display_box', __('Display', 'wdsb'), array($form, 'create_display_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_front_footer_box', __('Attempt to fix front page conflicts', 'wdsb'), array($form, 'create_front_footer_box'), 'wdsb_options_page', 'wdsb_advanced');
		add_settings_field('wdsb_manual_box', __('Manual box placement', 'wdsb'), array($form, 'create_manual_box'), 'wdsb_options_page', 'wdsb_advanced');
	}

	function create_admin_page () {
		include(WDSB_PLUGIN_BASE_DIR . '/lib/forms/plugin_settings.php');
	}

	function create_stats_page () {
		return false;
		$stats = array();
		$columns = array (
			'_post_id' => __('Post', 'wdsb'),
			'_post_url' => __('URL', 'wdsb'),
			'google' => 'Google +1',
			'facebook' => 'Facebook',
			'twitter' => 'Twitter',
			'linkedin' => 'LinkedIn',
			//'pinterest' => 'Pinterest',
			//'buffer' => 'Buffer',
		);
		$page_size = 10;
		
		$current_page = !empty($_REQUEST['paged']) ? (int)$_REQUEST['paged'] : 1;
		$current_post_type = !empty($_REQUEST['wdsb-post_type']) ? $_REQUEST['wdsb-post_type'] : 'post';
		$query = new WP_Query(array(
			'post_type' => $current_post_type,
			'posts_per_page' => $page_size,
			'paged' => $current_page,
		));
		$posts = $query->posts;

		$total_query = new WP_Query(array(
			'post_type' => $current_post_type,
			'posts_per_page' => -1,
		));
		$total = count($total_query->posts);
		$last_page = intval($total / $page_size) + 1;

		$raw_types = get_post_types(array('public'=>true), 'objects');
		$post_types = array();
		foreach ($raw_types as $type) {
			if ('attachment' == $type->name) continue;
			$post_types[$type->name] = $type->label;
		}

		wp_enqueue_script('wdsb-admin-stats', WDSB_PLUGIN_URL . '/js/wdsb-admin-stats.js', array('jquery'));
		include(WDSB_PLUGIN_BASE_DIR . '/lib/forms/plugin_stats.php');
	}

	function css_print_styles () {
		if (!isset($_GET['page']) || 'wdsb' != $_GET['page']) return false;
		wp_enqueue_style('wdsb-admin', WDSB_PLUGIN_URL . "/css/wdsb-admin.css");
	}

	function js_print_scripts () {
		if (!isset($_GET['page']) || 'wdsb' != $_GET['page']) return false;
		wp_enqueue_script( array("jquery", "jquery-ui-core", "jquery-ui-sortable", 'jquery-ui-dialog') );
	}

	function add_meta_box () {
		$types = get_post_types(array('public'=>true));
		$types = is_array($types) ? $types : array();
		foreach ($types as $type) {
			add_meta_box(
				'wdsb_show_box',
				__('Floating Social', 'wdsb'),
				array($this, 'render_meta_box'),
				$type,
				'side',
				'low'
			);
		}
	}

	function render_meta_box () {
		global $post;
		$prevent_items = $this->data->get_option('prevent_items');
		$prevent_items = is_array($prevent_items) ? $prevent_items : array();
		$checked = in_array($post->ID, $prevent_items) ? 'checked="checked"' : '';

		echo "" .
			"<input type='checkbox' name='wdsb_hide_box' id='wdsb-hide-box' value='1' {$checked} />" .
			'&nbsp;' .
			'<label for="wdsb-hide-box">' . __('Hide Floating Social', 'wdsb') . '</label>' .
		"";
	}

	function save_meta () {
		global $post;
		$opts = get_option('wdsb');
		$opts = $opts ? $opts : array();
		$opts['prevent_items'] = @$opts['prevent_items'] ? $opts['prevent_items'] : array();

		if (@$_POST['wdsb_hide_box']) {
			$opts['prevent_items'][] = $post->ID;
		} else {
			$key = array_search($post->ID, $opts['prevent_items']);
			if (false !== $key) unset($opts['prevent_items'][$key]);
		}
		$opts['prevent_items'] = array_unique($opts['prevent_items']);
		update_option('wdsb', $opts);
	}

	function json_wdsb_list_entries () {
		$query = new WP_Query;
		$all = $query->query(array(
			'post_type' => $_POST['type'],
			'posts_per_page' => 50,
		));

		$prevent_items = $this->data->get_option('prevent_items');
		$prevent_items = is_array($prevent_items) ? $prevent_items : array();

		$entries = array();
		foreach ($all as $entry) {
			$entries[] = array(
				"id" => $entry->ID,
				"title" => $entry->post_title,
				"checked" => (int)in_array($entry->ID, $prevent_items),
			);
		}
		header('Content-type: application/json');
		echo json_encode(array(
			'entries' => $entries
		));
		exit();
	}

	function add_hooks () {
		// Step0: Register options and menu
		add_action('admin_init', array($this, 'register_settings'));
		add_action('network_admin_menu', array($this, 'create_site_admin_menu_entry'));
		add_action('admin_menu', array($this, 'create_site_admin_menu_entry'));

		if ($this->data->get_option('show_metabox')) {
			add_action('admin_init', array($this, 'add_meta_box'));
			add_action('save_post', array($this, 'save_meta'));
		}

		add_action('admin_print_scripts', array($this, 'js_print_scripts'));
		add_action('admin_print_styles', array($this, 'css_print_styles'));

		add_action('wp_ajax_wdsb_list_entries', array($this, 'json_wdsb_list_entries'));
	}
}