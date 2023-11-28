<?php
if ( ! class_exists( 'Delete_All_Products_Command' ) ) {
	class Delete_All_Products_Command {

		/**
		 * Deletes all products after confirmation.
		 *
		 * ## OPTIONS
		 *
		 * [--yes]
		 * : Answer yes to the confirmation message.
		 *
		 * ## EXAMPLES
		 *
		 *     nvm products delete
		 *     nvm products delete --yes
		 */
		public function __invoke( $args, $assoc_args ) {
			WP_CLI::confirm( 'Are you sure you want to delete all products?', $assoc_args );

			$query       = new WC_Product_Query(
				array(
					'limit'  => -1,
					'return' => 'ids',
				)
			);
			$product_ids = $query->get_products();

			foreach ( $product_ids as $product_id ) {
				wp_delete_post( $product_id, true );
				WP_CLI::log( 'Deleted product ID: ' . $product_id );
			}

			WP_CLI::success( 'Completed deleting all products.' );
		}
	}
}

WP_CLI::add_command( 'nvm products delete', 'Delete_All_Products_Command' );
