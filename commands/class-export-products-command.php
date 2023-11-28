<?php

if ( ! class_exists( 'Export_Manufacturer_Products_Command' ) ) {
	class Export_Manufacturer_Products_Command {

		/**
		 * Exports products to a CSV file, filtered by attribute, slug, or SKU prefix.
		 *
		 * ## OPTIONS
		 *
		 * [--att=<attribute>]
		 * : The product attribute to filter by (e.g., 'pa_manufacturer').
		 *
		 * [--slug=<slug>]
		 * : The slug value to filter the attribute by.
		 *
		 * [--sku=<sku_prefix>]
		 * : The SKU prefix to filter products.
		 *
		 * <file>
		 * : The path where the CSV file will be saved.
		 *
		 * ## EXAMPLES
		 *
		 *     wp nvm products export_csv --att=pa_manufacturer --slug=brand-slug --sku=SKU123 /path/to/export.csv
		 *     wp nvm products export_csv /path/to/export.csv
		 */
		public function __invoke( $args, $assoc_args ) {
			$attribute  = isset( $assoc_args['att'] ) ? $assoc_args['att'] : '';
			$slug       = isset( $assoc_args['slug'] ) ? $assoc_args['slug'] : '';
			$sku_prefix = isset( $assoc_args['sku'] ) ? $assoc_args['sku'] : '';
			$file       = $args[0];

			// Open file handle
			$fp = fopen( $file, 'w' );

			// Add CSV headers
			fputcsv( $fp, array( 'SKU', 'Stock Status', 'Regular Price', 'Sale Price', 'Manage_stock', 'stock_quantity', 'ID' ) );

			// Set up base query arguments
			$query_args = array(
				'status' => 'publish',
				'limit'  => -1,
			);

			// Add attribute filter if specified
			if ( $attribute && $slug ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => $attribute,
						'field'    => 'slug',
						'terms'    => $slug,
					),
				);
			}

			$products = wc_get_products( $query_args );
			foreach ( $products as $product ) {
				$sku = $product->get_sku();

				if ( $sku_prefix && strpos( $sku, $sku_prefix ) !== 0 ) {
					continue;
				}

				$regular_price = $product->get_regular_price();
				$sale_price    = $product->get_sale_price() ?: $regular_price; // Use regular price if sale price is empty

				$terms                    = $manufacturer_slug ? get_the_terms( $product->get_id(), 'pa_manufacturer' ) : array();
				$manufacturer_name        = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->name : '';
				$manufacturer_slug_output = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->slug : '';

				fputcsv(
					$fp,
					array(
						$sku,
						$product->get_stock_status(),
						$regular_price,
						$sale_price,
						$product->get_manage_stock(),
						$product->get_stock_quantity(),
						$product->get_id(),
					)
				);

			}

			// Close file handle
			fclose( $fp );

			WP_CLI::success( "Exported products to {$file}" );
		}
	}

	WP_CLI::add_command( 'nvm products export_csv', 'Export_Manufacturer_Products_Command' );
}
