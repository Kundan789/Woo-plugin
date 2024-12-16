<?php

class Auction_Product {

    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_auction_price_field'));
        add_action('woocommerce_process_product_meta', array($this, 'save_auction_price_field'));
        add_action('woocommerce_single_product_summary', array($this, 'display_offer_form'), 30);
    }

    // Add custom auction price field
    public function add_auction_price_field() {
        woocommerce_wp_text_input(array(
            'id' => '_auction_price',
            'label' => __('Auction Price', 'tns_auction'),
            'desc_tip' => true,
            'description' => __('Enter the starting price for auction offers.', 'tns_auction'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => 0,
                'step' => 'any'
            )
        ));
    }

    // Save the auction price field
    public function save_auction_price_field($post_id) {
        $auction_price = isset($_POST['_auction_price']) ? sanitize_text_field($_POST['_auction_price']) : '';
        update_post_meta($post_id, '_auction_price', $auction_price);
    }

    // Display the offer form on product page
    public function display_offer_form() {
        global $post;
        $auction_price = get_post_meta($post->ID, '_auction_price', true);
        if ($auction_price) {
            echo '<h3>' . __('Make an Offer', 'tns_auction') . '</h3>';
            echo '<form action="" method="post">';
            echo '<label for="offer_price">' . __('Your Offer', 'tns_auction') . ':</label>';
            echo '<input type="number" id="offer_price" name="offer_price" min="' . $auction_price . '" step="any" required>';
            echo '<button type="submit" name="submit_offer">' . __('Submit Offer', 'tns_auction') . '</button>';
            echo '</form>';
        }
    }
}
