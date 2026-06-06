<?php


namespace {
	// EDD ConvertKit constants
	if (!defined('EDD_CONVERTKIT_VERSION')) {
		define('EDD_CONVERTKIT_VERSION', '1.0.10');
	}
}
namespace EDD\Newsletter {
    interface Settings
    {
        /**
         * Registers the plugin settings.
         *
         * @since 1.0
         * @param array $settings
         * @return array
         */
        public function settings($settings);
        /**
         * Registers the plugin subsection.
         *
         * @since 1.0
         * @param array $sections
         * @return array
         */
        public function subsection($sections);
    }
    interface Subscribe
    {
        /**
         * Subscribe a customer to a list.
         *
         * @since 1.0
         * @param array $user_info An array containing the user info: generally `email`, `first_name`, `last_name`.
         *                         An extension may change the array to meet its specific requirements.
         * @param int   $list_id   The list ID to which the user should be subscribed. If false, the method
         *                         should use `get_primary_list` to sign the user up for the primary list.
         *
         */
        public function subscribe_email($user_info = array(), $list_id = false);
    }
    abstract class Base implements \EDD\Newsletter\Settings, \EDD\Newsletter\Subscribe
    {
        /**
         * The ID for this newsletter extension.
         * This is defined in `get_id` during `init`.
         *
         * @since 1.0
         */
        public $id;
        /**
         * The current instance of the class.
         *
         * @var Base
         * @since 1.0
         */
        protected static $instance;
        /**
         * Gets the id for the extension.
         *
         * @since 1.0
         * @return string
         */
        protected function get_id() {}
        /**
         * Gets the formal label for the extension.
         *
         * @since 1.0
         * @return string
         */
        protected function get_label() {}
        /**
         * Retrieves the newsletter lists.
         *
         * Must return an array like this:
         *   array(
         *     'some_id'  => 'value1',
         *     'other_id' => 'value2'
         *   )
         *
         * @since 1.0
         */
        public function get_lists() {}
        /**
         * Returns the class instance.
         * Ensures that only one instance of \EDD\Newsletter\Base and the extending classes exist in memory at any one time.
         *
         * @since 1.0
         * @return static
         */
        public static function instance()
        {
        }
        /**
         * Initializes the plugin with default functions.
         *
         * @since 1.0
         * @return void
         */
        public function init()
        {
        }
        /**
         * Initialize settings hooks.
         * This is run during admin_init to ensure EDD settings are fully loaded.
         *
         * @since 1.0.10
         * @return void
         */
        public function init_settings()
        {
        }
        /**
         * Load the plugin's textdomain.
         *
         * @since 1.0
         * @return void
         */
        public function textdomain()
        {
        }
        /**
         * Gets the settings screen URL.
         *
         * @since 1.0
         * @return string
         */
        protected function get_settings_url()
        {
        }
        /**
         * Gets the settings tab.
         *
         * Fixed to use a version check only, avoiding early call to edd_get_settings_tabs()
         *
         * @since 1.0
         * @return string
         */
        protected function get_settings_tab()
        {
        }
        /**
         * Output the signup checkbox on the checkout screen, if enabled.
         *
         * @since 1.0
         * @return void
         */
        public function checkout_fields()
        {
        }
        /**
         * Gets the checkout label setting.
         *
         * @since 1.0
         * @return string
         */
        protected function get_checkout_label()
        {
        }
        /**
         * Check if a customer needs to be subscribed at checkout.
         *
         * @since 1.0
         * @return void
         */
        public function check_for_email_signup($payment_id = 0, $payment_data = array())
        {
        }
        /**
         * Check if a customer needs to be subscribed on completed purchase for the default list and of specific products.
         *
         * @since 1.0
         * @param int           $payment_id        The Payment ID being completed.
         * @param \EDD_Payment  $payment   The EDD_Payment object of the payment being completed.
         * @param \EDD_Customer $customer The customer being processed.
         */
        public function subscribe_to_lists($payment_id, \EDD_Payment $payment, \EDD_Customer $customer)
        {
        }
        /**
         * Register the metabox on the 'download' post type.
         *
         * @since 1.0
         * @param \WP_Post $post The post object.
         * @return void
         */
        public function add_metabox($post)
        {
        }
        /**
         * Display the metabox, which is a list of newsletter lists.
         *
         * @since 1.0
         * @param \WP_Post $post The post object.
         * @return void
         */
        public function render_metabox($post)
        {
        }
        /**
         * Save the metabox.
         *
         * @since 1.0
         * @param array $fields
         * @return array
         */
        public function save_metabox($fields)
        {
        }
        /**
         * Gets the string for the post meta key.
         *
         * @since 1.0
         * @return string
         */
        protected function get_post_meta_key()
        {
        }
        /**
         * Gets the primary list ID.
         *
         * @since 1.0
         * @return bool|int|string
         */
        protected function get_primary_list()
        {
        }
        /**
         * Whether the signup option should be shown on checkout.
         *
         * @since 1.0
         * @return bool
         */
        protected function show_signup_on_checkout()
        {
        }
        /**
         * Whether the signup checkbox should be checked by default on checkout.
         *
         * @since 1.0
         * @return bool
         */
        protected function is_signup_checked_by_default()
        {
        }
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
}
namespace {
    /**
     * Plugin Name: Easy Digital Downloads - ConvertKit
     * Plugin URI: https://easydigitaldownloads.com/downloads/convertkit
     * Description: Subscribe your customers to ConvertKit forms during purchase.
     * Version: 1.0.10
     * Author: Easy Digital Downloads
     * Author URI: https://easydigitaldownloads.com/
     */
}