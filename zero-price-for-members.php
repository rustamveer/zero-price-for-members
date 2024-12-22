<?php
/*
Plugin Name: Zero Price for Members
Plugin URI: https://github.com/rustamveer/zero-price-for-members.git
Description: Sets WooCommerce product prices to zero for members.
Version: 1.1
Author: Rustamveer Singh
Author URI: https://lampp.io
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ZeroPriceForMembers {
    
    const MEMBER_ROLE = 'm2m_members';
    
    public function __construct() {
        // Hook into WooCommerce filters for modifying prices
        add_filter('woocommerce_get_price_html', [$this, 'modify_price_display'], 10, 2);
        add_filter('woocommerce_product_get_price', [$this, 'modify_product_price'], 10, 2);
        add_filter('woocommerce_product_get_regular_price', [$this, 'modify_product_price'], 10, 2);

        // Hook into the cart calculation process
        add_action('woocommerce_before_calculate_totals', [$this, 'modify_cart_prices'], 10, 1);
    }

    /**
     * Check if the user is eligible for zero prices
     *
     * @return bool
     */
    private function is_member() {
        return is_user_logged_in() && current_user_can(self::MEMBER_ROLE);
    }

    /**
     * Modify the product price display
     *
     * @param string $price HTML price string
     * @param WC_Product $product WooCommerce product object
     * @return string
     */
    public function modify_price_display($price, $product) {
        if ($this->is_member()) {
            $price = wc_price(0); // Format the price to zero
        }
        return $price;
    }

    /**
     * Modify the product price in the backend logic
     *
     * @param float $price Product price
     * @param WC_Product $product WooCommerce product object
     * @return float
     */
    public function modify_product_price($price, $product) {
        if ($this->is_member()) {
            $price = 0.0;
        }
        return $price;
    }

    /**
     * Set cart item prices to zero for members
     *
     * @param WC_Cart $cart WooCommerce cart object
     */
    public function modify_cart_prices($cart) {
        if ($this->is_member()) {
            foreach ($cart->get_cart() as $cart_item) {
                $cart_item['data']->set_price(0.0);
            }
        }
    }
}

// kickoff the plugin
new ZeroPriceForMembers();
