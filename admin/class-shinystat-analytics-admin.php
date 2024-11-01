<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for 
 * the admin-specific stylesheet and JavaScript.
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/admin
 * @author     ShinyStat <support@shinystat.com>
 */

class Shinystat_Analytics_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string 		$option_name 	Option name of this plugin
	 */
	private $option_prefix = 'shinystat_analytics';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shinystat-analytics-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Add settings link to plugin actions
	 *
	 * @param  array  $plugin_actions
	 * @param  string $plugin_file
	 * @since  1.0
	 * @return array
	 */
	function add_plugin_link( $plugin_actions, $plugin_file ) {

		$custom_actions = array();

		if ($plugin_file === "shinystat-analytics/shinystat-analytics.php") {

			$custom_actions = array(
				'settings' => sprintf( 
					'<a href="%s">%s</a>', 
					admin_url( 'admin.php?page=shinystat-analytics' ), 
					__( 'Settings', 'shinystat-analytics' ) 
				)
			);
		}
			
		return array_merge( $custom_actions, $plugin_actions );
	}

	/**
	 * Add an options page under the Settings menu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
	
		add_options_page(
			__( 'ShinyStat Settings', 'shinystat-analytics' ),
			__( 'ShinyStat', 'shinystat-analytics' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {

		include_once 'partials/shinystat-analytics-admin-display.php';

	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting() {

		// Add a General section
		add_settings_section(
			$this->option_prefix . '_general',
			__( 'ShinyStat Account', 'shinystat-analytics' ),
			array( $this, $this->option_prefix . '_general_cb' ),
			$this->plugin_name
		);

		// Add input for Account Name
		add_settings_field(
			$this->option_prefix . '_account_name',
			__( 'Account Identifier', 'shinystat-analytics' ),
			array( $this, $this->option_prefix . '_account_name' ),
			$this->plugin_name,
			$this->option_prefix . '_general',
			array( 'label_for' => $this->option_prefix . '_account_name' )
		);
		register_setting( $this->plugin_name, $this->option_prefix . '_account_name');

		// Add radio buttons for Account Type
		add_settings_field(
			$this->option_prefix . '_account_type',
			__( 'Account Type', 'shinystat-analytics' ),
			array( $this, $this->option_prefix . '_account_type' ),
			$this->plugin_name,
			$this->option_prefix . '_general',
			array( 'label_for' => $this->option_prefix . '_account_type' )
		);
		register_setting( $this->plugin_name, $this->option_prefix . '_account_type');
		
		//if woocommerce is active (version >=3.3.0) add input for Conversion Name
		if ( preg_grep('/\/woocommerce.php$/', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if ( version_compare( WC_VERSION, '3.3.0', '>=' ) ) {
				add_settings_field(
					$this->option_prefix . '_conv_name',
					__( 'Conversion Name', 'shinystat-analytics' ),
					array( $this, $this->option_prefix . '_conv_name' ),
					$this->plugin_name,
					$this->option_prefix . '_general',
					array( 'label_for' => $this->option_prefix . '_conv_name' )
				);
			}
		}
		register_setting( $this->plugin_name, $this->option_prefix . '_conv_name');
		
		// Add advanced options
		add_settings_field(
			$this->option_prefix . '_advanced_options',
			__( 'Advanced Options', 'shinystat-analytics' ),
			array( $this, $this->option_prefix . '_advanced_options' ),
			$this->plugin_name,
			$this->option_prefix . '_general',
			array( 'label_for' => $this->option_prefix . '_advanced_options' )
		);
		register_setting( $this->plugin_name, $this->option_prefix . '_advanced_options_add_param_name');
		register_setting( $this->plugin_name, $this->option_prefix . '_advanced_options_add_param_value');
	}

	/**
	 * Render the widget warning section (if widget has to be 
	 * added to make analytics works, i.e. free accounts)
	 *
	 * @since  1.0.0
	 */
	public function show_warning() {

		$account_type = get_option( $this->option_prefix . '_account_type' );

		if ($account_type == "free")
			if ( ! is_active_widget(false, false, 'shinystat_analytics_widget') )
				return true;

		return false;
	}

	/**
	 * Render the widget info section (optional widget for business accounts)
	 *
	 * @since  1.0.2
	 */
	public function show_info() {

		$account_type = get_option( $this->option_prefix . '_account_type' );

		if ($account_type == "business")
			if ( ! is_active_widget(false, false, 'shinystat_analytics_widget') )
				return true;

		return false;
	}

	/**
	 * Render the widget success message (if options are successfully saved)
	 *
	 * @since  1.0.0
	 */
	public function show_success() {

		return isset($_GET['settings-updated']);
	}

	/**
	 * Render the text introduction for the general section
	 *
	 * @since  1.0.0
	 */
	public function shinystat_analytics_general_cb() {
		?>
			<div class="shinystat-analytics-panel">
				<?php echo __( 
					'Subscribe an account to access ShinyStat services',
					'shinystat-analytics' 
					); 
				?>: 
				<ul>
					<li> 
						<span class="shinystat-analytics-checkmark">&check;</span> 
						<?php echo __("Web Analytics", 'shinystat-analytics'); ?> 
					</li>
					<li> 
						<span class="shinystat-analytics-checkmark">&check;</span> 
						<?php echo __("On-site Marketing Automation", 'shinystat-analytics'); ?> 
					</li>
				</ul>
				<a class="shinystat-analytics-redirect" target="_blank" 
					href="<?php echo __('https://www.shinystat.com/cgi-bin/shinystatn.cgi?MODE=ADD1&ABBO=0&LANG=1', 'shinystat-analytics'); ?>">
					<button type="button" class="button button-primary">
					<?php echo __("Subscribe an account", 'shinystat-analytics'); ?>
					</button>
				</a>
			</div>
		<?php
	}


	/**
	 * Render the account name input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function shinystat_analytics_account_name() {

		$account_name = get_option( $this->option_prefix . '_account_name' );

		?>

			<fieldset>
				<input 
					type="text" 
					name="<?php echo $this->option_prefix . '_account_name' ?>"
					id="<?php echo $this->option_prefix . '_account_name' ?>"
					value="<?php echo $account_name ?>"
					required
				>
				<div class="help-tip">
    				<p><?php echo __("Insert the identifier of the ShinyStat account. It appears in the upper left corner when you log into the analytics measurement dashboards.", 'shinystat-analytics' ) ?></p>
				</div>
			</fieldset>

		<?php
	}

	/**
	 * Render the radio input field for account_type option
	 *
	 * @since  1.0.0
	 */
	public function shinystat_analytics_account_type() {

		$account_type = get_option( $this->option_prefix . '_account_type' );

		?>

		<fieldset>
			<select 
				id="<?php echo $this->option_prefix . '_account_type' ?>"
				name="<?php echo $this->option_prefix . '_account_type' ?>" >
				<label>
					<option 
						id="<?php echo $this->option_prefix . '_account_type_free' ?>" 
						onclick="enable_disable_conv_name()"
						value="free" 
						<?php  echo ( $account_type ==  'free' ) ? "selected" : ""; ?>
					>
					<?php _e( 'Free', 'shinystat-analytics' ); ?>
					</option>
				</label>
				<br>
				<label>
					<option  
						id="<?php echo $this->option_prefix . '_account_type_business' ?>" 
						onclick="enable_disable_conv_name()"
						value="business" 
						<?php echo ( $account_type ==  'business' ) ? "selected" : ""; ?>
					>
					<?php _e( 'Business', 'shinystat-analytics' ); ?>
					</option>
				</label>
			</select>
		</fieldset>

		<?php
	}


	/**
	 * Render the advanced_options inputs with "Add parameter" button and name 
	 * and value pairs to define additional parameters
	 *
	 * @since  1.0.10
	 */
	public function shinystat_analytics_advanced_options() {

		$add_param_name = get_option( 'shinystat_analytics_advanced_options_add_param_name' );
		$add_param_value = get_option( 'shinystat_analytics_advanced_options_add_param_value' );
		?>

		<fieldset id="<?php echo $this->option_prefix . '_advanced_options' ?>">
			<div id="<?php echo $this->option_prefix . '_advanced_options_collapsible' ?>"></div>
			<div id="<?php echo $this->option_prefix . '_advanced_options_content' ?>" style="max-height:0px">
				<div id="<?php echo $this->option_prefix . '_advanced_options_newinput' ?>"></div>
				<button id="<?php echo $this->option_prefix . '_advanced_options_rowAdder' ?>"
					type="button" class="btn float-left button button-primary" style="margin:10px;"> 
					     &plus; &nbsp; <?php _e( 'Add parameter', 'shinystat-analytics' ); ?>
				</button>
			</div>
		</fieldset>

		<script type="text/javascript">
		(function() {
			var coll = document.getElementById("<?php echo $this->option_prefix . '_advanced_options_collapsible' ?>");
			if (!!coll) {
				coll.addEventListener("click", function() {
					this.classList.toggle("active");
					var content = this.nextElementSibling;
					if (content.style.maxHeight == "0px"){
						content.style.maxHeight = "unset";
					} else {
						content.style.maxHeight = "0px";
					} 
				});
			}

	                function createNewRow(index, name, value) {
				var rowDeleteClass = "<?php echo $this->option_prefix . '_advanced_options_rowDelete' ?>";
                	        var newRowAdd = '';
             	           	newRowAdd += '<fieldset style="padding:5px">';
                	        newRowAdd += '<button class="col-md-1 btn ' + rowDeleteClass + '" id="' + rowDeleteClass  + index + '" type="button">&times;</button>';
			
				newRowAdd += '<div class="<?php echo $this->option_prefix . '_advanced_options_inlineleft' ?>">';
				newRowAdd += '<label for="<?php echo $this->option_prefix . '_advanced_options_add_param_name' ?>' + index + '">';
				newRowAdd += '<?php _e( 'Name', 'shinystat-analytics' ); ?>:</label>';
				newRowAdd += '<input class="form-control mb-2" type="text" ';
				newRowAdd += 'id="<?php echo $this->option_prefix . '_advanced_options_add_param_name' ?>' + index + '" ';
				newRowAdd += 'name="<?php echo $this->option_prefix . '_advanced_options_add_param_name[]' ?>" ';
				newRowAdd += 'value="' + name + '">';
				newRowAdd += '</div>';

				newRowAdd += '<div class="<?php echo $this->option_prefix . '_advanced_options_inlineright' ?>">';
				newRowAdd += '<label for="<?php echo $this->option_prefix . '_advanced_options_add_param_value' ?>' + index + '">';
				newRowAdd += '<?php _e( 'Value', 'shinystat-analytics' ); ?>:</label>';
				newRowAdd += '<input class="form-control mb-2" type="text" ';
				newRowAdd += 'id="<?php echo $this->option_prefix . '_advanced_options_add_param_value' ?>' + index + '" ';
				newRowAdd += 'name="<?php echo $this->option_prefix . '_advanced_options_add_param_value[]' ?>" ';
				newRowAdd += 'value="' + value + '">';
				newRowAdd += '</div>';
                        	newRowAdd += '</fieldset>';
                        	
				var newinput = document.getElementById("<?php echo $this->option_prefix . '_advanced_options_newinput' ?>");
				newinput.insertAdjacentHTML('beforebegin', newRowAdd);
				var rowDelete = document.getElementById(rowDeleteClass + index);
				rowDelete.addEventListener("click", function () {
                        	        this.parentElement.remove();
                        	})
                	}

	                var index = 0;
			var rowAdder = document.getElementById("<?php echo $this->option_prefix . '_advanced_options_rowAdder' ?>");
			rowAdder.addEventListener("click", function () {
                        	createNewRow(index++, '', '');
	                });

			var definedNames = <?php echo json_encode($add_param_name) ?>;
			var definedValues = <?php echo json_encode($add_param_value) ?>;
			var value;
			for (var i=0; i < definedNames.length; i++) {
				value = (typeof definedValues === "object" && definedValues[i] !== "undefined") ? definedValues[i] : "";
				if (typeof definedNames[i] === "string" && definedNames[i].length > 0)
		                        createNewRow(index++, definedNames[i], value);
                	}

		})()
		</script>


		<?php
	}


	/**
	 * Render the conversion name input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function shinystat_analytics_conv_name() {

		$conv_name = get_option( $this->option_prefix . '_conv_name' );
		$conv_image = plugin_dir_url( dirname( __FILE__ ) ) . 'admin/images/' . __("conversions_en.png", 'shinystat-analytics' );

		?>
	
			<fieldset>
				<input 
					type="text" 
					name="<?php echo $this->option_prefix . '_conv_name' ?>"
					id="<?php echo $this->option_prefix . '_conv_name' ?>"
					value="<?php echo $conv_name ?>"
				>
				<div class="help-tip">
					<p>
						<?php echo
							__("Statistics about WooCommerce Conversion are available on ShinyStat dashboard for Business accounts.", 'shinystat-analytics' ) . "<br>" .
							__("Conversions data are collected by using, as identifier, the name filled in this field.", 'shinystat-analytics' ) . "<br>" .
							__("It is required that the conversion name is active in the list shown on the ShinyStat dashboard.", 'shinystat-analytics' )
						?>
						<br><br>
						<a href="<?php echo $conv_image ?>" target="_blank">
							<img src="<?php echo $conv_image ?>" width="160" />
						</a>
					</p>
				</div>
			</fieldset>

			<script type="text/javascript">
				function enable_disable_conv_name() {
					var type = document.getElementById("<?php echo $this->option_prefix . '_account_type_business' ?>");

					var conv_name = document.getElementById("<?php echo $this->option_prefix . '_conv_name' ?>");
					conv_name.disabled = type.selected ? false : true;
				}
				enable_disable_conv_name();
			</script>

		<?php
	}

}
