<?php

if ( ! class_exists( 'Set_Outofstock_Command' ) ) {
	class Set_Outofstock_Command {

		/**
		 * Sets products to 'out of stock', filtered by attribute, SKU, or all products.
		 *
		 * ## OPTIONS
		 *
		 * [--att=<attribute>]
		 * : The attribute to filter by (e.g., 'pa_color').
		 *
		 * [--slug=<slug>]
		 * : The slug of the attribute to filter by.
		 *
		 * [--sku=<sku_prefix>]
		 * : The SKU prefix to filter products.
		 *
		 * [--all]
		 * : If set, all products will be marked as 'out of stock'.
		 *
		 * ## EXAMPLES
		 *
		 *     wp nvm set_outofstock --att=pa_color --slug=red
		 *     wp nvm set_outofstock --sku=SKU123_
		 */
		public function __invoke( $args, $assoc_args ) {
			WP_CLI::confirm( 'Are you sure you want to set out of stock?', $assoc_args );

			$attribute  = isset( $assoc_args['att'] ) ? $assoc_args['att'] : '';
			$slug       = isset( $assoc_args['slug'] ) ? $assoc_args['slug'] : '';
			$sku_prefix = isset( $assoc_args['sku'] ) ? $assoc_args['sku'] : '';

			// if ( empty( $attribute ) && empty( $slug ) && empty( $sku_prefix ) ) {
			// WP_CLI::error( 'Please specify an attribute, SKU, or use --all to update all products.' );
			// return;
			// }

			$query_args = array(
				'limit'        => -1,
				'stock_status' => array( 'instock', 'onbackorder' ),
			);

			// Construct query based on provided parameters
			$this->construct_query( $query_args, $attribute, $slug, $sku_prefix );

			$products = wc_get_products( $query_args );

			foreach ( $products as $product ) {
				$this->set_product_outofstock( $product );

				$status = $product->get_stock_status();
				if ( 'outofstock' !== $status ) {
					WP_CLI::log( 'Checking: ' . $product->get_stock_status() );
				}
			}

			WP_CLI::success( 'Completed setting products to out of stock.' );
		}

		private function construct_query( &$query_args, $attribute, $slug, $sku_prefix ) {
			if ( $attribute && $slug ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => $attribute,
						'field'    => 'slug',
						'terms'    => $slug,
					),
				);
			} elseif ( $sku_prefix ) {
				$query_args['sku'] = $sku_prefix;
			}
		}

		private function set_product_outofstock( $product ) {
			// Simple Products
			if ( ! $product->is_type( 'variable' ) ) {

				if ( $product->get_backorders() != 'no' ) {
					$product->set_backorders( 'no' );
				}

				if ( $product->get_manage_stock() ) {
					$product->set_manage_stock( false );
					$product->set_stock_quantity( 0 );
				}

				$product->set_stock_status( 'outofstock' );
				$product->save();

				WP_CLI::log( 'Set outofstock for product ID: ' . $product->get_id() );
			}
			// Variable Products
			if ( $product->is_type( 'variable' ) ) {

				if ( $product->get_backorders() !== 'no' ) {
					$product->set_backorders( 'no' );
				}

				if ( $product->get_manage_stock() ) {
					$product->set_manage_stock( false );
					$product->set_stock_quantity( 0 );
				}
				$product->set_stock_status( 'outofstock' );
				$product->save();

				foreach ( $product->get_children() as $child_id ) {
					$child_product = wc_get_product( $child_id );

					if ( $child_product ) {

						if ( $child_product->get_backorders() != 'no' ) {
							$child_product->set_backorders( 'no' );
						}

						if ( $child_product->get_manage_stock() ) {
							$child_product->set_manage_stock( false );
							$child_product->set_stock_quantity( 0 );
						}

						$child_product->set_stock_status( 'outofstock' );
						$child_product->save();
					}
				}

				$product->save();
				WP_CLI::log( 'Set outofstock for variable product ID: ' . $product->get_id() );
			}
		}
	}
	WP_CLI::add_command( 'nvm products set_outofstock', 'Set_Outofstock_Command' );
}
