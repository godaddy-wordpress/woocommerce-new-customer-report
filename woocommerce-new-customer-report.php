<?php
/**
 * Plugin Name: WooCommerce New Customer Report
 * Plugin URI: http://skyverge.com/product/woocommerce-new-customer-report/
 * Description: Provides reporting on new customers vs returning customers for a given date range
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com/
 * Version: 1.2.0-dev.1
 * Text Domain: woocommerce-new-customer-report
 *
 * GitHub Plugin URI: skyverge/woocommerce-new-customer-report
 * GitHub Branch: master
 *
 * Copyright: (c) 2016-2023 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-New-Customer-Report
 * @author    SkyVerge
 * @category  Admin
 * @copyright Copyright (c) 2016-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * WC requires at least: 3.9.4
 * WC tested up to: 7.8.0
 */

defined( 'ABSPATH' ) or exit;

// WC version check
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || version_compare( get_option( 'woocommerce_db_version' ), '2.4.0', '<' ) ) {

	function wc_new_customer_report_outdated_version_notice() {

		$message = sprintf(
		/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
			esc_html__( '%1$sWooCommerce New Customer Report is inactive.%2$s This plugin requires WooCommerce 2.4 or newer. Please %3$supdate WooCommerce to version 2.4 or newer%4$s.', 'woocommerce-new-customer-report' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);

		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
	}

	add_action( 'admin_notices', 'wc_new_customer_report_outdated_version_notice' );
	return;
}


/**
 * Plugin Description
 *
 * WooCommerce New Customer Report adds a report to the WooCommerce > Reports > Customer section.
 *
 * This report tracks whether a customer is new vs returning based on whether the billing email
 *  has been used or not for an order before the start of the selected date range.
 */


if ( ! class_exists( 'WC_New_Customer_Report' ) ) :

// fire it up!
add_action( 'plugins_loaded', 'wc_new_customer_report' );

/**
 * Sets up the plugin and loads the reporting class
 *
 * @since 1.0.0
 */
class WC_New_Customer_Report {


	const VERSION = '1.2.0-dev.1';

	/** @var WC_New_Customer_Report single instance of this plugin */
	protected static $instance;


	/**
	 * WC_New_Customer_Report constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load translations
		add_action( 'init', array( $this, 'load_translation' ) );

		// any frontend actions

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// any admin actions
			add_filter( 'woocommerce_admin_reports', array( $this, 'add_reports' ) );

			// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );

			// run every time
			$this->install();

		}


		// handle HPOS compatibility
		add_action( 'before_woocommerce_init', [ $this, 'handle_hpos_compatibility' ] );
	}


	/**
	 * Declares HPOS compatibility.
	 *
	 * @since 1.2.0-dev.1
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function handle_hpos_compatibility()
	{
		if ( class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
		}
	}


	/** Plugin methods ***************************************/


	/**
	 * Adds a 'New vs Returning' report to the 'Customers' tab with associated reports
	 * to the WC admin reports area
	 *
	 * @since 1.0.0
	 * @param array $core_reports
	 * @return array the updated reports
	 */
	public function add_reports( $core_reports ) {

		$customer_reports = array(
			'new_customers' => array(
				'title'       => __( 'New vs. Returning', 'woocommerce-new-customer-report' ),
				'description' => '',
				'hide_title'  => true,
				'function'    => array( $this, 'load_report' ),
			),
		);

		// add new customer report
		if ( isset( $core_reports['customers']['reports'] ) ) {
			$core_reports['customers']['reports'] = array_merge( $core_reports['customers']['reports'], $customer_reports );
		}

		return $core_reports;
	}


	/**
	 * Callback to load and output the given report
	 *
	 * @since 1.0.0
	 * @param string $name report name, as defined in the add_reports() array above
	 */
	public function load_report( $name ) {

		$report = require_once( 'includes/class-wc-new-customer-report.php' );
		$report->output_report();
	}


	/** Helper methods ***************************************/


	/**
	 * Main WC_New_Customer_Report Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.0.0
	 * @see wc_new_customer_report()
	 * @return WC_New_Customer_Report
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'woocommerce-new-customer-report' ), 'WooCommerce New Customer Report' ), '1.1.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.1.0
	 */
	public function __wakeup() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'woocommerce-new-customer-report' ), 'WooCommerce New Customer Report' ), '1.1.0' );
	}

	/**
	 * Adds plugin page links
	 *
	 * @since 1.0.0
	 * @param array $links all plugin links
	 * @return array $links all plugin links + our custom links (i.e., "Settings")
	 */
	public function add_plugin_links( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-reports&tab=customers&report=new_customers' ) . '">' . __( 'View Report', 'woocommerce-new-customer-report' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}


	/**
	 * Load Translations
	 *
	 * @since 1.0.0
	 */
	public function load_translation() {
		// localization
		load_plugin_textdomain( 'woocommerce-new-customer-report', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
	}


	/** Lifecycle methods ***************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 1.0.0
	 */
	private function install() {

		// get current version to check for upgrade
		$installed_version = get_option( 'wc_new_customer_report_version' );

		// force upgrade to 1.0.0
		if ( ! $installed_version ) {
			$this->upgrade( '1.0.0' );
		}

		// upgrade if installed version lower than plugin version
		if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			$this->upgrade( self::VERSION );
		}

	}


	/**
	 * Perform any version-related changes.
	 *
	 * @since 1.0.0
	 * @param int $installed_version the currently installed version of the plugin
	 */
	private function upgrade( $version ) {

		// update the installed version option
		update_option( 'wc_new_customer_report_version', $version );
	}


}


/**
 * Returns the One True Instance of WC_New_Customer_Report
 *
 * @since 1.0.0
 * @return WC_New_Customer_Report
 */
function wc_new_customer_report() {
	return WC_New_Customer_Report::instance();
}

endif;
