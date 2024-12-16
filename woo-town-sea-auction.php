<?php
/*
Plugin Name: Town and Sea Auction
Description: A plugin to handle auction-based product offers, negotiations, and email notifications.
Version: 1.0
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-auction-product.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-bid-handler.php';

// Initialize plugin functionality
function tns_auction_plugin_init() {
    new Auction_Product();
    new Bid_Handler();
}

add_action('plugins_loaded', 'tns_auction_plugin_init');
