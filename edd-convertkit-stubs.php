<?php

namespace {
	// EDD ConvertKit constants
	if (!defined('EDD_CONVERTKIT_VERSION')) {
		define('EDD_CONVERTKIT_VERSION', '1.0.10');
	}
}
namespace {
class EDD_ConvertKit extends \EDD\Newsletter\Base
{
    /**
     * @var EDD_ConvertKit
     */
    protected static $instance;
    /**
     * ConvertKit API Key
     *
     * @var string
     */
    public $api_key;
    /**
     * ConvertKit API Secret
     *
     * @var string
     */
    public $api_secret;
    /**
     * Convert kit account tags
     *
     * @var object
     */
    public $tags;
    /**
     * Gets the ID for the class.
     *
     * @since 1.0.0
     * @return string
     */
    protected function get_id()
    {
    }
    /**
     * Gets the label for the class.
     *
     * @since 1.0.9
     * @return string
     */
    protected function get_label()
    {
    }
    /**
     * Sets up the checkout label
     */
    public function init()
    {
    }
    /**
     * Retrieves the lists from ConvertKit
     */
    public function get_lists()
    {
    }
    /**
     * Retrieve plugin tags
     */
    public function get_tags()
    {
    }
    /**
     * Register our subsection for EDD 2.5
     *
     * @since  1.0.3
     * @param  array $sections The subsections
     * @return array           The subsections with Convertkit added
     */
    public function subsection($sections)
    {
    }
    /**
     * Registers the plugin settings
     */
    public function settings($settings)
    {
    }
    /**
     * Flush the list transient on save
     */
    public function save_settings($input)
    {
    }
    /**
     * Display the metabox, which is a list of newsletter lists
     */
    public function render_metabox($post)
    {
    }
    /**
     * Save the metabox
     *
     * @params array $fields
     */
    public function save_metabox($fields)
    {
    }
    /**
     * Check if a customer needs to be subscribed on completed purchase for the default list and of specific products
     *
     * @param int $payment_id        The Payment ID being completed.
     * @param EDD_Payment $payment   The EDD_Payment object of the payment being completed.
     * @param EDD_Customer $customer The customer being processed.
     */
    public function subscribe_to_lists($payment_id, \EDD_Payment $payment, \EDD_Customer $customer)
    {
    }
    /**
     * Records purchase record for customer after a purchase is completed
     *
     * @param int $payment_id ID of payment
     *
     * @return void
     */
    public function after_purchase($payment_id)
    {
    }
    /**
     * Subscribe an email to a list
     *
     * @param array $user_info User info as returned by edd_get_payment_meta_user_info()
     * @param bool|string $list_id Optional. ID of list to subscribe to. If false, the default, value saved in UI will be used.
     * @param bool $opt_in_overridde Not used
     * @param array $tags Optional. Optional. Array of tags to add to subscriber
     *
     * @return bool
     */
    public function subscribe_email($user_info = array(), $list_id = \false, $tags = array())
    {
    }
}
/**
 * Plugin Name: Easy Digital Downloads - ConvertKit
 * Plugin URI: https://easydigitaldownloads.com/downloads/convertkit
 * Description: Subscribe your customers to ConvertKit forms during purchase.
 * Version: 1.0.10
 * Author: Easy Digital Downloads
 * Author URI: https://easydigitaldownloads.com/
 */
}

namespace EDD\Newsletter {
	class Base {}
}
