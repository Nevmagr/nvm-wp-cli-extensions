# Nevma extend WP-CLI for WooCommerce Plugin

Nevma extend wp-cli for WooCommerce Plugin
Description

Please backup before making any use of it!

This plugin extends WooCommerce capabilities by introducing new command-line functionalities. It allows for efficient management and manipulation of WooCommerce products through WP-CLI commands.
Features

    Delete All Products: Quickly delete all products from your WooCommerce store.
    Export Products to CSV: Export products to a CSV file with filtering options.
    Set Products as 'Out of Stock': Mark products as 'out of stock', with filtering capabilities.
    Update Products from CSV: Update product details, including stock and prices, from a CSV file.

Installation

    Clone the plugin into your WordPress plugins directory.
    Navigate to the WordPress admin panel and activate the plugin.

Usage

## Delete All Products

Deletes all products from the WooCommerce store.
Options

    --yes: Automatically confirm the deletion process.

Examples

bash

nvm products delete
nvm products delete --yes

## Export Products to CSV

Exports products to a CSV file with optional filters.
Options

    --att=<attribute>: Filter by attribute (e.g., 'pa_manufacturer').
    --slug=<slug>: Filter by the attribute's slug.
    --sku=<sku_prefix>: Filter by SKU prefix.
    <file>: Path for saving the CSV file.

Examples

bash

wp nvm products export_csv --att=pa_manufacturer --slug=brand-slug --sku=SKU123 /path/to/export.csv
wp nvm products export_csv /path/to/export.csv

## Set Products as 'Out of Stock'

Marks products as 'out of stock', with filtering options.
Options

    --att=<attribute>: Filter by attribute (e.g., 'pa_color').
    --slug=<slug>: Filter by the attribute's slug.
    --sku=<sku_prefix>: Filter by SKU prefix.
    --all: Mark all products as 'out of stock'.

Examples

bash

wp nvm set*outofstock --att=pa_color --slug=red
wp nvm set_outofstock --sku=SKU123*

## Update Products from CSV

Updates product details from a CSV file.
Options

    <file>: Path to the CSV file.
    --update_stock: Update the stock status.
    --update_price: Update regular and sale prices.

Examples

bash

wp nvm products update_csv /path/to/file.csv --update_stock --update_price
wp nvm products update_csv /path/to/file.csv --update_stock

Contributing

We welcome contributions to improve this plugin. Please feel free to submit issues and pull requests.
