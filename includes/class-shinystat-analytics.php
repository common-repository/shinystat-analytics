<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/includes
 * @author     ShinyStat <support@shinystat.com>
 */
class Shinystat_Analytics {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Shinystat_Analytics_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SHINYSTAT_ANALYTICS_VERSION' ) ) {
			$this->version = SHINYSTAT_ANALYTICS_VERSION;
		} else {
			$this->version = '1.0.15';
		}
		$this->plugin_name = 'shinystat-analytics';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_widget();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Shinystat_Analytics_Loader. Orchestrates the hooks of the plugin.
	 * - Shinystat_Analytics_i18n. Defines internationalization functionality.
	 * - Shinystat_Analytics_Admin. Defines all hooks for the admin area.
	 * - Shinystat_Analytics_Public. Defines all hooks for the public side of the site.
	 * - Shinystat_Analytics_Widget. Defines all hooks for the widget area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shinystat-analytics-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shinystat-analytics-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shinystat-analytics-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shinystat-analytics-public.php';

		$this->loader = new Shinystat_Analytics_Loader();

		/**
		 * The class responsible for widget functionalities
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shinystat-analytics-widget.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Shinystat_Analytics_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Shinystat_Analytics_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Shinystat_Analytics_Admin( $this->get_plugin_name(), $this->get_version() );

		//add css file for the style of the settings admin page
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		//add menu option for settings
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		//add link for setting in plugin page
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_link' , 10, 2);

		//add settings input
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Shinystat_Analytics_Public( $this->get_plugin_name(), $this->get_version() );

		//load getcod external script
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'load_getcod' );	

		//set async attribute to getcod
		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'shinystat_analytics_getcod_async', 10, 2);

		//inside the thankyou page add the conversion call with order parameters
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'woocommerce_thankyou_send_conversion'); 

		//add the script to populate the shn_engage structure
		$this->loader->add_action( 'wp_head', $plugin_public, 'woocommerce_cart_content' );
 
		//add custom endpoint to retrieve cart/product information
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'register_shinystat_rest_route' );

		//capture call adding product to cart and update the shn_engage structure inside the page navigation
		$this->loader->add_filter( 'woocommerce_add_to_cart_fragments', $plugin_public, 'woocommerce_add_to_cart_fragments', 10, 1);	
		
		//add support to amp (by using the plugin https://it.wordpress.org/plugins/amp/)
		$this->loader->add_filter( 'amp_post_template_analytics', $plugin_public, 'amp_add_shinystat_analytics' );
		$this->loader->add_filter( 'amp_analytics_entries', $plugin_public, 'amp_add_shinystat_analytics_entry' );
		
	}

	/**
	 * Register all of the hooks related to the widgets admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_widget() {

		$plugin_widget = new Shinystat_Analytics_Widget();
		$plugin_widget->set_plugin_name($this->get_plugin_name());
		$plugin_widget->set_plugin_version($this->get_version());

		//init shinystat widget
		$this->loader->add_action('widgets_init', $plugin_widget, 'shinystat_analytics_widget_init');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Shinystat_Analytics_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
