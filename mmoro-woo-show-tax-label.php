<?php
/*
Plugin Name: Tax Label for Astra Theme
Plugin URI:
Description: Näyttää tuotteen veron nimen kauppa-sivulla ja tuotesivulla hinnan alapuolella Astra-teemaa käytettäessä. 
Author: Mikko Mörö - sivuseppa.fi
Version:1.0
*/

$theme = wp_get_theme();

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( 'Astra' == $theme->name || 'Astra' == $theme->parent_theme ) {

		function mmoro_woo_show_tax_label(){

			global $product;
			$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

			if (!empty($tax_rates)) {
				$tax_rate = reset($tax_rates);
				$tax_label = $tax_rate['label'];

				echo '<p class="mmoro-tax-label">' . $tax_label . '</p>';
			}
		}

		add_action( 'astra_woo_shop_price_after', 'mmoro_woo_show_tax_label', 10 );

		add_action( 'woocommerce_single_product_summary', 'mmoro_woo_show_tax_label', 15 );

	}
}