<?php

class Bid_Handler {

    public function __construct() {
        add_action('init', array($this, 'process_bid'));
        add_action('woocommerce_after_single_product', array($this, 'handle_offer_submission'));
    }

    // Process the offer submission
    public function process_bid() {
        if (isset($_POST['submit_offer']) && isset($_POST['offer_price']) && is_numeric($_POST['offer_price'])) {
            $offer_price = sanitize_text_field($_POST['offer_price']);
            $product_id = get_the_ID();
            $user_id = get_current_user_id();

            if ($offer_price < 0) {
                return;
            }

            // Save the offer to the database
            $this->save_offer($product_id, $user_id, $offer_price);
            $this->send_offer_email($product_id, $user_id, $offer_price);
        }
    }

    // Save the offer to the database (can be extended for more complex tracking)
    public function save_offer($product_id, $user_id, $offer_price) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'tns_auction_offers';

        // Create table if it doesn't exist
        $charset_collate = $wpdb->get_charset_collate();
        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS $table_name (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                offer_price DECIMAL(10, 2) NOT NULL,
                offer_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;"
        );

        // Insert the offer
        $wpdb->insert(
            $table_name,
            array(
                'product_id' => $product_id,
                'user_id' => $user_id,
                'offer_price' => $offer_price
            )
        );
    }

    // Send email notification about the offer
    public function send_offer_email($product_id, $user_id, $offer_price) {
        $product = wc_get_product($product_id);
        $user = get_user_by('id', $user_id);
        $admin_email = get_option('admin_email');
        $subject = __('New Offer on Your Product', 'tns_auction');
        $message = sprintf(
            __('A new offer has been made for your product %s. Offer Price: %s. Offer made by: %s', 'tns_auction'),
            $product->get_name(),
            $offer_price,
            $user->display_name
        );

        wp_mail($admin_email, $subject, $message);
    }

    // Handle offer form submission
    public function handle_offer_submission() {
        if (isset($_POST['submit_offer'])) {
            echo '<p>' . __('Your offer has been submitted successfully. The seller will review it shortly.', 'tns_auction') . '</p>';
        }
    }
}
