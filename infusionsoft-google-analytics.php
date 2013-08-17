<?php

/*
Plugin Name: Infusionsoft Google Analytics
Plugin URI: http://mindsharelabs.com/
Description: Provides Google Analytics ecommerce tracking for sales made through Infusionsoft's shopping cart
Version: 0.1
Author: Mindshare Studios
Author URI: http://mindsharelabs.com/
*/

/**
 * @copyright Copyright (c) 2013. All rights reserved.
 * @author    Mindshare Studios, Inc.
 *
 * @license   Released under the GPL license http://www.opensource.org/licenses/gpl-license.php
 * @see       http://wordpress.org/extend/plugins/wp-ultimate-search/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 *
 */
if(!class_exists("Infusionsoft_GA")) {
	class Infusionsoft_GA {
	
		private $app;
		private $options = array(
			'api_key' => '',
			'application_name' => ''
		);
	
		public function __construct() {
			add_shortcode('infusionsoft-google-analytics', array($this, 'track_order'));
			if(is_admin()) {
				add_action( 'admin_init', array($this, 'register_settings') );
				add_action( 'admin_menu', array($this, 'iga_options') );
			}
		}
	
		private function connect() {
			require("isdk.php");  
			$this->app = new iSDK;
			
			$settings = get_option( 'iga_options', $this->options );

			$this->app->cfgCon($settings['application_name'], $settings['api_key']);
			
			$result = $this->app->dsGetSetting('Contact', 'optiontypes');
			if (strpos($result, 'InvalidKey') !== false) {
			    return $result . ".";
			} elseif(strpos($result, '200') !== false) {
				return $result . ". This can be caused by specifying an incorrect application name.";
			} else {
				return true;
			}
		}

		public function track_order() {
			$order_id = $_REQUEST['orderId'];
			
			if($this->connect() === true) {

				$invoice = $this->app->getInvoiceID($order_id);
				$query = array('ID' => $invoice);
				$returnFields = array('TotalPaid', 'ProductSold');
				
				$invoice_data = $this->app->dsQuery("Invoice",10,0,$query,$returnFields); ?>
				
				<script type="text/javascript">
				  _gaq.push(['_addTrans',
				    '<?php echo $invoice ?>',           					// transaction ID - required
				    '',  													// affiliation or store name
				    '<?php echo $invoice_data[0]["TotalPaid"]; ?>',         // total - required
				    '',           											// tax
				    '',              										// shipping
				    '',       												// city
				    '',     												// state or province
				    ''             											// country
				  ]);
				</script>
				
				<?php $products = explode(",", $invoice_data[0]["ProductSold"]);
				
				foreach($products as $product) {
					$query = array('ID' => $product);
					$returnFields = array('Id', 'ProductName', 'ProductPrice');		

					$product_data = $this->app->dsQuery("Product",10,0,$query,$returnFields); ?>
					
					<script type="text/javascript">
					
					_gaq.push(['_addItem',
					    '<?php echo $invoice; ?>',           					// transaction ID - required
					    '<?php echo $product_data[0]["Id"]; ?>',           		// SKU/code - required
					    '<?php echo $product_data[0]["ProductName"]; ?>',       // product name
					    '',   													// category or variation
					    '<?php echo $product_data[0]["ProductPrice"]; ?>',      // unit price - required
					    '1'               										// quantity - required
					  ]);
				  
					</script>
				<?php } ?>

				<script type="text/javascript">
				  _gaq.push(['_trackTrans']); 								//submits transaction to the Analytics servers
				</script>

			<?php } else {
				return "Unable to connect to Infusionsoft. Please check your settings and try again.";
			}
			
		}

		public function register_settings() {
			register_setting( 'iga_options', 'iga_options' );
		}

		public function iga_options() {
			// Add theme options page to the addmin menu
			add_options_page( 'Infusionsoft Google Analytics Settings', 'Infusionsoft Google Analytics', 'manage_options', 'iga-options', array($this, 'iga_options_page') );
		}

		// Function to generate options page
		public function iga_options_page() {

			if ( ! isset( $_REQUEST['updated'] ) )
				$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>
				
			<?php $connection = $this->connect(); ?>	

			<?php if($connection === true) { ?>
				<div class="updated" style=""><p><strong>Congratulations:</strong> you've successfully established a connection to Infusionsoft.</p></div>
			<?php } else { ?>
				<div class="error"><p><strong>Error: </strong><?php echo $connection; ?> Please try again or contact Infusionsoft support.</p></div>						
			<?php } ?>
			<div class="wrap">

				<?php screen_icon(); echo "<h2>Infusionsoft Google Analytics Settings</h2>";
				// This shows the page's name and an icon if one has been provided ?>

				<?php if ( false !== $_REQUEST['updated'] ) : ?>
				<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
				<?php endif; // If the form has just been submitted, this shows the notification ?>

				<form method="post" action="options.php">

					<?php $settings = get_option( 'iga_options', $this->options ); ?>

					<?php settings_fields( 'iga_options' );
					/* This function outputs some hidden fields required by the form,
					including a nonce, a unique number used to ensure the form has been submitted from the admin page
					and not somewhere else, very important for security */ ?>

					<table class="form-table"><!-- Grab a hot cup of coffee, yes we're using tables! -->

					<tr valign="top"><th scope="row"><label for="footer_copyright">Infusionsoft API Key</label></th>
					<td>
					<input id="API Key" name="iga_options[api_key]" type="text" size="35" value="<?php  esc_attr_e($settings['api_key']); ?>" />
					</td>
					</tr>
					<tr valign="top"><th scope="row"><label for="footer_copyright">Application Name</label></th>
					<td>
					<input id="Application Name" name="iga_options[application_name]" type="text" value="<?php  esc_attr_e($settings['application_name']); ?>" />
					</td>
					</tr>
					</table>

					<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>

				</form>

			</div>

			<?php
		}
	}
}

if(class_exists("Infusionsoft_GA")) {
	$infusionsoftga = new Infusionsoft_GA;
	
	// public function to go here
	
}

?>