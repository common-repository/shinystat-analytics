<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link	 https://www.shinystat.com
 * @since	1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/public
 * @author     ShinyStat <support@shinystat.com>
 */
class Shinystat_Analytics_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var	string    $plugin_name    The ID of this plugin.
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
	 * @since    1.0.0
	 * @access   private
	 * @var	string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param	string    $plugin_name       The name of the plugin.
	 * @param	string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
		
	/**
	 * Register the JavaScript for the public-facing side of the site
	 * to load getcod javascript in each page of the website.
	 *
	 * @since    1.0.0
	 */
	public function load_getcod() {

		$account_name = get_option( $this->option_prefix . '_account_name' );
		$account_type = get_option( $this->option_prefix . '_account_type' );
		$add_param_name = get_option( $this->option_prefix . '_advanced_options_add_param_name' );
		$add_param_value = get_option( $this->option_prefix . '_advanced_options_add_param_value' );

		if ($account_type == "business")
			$src = "https://codicebusiness.shinystat.com/cgi-bin/getcod.cgi";
		else
			$src = "https://codice.shinystat.com/cgi-bin/getcod.cgi";

		$src .= "?USER=" . $account_name;
		$src .= "&NODW=yes";

		//additional parameters with valid name
		if(is_array($add_param_name)) {
			for ($i=0; $i < count($add_param_name); $i++) {
				$value = (count($add_param_value) > $i) ? $add_param_value[$i] : "";
				if (strlen($add_param_name[$i]) > 0)
					$src .= "&" . $add_param_name[$i] ."=" . $value;
			}
		}

		$src .= "&WPPV=" . $this->version;

		wp_enqueue_script( $this->plugin_name . "_getcod", $src, array(), NULL, false );
	}


	/**
	 * Add async attribute to script getcod
	 * 
	 * @since    1.0.0
	 */
	function shinystat_analytics_getcod_async ( $tag, $handle ) {

		if ( $this->plugin_name . "_getcod" !== $handle )
			return $tag;
	
		return str_replace( ' src', ' async src', $tag ); 
	}


	/**
	 * Clean the field content to insert inside javascript to avoid problematic
	 * characters
	 * 
	 * @since    1.0.0
	 * @param	string    $field       Content of the field to clean 
	 */
	function clean_field($field, $encode=true) {

		$max_string_length = 2000;

		$field = str_replace("\n", " ", $field);
		$field = str_replace("\r", " ", $field);
		$field = str_replace("\"", "", $field);
		$field = substr($field, 0, $max_string_length);

		if ($encode)
			$field = rawurlencode($field);

		return $field;
	}


	/**
	 * Callback function for action woocommerce_thankyou to extract order data
	 * for ShinyStat conversion report page.
	 * 	 
	 * @since    1.0.0
	 * @param	int    $order_id       The id of the processed order
	 */
	public function woocommerce_thankyou_send_conversion( $order_id ) {

		$conv_name = get_option( $this->option_prefix . '_conv_name' );
		$account_type = get_option( $this->option_prefix . '_account_type' );

		if ($conv_name == "")
			return;
		if ($account_type != "business")
			return;
		

		// Get an instance of the WC_Order object
		$order = wc_get_order( $order_id );
	
		?>

			<script type="text/javascript" id="shn-conv-data" async>

				var _ssCONV, _ssCurr;
				(function() {
	
					_ssCONV = "<?php echo $conv_name ?>";
					_ssCurr = "<?php echo $order->get_currency(); ?>";

					function send_conv_data (attempt) {
						setTimeout(function () { 
							if (typeof(ssORD) === "undefined") {
								if (attempt > 0)
									send_conv_data(attempt - 1);
							} else {

								//send order data
								ssORD(
									"<?php echo $order->get_id(); ?>", 
									"<?php echo $order->get_total(); ?>",
									"<?php echo $order->get_total_tax(); ?>",
									"<?php echo $order->get_shipping_total(); ?>",
									"<?php echo $this->clean_field($order->get_shipping_country(), false); ?>",
									"<?php echo $this->clean_field($order->get_shipping_state(), false); ?>",
									"<?php echo $this->clean_field($order->get_shipping_city(), false); ?>"
								);
		
								//send items data
								<?php	
								foreach ( $order->get_items() as $item_id => $item ) { 
								?>	ssPROD(
										"<?php echo $item->get_product_id() ?>",
										"<?php echo $item->get_quantity(); ?>",
										"<?php echo ($item->get_total() / $item->get_quantity()) ?>",
										"<?php echo $this->clean_field($item->get_name(), false); ?>",
										"<?php echo strip_tags(wc_get_product_category_list($item->get_product_id(), ',')); ?>"
									);
								<?php	
								}
								?>
								
								ssCvTrack();
							}
						}, 100); 
					};
	
					send_conv_data(10);
	
				})();
	
			</script>


		<?php
	
	}

							
	/**
	 * Check if the request contains POST attributes to add items in the cart.
	 *
	 * @since 1.0.9
	 */
	private function is_post_add_to_cart( ) {
		global $_POST;
		
		if ( isset($_POST["add-to-cart"]) || ( isset($_GET['wc-ajax']) && $_GET['wc-ajax'] == 'add_to_cart' ) ) {
		
			if ( isset($_POST["quantity"]) && is_numeric($_POST["quantity"]) )
				return True;

		}
		
		return False;
	}


	/**
	 * Javascript to be added to website pages containing woocommerce.
	 * It creates the shn_engage structure with functions to read
	 * and modify cart content.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_cart_content( ) { 

		if ( ! function_exists('WC') )
			return;
			
		$session_existing = WC()->cart && WC()->session && WC()->session->has_session();
		$user_logged = is_user_logged_in() || is_admin();
		$update_cart_timestamp = $this->is_post_add_to_cart();

		?>

			<script type="text/javascript" id="shn-engage-definition">

				(function () {

					var shn_engage  = {

						/**
		 				 * Apply the redirect to cart or checkout page
		 				 */
						apply_redirect: function(redirect) {

							if (redirect == "cart")
								window.location.href = "<?php echo get_permalink( wc_get_page_id( "cart" )); ?>";
							if (redirect == "checkout")
								window.location.href = "<?php echo get_permalink( wc_get_page_id( "checkout" )); ?>";
			
						},

						/**
						 * Call the callback function with the parameter cart_content
						 */
						get_cart_content: function(callback_fnc) {

							let xhr_prod = new XMLHttpRequest();
							xhr_prod.open('GET', "<?php echo get_rest_url( null, 'shinystat/v1/cart' ); ?>" );

							xhr_prod.onload  = function() {
								if (!!xhr_prod.responseText) {
									
									var jsonResp = JSON.parse(xhr_prod.responseText);
									if (!jsonResp)
										return;

									jsonResp.cart_update_ts = localStorage.getItem("cart_update_ts");
									callback_fnc(jsonResp, "completed");
								
								}
							}

							xhr_prod.send();
						},


						/**
						 * Set local storage cart_update_ts with current timestamp 
						 */
						update_timestamp: function( ) {

							var t  = Math.floor(new Date().getTime() / 1000);
							localStorage.setItem("cart_update_ts", t);
							
							return t;
						},

						/**
						 * Extract js var value from text (first match)
						 */
						extract_nonce(text, var_name) {

							var var_name_ext = `var ${var_name} = "(.*)";`;
							var reg_exp = new RegExp(var_name_ext, "g");

							var nonce = text.match(reg_exp) || [""];
							var nonce_val = nonce[0].substring(var_name.length + 8, nonce[0].length-2); 

							return nonce_val;
						},

						/**
						 * Apply the discount to the current session and redirect to selected page.
						 */
						apply_discount_with_nonce: function(name, redirect, apply_coupon_nonce) {

							let xhr = new XMLHttpRequest();
							xhr.open('POST', woocommerce_params.wc_ajax_url.replace( '%%endpoint%%', 'apply_coupon' ), );
							xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

							xhr.onload  = function() {
								if (!xhr.responseText)
									return;

								shn_engage.apply_redirect(redirect);
							}
		
							xhr.send("coupon_code=" + name + "&security=" + apply_coupon_nonce);
						},

						/**
						 * Get the nonce to apply discount, if the cart session is still not initialized, call cart page
						 * and get the valid nonce from the response before apply discount
						 */
						apply_discount: function(name, redirect) {

							var srcdata_nonce = "<?php echo ($user_logged || $session_existing) ? "complete" : "partial"; ?>";
							var apply_coupon_nonce = "<?php echo wp_create_nonce('apply-coupon') ?>";

							if (srcdata_nonce == "complete") {
							
								shn_engage.apply_discount_with_nonce(name, redirect, apply_coupon_nonce);
							
							} else {

								//get valid nonce after the cart session is initialized
								let xhr_cart = new XMLHttpRequest();
								xhr_cart.open('GET', "<?php echo get_permalink( wc_get_page_id( "cart" )); ?>");

								xhr_cart.onload  = function() {
									if (!!xhr_cart.responseText) {
									
										apply_coupon_nonce = shn_engage.extract_nonce(xhr_cart.responseText, "apply_coupon_nonce");
							
										shn_engage.apply_discount_with_nonce(name, redirect, apply_coupon_nonce);	
									}
								}
								xhr_cart.send();
							}

						},

						/**
						 * Get product details by product id
						 */
						get_product_details: function(callback_fnc, prod_id) {
							
							let xhr_prod = new XMLHttpRequest();
							xhr_prod.open('GET', "<?php echo get_rest_url( null, 'shinystat/v1/product/' ); ?>" + prod_id);

							xhr_prod.onload  = function() {
								if (!!xhr_prod.responseText) {
									
									var jsonResp = JSON.parse(xhr_prod.responseText);
									if (!jsonResp)
										return;

									callback_fnc(jsonResp);
								
								}
							}

							xhr_prod.send();
						},


						/**
						 * Add product (identified by its variant id) for input quantity
						 * to the cart of the current session
						 */
						add_product: function(id, quantity, redirect) {

							let xhr = new XMLHttpRequest();
							xhr.open('POST', woocommerce_params.wc_ajax_url.replace( '%%endpoint%%', 'add_to_cart' ), );

							xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

							xhr.onload  = function() {
								if (!xhr.responseText)
									return;

								shn_engage.apply_redirect(redirect);
							}

							xhr.send("product_id=" + id + "&quantity=" + quantity);
						},


						/**
						* Set product quantity (identified by its variant id) in the cart of the current session.
						* To remove a product from cart, set quantity to zero. 
						*/
						update_product_quantity: function(id, quantity=0, redirect="") {

							let xhr_prod = new XMLHttpRequest();
							xhr_prod.open('POST', "<?php echo get_rest_url( null, 'shinystat/v1/set_product_quantity/' ); ?>");
							
							xhr_prod.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

							xhr_prod.onload  = function() {
								if (!!xhr_prod.responseText) {
									
									var jsonResp = JSON.parse(xhr_prod.responseText);
									if (!jsonResp)
										return;

									shn_engage.apply_redirect(redirect);
								
								}
							}

							xhr_prod.send("product_id=" + id + "&quantity=" + quantity);
						},

						/**
						* Return boolean indicating that the current platform 
						* is wordpress with woocommerce plugin
						*/
						is_woocommerce: function() {
							return (!!woocommerce_params);
						},

						/**
						 * Get site-id from a page element and call add_event_listeners
						 *
						 */
						 init: function() {

							 function addListener(e, t, n) {     
								 window.jQuery && window.jQuery(e).bind ? window.jQuery(e).bind(t, n) : 
									 e.addEventListener ? e.addEventListener(t, n) : 
									 e.attachEvent && e.attachEvent('on' + t, n);    
							 }

							 //listen wc-blocks_added_to_cart event to update cart timestamp
							 function detect_cart_change() {
								setTimeout ( function() {
									addListener(document.body, 'wc-blocks_added_to_cart', function() {
										shn_engage.update_timestamp( )
									});
								}, 2000);
							 }

							 if (document.readyState == "complete")
								detect_cart_change();
							 else 
								addListener(window, 'load', detect_cart_change());
						 }

					}

<?php					if ( $update_cart_timestamp ) {
						echo "\t\t\t\t\tshn_engage.update_timestamp( )";
					}                                   
?>


					shn_engage.init();
					
					//set shn_engage object returning selected methods
					if (!window.shn_engage) {
						window.shn_engage = {
							apply_discount:   		shn_engage.apply_discount,
							get_cart_content: 		shn_engage.get_cart_content,
							get_product_details: 		shn_engage.get_product_details,
							add_product:			shn_engage.add_product,
							update_product_quantity:	shn_engage.update_product_quantity,
							update_timestamp:		shn_engage.update_timestamp,
							is_woocommerce: 	  	shn_engage.is_woocommerce,
						};
					};


					var head = document.getElementsByTagName('head');
					if (head.length > 0) {
						var s = document.createElement("script");
						s.type = "text/javascript";
						s.id = "shn-engage-cart-update";
						head[0].append(s);
					}

				})();
		
			</script>

		<?php

	}


	/**
	 * Update timestamp when it is an ajax post add-cart request 
	 *
	 * @since    1.0.0
	 */
	function woocommerce_add_to_cart_fragments( $fragments ) {

		if ( ! function_exists('WC') )
			return $fragments;

		$update_cart_timestamp = $this->is_post_add_to_cart();
		

		$script = '<script type="text/javascript" id="shn-engage-cart-update">';
		
		if ( $update_cart_timestamp )
			$script .= 'shn_engage.update_timestamp( )';

		$script .= '</script>';

               

		$fragments['script#shn-engage-cart-update'] = $script;
 
		return $fragments;

	}


	/**
	 * Set product quantity in the cart for the current session by using 
	 * post parameters product_id and quantity.
	 * 
	 * @since    1.0.14
	 */
	public function set_product_quantity( ) {
		global $_POST;

		$product_id = $_POST['product_id'];
		$quantity = $_POST['quantity'];

		if ( ! is_numeric($product_id) )
			return ['code' => 'parameter_value_not_valid_product_id', 'message' => 'product_id is not a numeric value'];
		
		if ( ! is_numeric($quantity) )
			return ['code' => 'parameter_value_not_valid_quantity', 'message' => 'quantity is not a numeric value'];


		if ( ! function_exists('WC') )
			return ['code' => 'wc_not_found', 'message' => ''];

		$cart = WC()->cart;

		$product_key = null;
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id ) {
				$product_key = $cart_item_key;
				break;
			}
		}

		if ( ! $product_key )
			return ['code' => 'product_not_found', 'message' => 'product id does not correspond to any product in cart'];

		$cart->set_quantity($product_key, $quantity);

		return ['code' => 'successfull', 'message' => ''];
	}
		
		
	/**
	 * Get data about specified wc product id for the rest api response.
	 * 
	 * @since    1.0.12
	 */
	public function get_product_details( $data ) {
		
		if ( ! function_exists('WC') )
			return ['code' => 'wc_not_found', 'message' => ''];

		$product = wc_get_product( $data['id'] );

		if ( ! $product )
			return ['code' => 'product_not_found', 'message' => 'product id does not correspond to any product'];


		$attributes = [];
		foreach ( $product->get_attributes() as $attribute ) {
			if ( is_string($attribute) ) {
				$attributes[] = $attribute;
			} else {
				$attribute_data = $attribute->get_data();
				$value = $attribute_data['value'];
				$attributes[] = [
					'name' => $this->clean_field( $attribute_data['name'] ),
					'value' => $this->clean_field( is_array($value) ? '' : $value ),
				];
			}
		}

		$categories = [];
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		if ( is_array($terms) ) {
			foreach( $terms as $term ) {
				$categories[] = $this->clean_field( $term->name );
			}
		}

		return [
			'id' => $product->get_id(),
			'product_title' => $this->clean_field( $product->get_name() ),
			'product_type' => $this->clean_field( $product->get_description() ),
			'handle' => $this->clean_field( $product->get_slug() ),
			'url' => $this->clean_field( $product->get_permalink( $product->get_id() ) ),
			'image' => $this->clean_field( wp_get_attachment_url( $product->get_image_id() ) ),
			'categories' => $categories, 
			'price' => $product->price * 100,
			'compare_at_price' => ( ($product->regular_price != '') ? $product->regular_price : 0 ) * 100,
			'options_with_values' => $attributes,
		];
	
	}

	/**
	 * Get the wc cart content.
	 * 
	 * @since    1.0.14
	 */
	function get_cart_content( ) {
		
		if ( ! function_exists('WC') )
			return [];

		$cart = WC()->cart;


		$cart_content = [
			'item_count' => ($cart != null) ? $cart->get_cart_contents_count() : 0,
			'total_price' => ($cart != null) ? (($cart->subtotal - $cart->get_discount_total()) * 100) : 0,
			'total_discount' => ($cart != null) ? ($cart->get_discount_total() * 100) : 0,
			'currency' => get_woocommerce_currency(),
			'cart_update_ts' => null,
			'items' => [],
		];


		$cart_items = ($cart != null) ? $cart->get_cart() : [];
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$product_json = json_decode($product, true);
			$attributes = []; 
			foreach ( $product_json['attributes'] as $name => $value ) {
				$attributes[] = [
					'name' =>	$this->clean_field($name),
					'value' =>      $this->clean_field(is_array($value) ? '' : $value),
				];
			}
      
			$cart_content["items"][] = [
				'id' =>                     $product_json['id'],
				'quantity' =>               $cart_item['quantity'],
				'key' =>                    $this->clean_field($cart_item['key']),
				'handle' =>                 $this->clean_field($product_json['slug']),
				'product_title' =>          $this->clean_field($product_json['name']),
				'product_type' =>           $this->clean_field($product_json['description']),
				'url' =>                    $this->clean_field($product->get_permalink( $cart_item )),
				'compare_at_price' =>       ( ($product_json['regular_price'] != '') ? $product_json['regular_price'] : 0 ) * 100,
				'price' =>                  $product_json['price'] * 100,
				'discounted_price' =>       (int) ($cart->get_discount_total() * $product_json['price'] * 100 / ($cart->subtotal)),
				'options_with_values' =>    $attributes,
			];
		}


		return $cart_content;
	}



	/**
	 * Register rest route to get data about specified wc product.
	 * The route is /?rest_route=/shinystat/v1/cart
	 * The route is /?rest_route=/shinystat/v1/product/{prod_id}
	 * The route is /?rest_route=/shinystat/v1/set_product_quantity
	 *
	 * @since    1.0.12
	 */
	public function register_shinystat_rest_route() {
	
		register_rest_route( 'shinystat/v1', 'product/(?P<id>\d+)', [
			'methods' => [ 'GET' ],
			'callback' => array($this, 'get_product_details'),
			'permission_callback' => '__return_true'
		]);

		register_rest_route( 'shinystat/v1', 'cart', [
			'methods' => [ 'GET' ],
			'callback' => array($this, 'get_cart_content'),
			'permission_callback' => '__return_true'
		]);
		
		register_rest_route( 'shinystat/v1', 'set_product_quantity', [
			'methods' => [ 'POST' ],
			'callback' => array($this, 'set_product_quantity'),
			'permission_callback' => '__return_true'
		]);

	}


	/**
	 * Add ShinyStat AMP tag when amp plugin is in Reader mode.
	 * @link https://amp-wp.org/documentation/getting-started/analytics/
	 *
	 * @param array $analytics
	 *	array of associative array(s) of the analytics entries to output, containing
	 *	type: the analytics vendor (shinystat)
	 *	config_data: the config data to include in the <amp-analytics> script tag.
	 *
	 * @return
	 *	the updated version of $analytics
	 */
	public function amp_add_shinystat_analytics($analytics) {

		$account_name = get_option( $this->option_prefix . '_account_name' );

		if ( ! is_array( $analytics ) ) {
			$analytics = array();
		}
		
		$analytics['shinystat'] = array(
			'type' => 'shinystat',
			'config_data' => array(
				'vars' => array(
					'account' => $account_name
				),
				'requests' => array(
					'pageview' => '${base}?PAG=${sourceUrl}&${commpar}${pagepar}'
				)
			)
		); 
		
		return $analytics;
	}


	/**
	 * Add ShinyStat AMP tag when amp plugin is in Standard or Transitional mode.
	 * @link https://amp-wp.org/documentation/getting-started/analytics/
	 *
	 * @param array $analytics_entries 
	 *	array of associative array(s) of the analytics entries to output, containing
	 *	type: the analytics vendor (shinystat)
	 *	config: JSON-encoded data to be output in the <amp-analytics> script tag.
	 * @return
	 *	the updated version of $analytics_entries 
	 */
	public function amp_add_shinystat_analytics_entry($analytics_entries) {

		$account_name = get_option( $this->option_prefix . '_account_name' );


		if ( ! is_array( $analytics_entries ) ) {
			$analytics_entries = array();
		}
		
		$analytics_entries['shinystat'] = array(
			'type' => 'shinystat',
			'config' => wp_json_encode( 
				array(
					'vars' => array(
						'account' => $account_name
					),
					'requests' => array(
			  			'pageview' => '${base}?PAG=${sourceUrl}&${commpar}${pagepar}'
					)
				)
			)
		); 
		
		return $analytics_entries;
	}


}
