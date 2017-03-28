<?php
/**
 * WooCommerce New Customer Report
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce New Customer Report to newer
 * versions in the future.
 *
 * @package     WC-New-Customer-Report/Includes/
 * @author      SkyVerge
 * @copyright   Copyright (c) 2016-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * New Customer Report Admin Report Class
 *
 * Handles generating and rendering the New vs Returning Customers by Date report
 *
 * @since 1.0.0
 */
class WC_New_Customer_Report_Report extends WC_Admin_Report {


	/** @var array define the chart colors for this report */
	protected $chart_colors = array(
		'new_customers'       => '#b1d4ea',
		'returning_customers' => '#87d4a8',
	);

	/** @var stdClass for caching multiple calls to get_report_data() */
	protected $report_data;


	/**
	 * Render the report data, including legend and chart
	 *
	 * @since 1.0.0
	 */
	public function output_report() {

		$current_range = $this->get_current_range();

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		// used in view
		$ranges = array(
			'year'       => __( 'Year', 'woocommerce-cost-of-goods' ),
			'last_month' => __( 'Last Month', 'woocommerce-cost-of-goods' ),
			'month'      => __( 'This Month', 'woocommerce-cost-of-goods' ),
			'7day'       => __( 'Last 7 Days', 'woocommerce-cost-of-goods' )
		);

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}


	/**
	 * Return the currently selected date range for the report
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_current_range() {
		return ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
	}


	/**
	 * Render the "Export to CSV" button
	 *
	 * @since 1.0.0
	 */
	public function get_export_button() {

		$args = array(

			'filename'       => sprintf( '%1$s-report-%2$s-%3$s.csv',
				strtolower( str_replace( array(
					'WC_New_Customer_Report_Report_',
					'_'
				), array( '', '-' ), get_class( $this ) ) ),
				$this->get_current_range(), date_i18n( 'Y-m-d', current_time( 'timestamp' ) ) ),
			'xaxes'          => __( 'Date', 'woocommerce-new-customer-report' ),
			'exclude_series' => '',
			'groupby'        => $this->chart_groupby,
		);

		?>
		<a
			href="#"
			download="<?php echo esc_attr( $args['filename'] ); ?>"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php echo esc_attr( $args['xaxes'] ); ?>"
			data-exclude_series="<?php echo esc_attr( $args['exclude_series'] ); ?>"
			data-groupby="<?php echo esc_attr( $args['groupby'] ); ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce-new-customer-report' ); ?>
		</a>
		<?php
	}


	/**
	 * Get the chart legend data
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_chart_legend() {

		$data = $this->get_report_data();

		return array(

			// new customers
			array(
				/* translators: Placeholders: %1$s is the formatted new customer count with surrounding <strong> tags, e.g. <strong>5</strong> */
				'title'            => sprintf( __( '%1$s new customers', 'woocommerce-new-customer-report' ), '<strong>' . $data->total_new_customers . '</strong>' ),
				'placeholder'      => __( 'Total new customers in this time period.', 'woocommerce-new-customer-report' ),
				'color'            => $this->chart_colors['new_customers'],
				'highlight_series' => 0,
			),

			// returning customers
			array(
				/* translators: Placeholders: %1$s is the formatted returning customer count with surrounding <strong> tags, e.g. <strong>10</strong> */
				'title'            => sprintf( __( '%1$s returning customers', 'woocommerce-new-customer-report' ), '<strong>' . $data->total_returning_customers . '</strong>' ),
				'placeholder'      => __( 'Total returning customers in this time period.', 'woocommerce-new-customer-report' ),
				'color'            => $this->chart_colors['returning_customers'],
				'highlight_series' => 1,
			),
		);
	}


	/**
	 * Render the main chart
	 *
	 * @since 1.0.0
	 */
	public function get_main_chart() {

		$data = $this->get_report_data();

		$new_customers       = $this->prepare_chart_data( $data->customers, 'date', 'new_customers', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$returning_customers = $this->prepare_chart_data( $data->customers, 'date', 'returning_customers', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$chart_data = json_encode( array(
			'new_customers'       => array_values( $new_customers ),
			'returning_customers' => array_values( $returning_customers ),
		) );
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">
			var main_chart;

			jQuery(function(){
				var chart_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );

				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'New Customers', 'woocommerce-new-customer-report' ) ) ?>",
							data: chart_data.new_customers,
							color: '<?php echo esc_js( $this->chart_colors['new_customers'] ); ?>',
							bars: { fillColor: '<?php echo esc_js( $this->chart_colors['new_customers'] ); ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							enable_tooltip: true,
							append_tooltip: "<?php echo esc_js( ' ' . __( 'new customers', 'woocommerce-new-customer-report' ) ); ?>",
							stack: true,
						},
						{
							label: "<?php echo esc_js( __( 'Returning Customers', 'woocommerce-new-customer-report' ) ) ?>",
							data: chart_data.returning_customers,
							color: '<?php echo esc_js( $this->chart_colors['returning_customers'] ); ?>',
							bars: { fillColor: '<?php echo esc_js( $this->chart_colors['returning_customers'] ); ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							enable_tooltip: true,
							append_tooltip: "<?php echo esc_js( ' ' . __( 'returning customers', 'woocommerce-new-customer-report' ) ); ?>",
							stack: true,
						},
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#9c5d90';

						if ( highlight_series.bars )
							highlight_series.bars.fillColor = '#9c5d90';

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 5;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $GLOBALS['wp_locale']->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								tickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#ecf0f1',
									font: { color: "#aaa" }
								}
							],
						}
					);
					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}


	/**
	 * Get the data for the main chart
	 *
	 * @since 1.0.0
	 */
	protected function get_report_data() {

		if ( ! empty( $this->report_data ) ) {
			return $this->report_data;
		}

		$this->report_data = new stdClass();

		$orders = $this->get_order_report_data( array(
			'data'         => array(
				'_billing_email' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'email',
				),
				'post_date'      => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'date'
				),
			),
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'order_types'  => wc_get_order_types( 'sales-reports' ),
			'order_status' => array( 'completed', 'processing', 'on-hold' ),
		) );

		$prior_order_emails = $current_order_emails = array();

		foreach ( $orders as $order ) {

			// don't include future order emails
			if ( strtotime( $order->date ) > $this->end_date ) {
				continue;
			}

			// divide into current vs. prior period
			if ( strtotime( $order->date ) >= $this->start_date ) {

				// current
				$date = date( ( 'day' === $this->chart_groupby ? 'Y-m-d' : 'Y-m' ), strtotime( $order->date ) );

				if ( ! isset( $current_order_emails[ $date ] ) ) {
					$current_order_emails[ $date ] = array();
				}

				// only unique emails for this period
				if ( false === array_search( $order->email, $current_order_emails[ $date ] ) ) {
					$current_order_emails[ $date ][] = $order->email;
				}

			} else {

				// prior

				// only unique emails
				if ( false === array_search( $order->email, $prior_order_emails ) ) {
					$prior_order_emails[] = $order->email;
				}
			}
		}

		$customers = $total_new_customers = $total_returning_customers = array();

		// iterate through current period
		foreach ( $current_order_emails as $date => $emails ) {

			$new_customers = $returning_customers = array();

			// iterate through emails in range
			foreach ( $emails as $email ) {

				// if an email doesn't exist in the set of prior order emails, consider them a new customer
				if ( false === array_search( $email, $prior_order_emails) ) {

					$new_customers[] = $email;
					$total_new_customers[] = $email;

				} else {

					$returning_customers[] = $email;
					$total_returning_customers[] = $email;
				}
			}

			$c = new stdClass();

			$c->date                = $date;
			$c->new_customers       = count( array_unique( $new_customers ) );
			$c->returning_customers = count( array_unique( $returning_customers ) );

			$customers[] = $c;

			// add this date's emails to the prior list so new customers that order again in the same period aren't counted multiple times
			$prior_order_emails = array_merge( $prior_order_emails, $emails );
		}

		$this->report_data->customers = $customers;

		// totals
		$this->report_data->total_new_customers = count( array_unique( $total_new_customers ) );
		$this->report_data->total_returning_customers = count( array_unique( $total_returning_customers ) );

		return $this->report_data;
	}


}


return wc_new_customer_report()->report = new WC_New_Customer_Report_Report();
