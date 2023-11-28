<?php

if ( ! class_exists( 'Update_Products_From_CSV_Command' ) ) {
	class Update_Products_From_CSV_Command {

		/**
		 * Updates products from a CSV file based on specified attributes.
		 *
		 * ## OPTIONS
		 *
		 * <file>
		 * : The path to the CSV file.
		 *
		 * [--update_stock]
		 * : Whether to update the stock status.
		 *
		 * [--update_price]
		 * : Whether to update the regular and sale prices.
		 *
		 * ## EXAMPLES
		 *
		 *     wp nvm products update_csv /path/to/file.csv --update_stock --update_price
		 *     wp nvm products update_csv /path/to/file.csv --update_stock
		 */
		public function __invoke( $args, $assoc_args ) {
			list( $file ) = $args;

			$update_stock = isset( $assoc_args['update_stock'] );
			$update_price = isset( $assoc_args['update_price'] );

			if ( ! file_exists( $file ) ) {
				WP_CLI::error( "File not found: {$file}" );
				return;
			}

			$fp = fopen( $file, 'r' );

			// Skip header line
			fgetcsv( $fp );

			while ( ( $row = fgetcsv( $fp ) ) !== false ) {
				$sku        = $row[0];
				$product_id = wc_get_product_id_by_sku( $sku );

				if ( $product_id ) {
					$product      = wc_get_product( $product_id );
					$needs_update = false;

					if ( $update_stock && isset( $row[1] ) && $product->get_stock_status() !== $row[1] ) {
						$product->set_stock_status( $row[1] );
						$needs_update = true;
					}

					if ( $update_price ) {
						if ( isset( $row[2] ) && $product->get_regular_price() !== $row[2] ) {
							$product->set_regular_price( $row[2] );
							$needs_update = true;
						}

						if ( isset( $row[3] ) && $product->get_sale_price() !== $row[3] ) {
							$product->set_sale_price( $row[3] );
							$needs_update = true;
						}
					}

					if ( $needs_update ) {
						$product->save();
						WP_CLI::success( "Updated product: {$sku}" );
					} else {
						WP_CLI::log( "No update needed for product: {$sku}" );
					}
				} else {
					WP_CLI::warning( "No product found for SKU: {$sku}, skipping." );
				}
			}

			fclose( $fp );

		}
	}

	WP_CLI::add_command( 'nvm products update_csv', 'Update_Products_From_CSV_Command' );
}
