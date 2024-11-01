<?php

/**
 * The widget functionality of the plugin.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/widget
 */

/**
 * The widget functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the widget stylesheet and JavaScript.
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/widget
 * @author     ShinyStat <support@shinystat.com>
 */

 class Shinystat_Analytics_Widget extends WP_Widget{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_prefix = 'shinystat_analytics';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$account_type = get_option( $this->option_prefix . '_account_type' );

		$params = array(
			'description' => __('Insert the ShinyStat visitors counter icon', 'shinystat-analytics'), 
			'name' => 'ShinyStat Analytics',
			'classname' => 'shinystat_analytics',
		);

		parent::__construct('shinystat_analytics_widget', '', $params);
	
	}
	
	/**
	 * Set plugin_name class variable plugin_name
	 * 
	 * @since    1.0.1
	 */
	public function set_plugin_name($plugin_name) {

		$this->plugin_name = $plugin_name;
	
	}

	/**
	 * Set plugin_name class variable version
	 * 
	 * @since    1.0.1
	 */
	public function set_plugin_version($plugin_version) {
	
		$this->version = $plugin_version;
	
	}

	/**
	 * Register the component
	 * 
	 * @since    1.0.0
	 */
	function shinystat_analytics_widget_init() {

		register_widget('Shinystat_Analytics_Widget');

	}

	/**
	 * Outputs the script call that gets the content of the widget
	 * 
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance){
		
		$account_type = get_option( $this->option_prefix . '_account_type' );


		?>
			<div class="widget widget_shinystat_analytics">
			<center>
				<a id="shinystat_img_container" href="https://www.shinystat.com/it/vedistat.html" target="_blank"></a>
			</center>
			</div>
		
			<!-- Insert image element ig_ inside the widget div -->
			<script type="text/javascript">

				(function () {
					var a = document.getElementById("shinystat_img_container");

					var waitImg = function(key, attempts, timeout, callback) {
						if (!!window[key]) {
							callback();
						} else {
							if (attempts > 0 ) {
								setTimeout(function() {
									waitImg(key, attempts-1, timeout, callback);
								}, timeout);
							}
						} 
					};
 
					waitImg("ig_", 30, 200, function() {
						a.appendChild(ig_);
						ig_.removeAttribute("width");
						ig_.removeAttribute("height");
					});
					
				})();

				
			</script>
		
		<?php

	}

}
