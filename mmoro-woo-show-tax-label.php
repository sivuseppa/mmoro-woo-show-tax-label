<?php
/*
Plugin Name: Tax Label for Astra Theme
Plugin URI:
Description: Näyttää tuotteen veron nimen kauppa-sivulla ja tuotesivulla hinnan alapuolella Astra-teemaa käytettäessä. 
Author: Mikko Mörö - sivuseppa.fi
Version:1.1
*/

$theme = wp_get_theme();

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( 'Astra' == $theme->name || 'Astra' == $theme->parent_theme ) {

		add_action( 'woocommerce_before_add_to_cart_form', 'mmoro_woo_simple_show_tax_label', 0 );
		add_action( 'astra_woo_shop_price_after', 'mmoro_woo_simple_show_tax_label', 10 );
			
		function mmoro_woo_simple_show_tax_label(){

			global $product;
			$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

			if( $product->is_type( 'simple' ) ){

				if (!empty($tax_rates)) {
					$tax_rate = reset($tax_rates);
					$tax_label = $tax_rate['label'];

					echo '<p class="mmoro-tax-label">' . 'sis. ' . $tax_label . '</p>';
				}
			}
			else{
				return;
			}
		}

		add_action( 'woocommerce_single_variation', 'mmoro_woo_variable_show_tax_label', 20 );
		add_action( 'astra_woo_shop_price_after', 'mmoro_woo_variable_show_tax_label', 10 );
			
		function mmoro_woo_variable_show_tax_label(){

			global $product;

			if( $product->is_type( 'variable' ) ){
				
				$available_variations = $product->get_available_variations();
				$tax_array = array();

				foreach ( $available_variations as $variation ){

					$variation_ID = $variation['variation_id'];
					$variation_product = wc_get_product( $variation_ID );
					$var_tax_rates = WC_Tax::get_rates( $variation_product->get_tax_class() );

					if (!empty($var_tax_rates)) {
						$var_tax_rate = reset($var_tax_rates);
						$var_tax_label = $var_tax_rate['label'];
						$tax_array[ $variation_ID ] = $var_tax_label;
					}
				}

				echo '<p id="variation-tax-element"></p>';
				?>
				<script type="text/javascript">

					var tax_array = <?php echo json_encode($tax_array); ?>;
					
					jQuery(document).ready(function() {
						
							jQuery(this).on( 'found_variation', function( event, variation ) {
								var variation_id = jQuery( 'input[name="variation_id"]' ).val();
								jQuery("p#variation-tax-element").html(tax_array[variation_id]);
								
							});
						
					});
					
					jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
						
						jQuery(this).on( 'found_variation', function( event, variation ) {
							
							var variation_id = jQuery( 'input[name="variation_id"]' ).val();
							jQuery("p#variation-tax-element").html(tax_array[variation_id]);
						});
					} );

				</script>

				<?php
				
			}
			else{
				return;
			}
		}
	}
}