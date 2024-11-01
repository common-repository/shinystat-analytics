<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php if ($this->show_success()) { ?>
		<div class="updated notice notice-success is-dismissible">
			<p>
			<?php
				echo __( 'Settings successfully updated.', 'shinystat-analytics' ); 
				echo ' <a target="_blank" href="https://report.shinystat.com" >'; 
				echo __( 'Go to the report page', 'shinystat-analytics' );
				echo '</a>';
			?>.
			</p>
		</div>
	<?php } ?>

	<?php if ($this->show_warning()) { ?>
		<div class="update-nag notice notice-warning is-dismissible">
			<?php
				echo __( 'Free accounts require to insert the ShinyStat Analytics widget.', 'shinystat-analytics' );
				echo ' <a href="' . admin_url('widgets.php') . '" >'; 
				echo __( 'Go to the widget page', 'shinystat-analytics' );
				echo '</a>';
			?>.
		</div>
	<?php } ?>

	<?php if ($this->show_info()) { ?>
		<div class="update-nag notice notice-info is-dismissible">
			<?php
				echo __( 'Business accounts can insert the ShinyStat Analytics widget.', 'shinystat-analytics' );
				echo ' <a href="' . admin_url('widgets.php') . '" >'; 
				echo __( 'Go to the widget page', 'shinystat-analytics' );
				echo '</a>';
			?>.
		</div>
	<?php } ?>

	<form id="shinystat-analytics-form" action="options.php" method="post">
	    <?php
	        settings_fields( $this->plugin_name );
	        do_settings_sections( $this->plugin_name );
	        submit_button();	
		?>
	</form>
	
</div>


