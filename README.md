## WooCommerce New Customer Report

This plugin adds a new report under **WooCommerce &gt; Reports &gt; Customers** to track new vs returning customers for a given date range. This lets you more accurately count metrics for new customers such as **customer acquisition cost**.

The plugin is translation-ready, and the text domain is: `woocommerce-new-customer-report`

### How are New vs. Returning customers calculated?

If a customer has made a purchase prior to the date range start, s/he is counted as a returning customer, or a new customer if not.

This is also calculated on a "per group" basis (the bars in the bar graph). For example, if looking at a 7 day view, let's say this has happened:

 - Customer A purchased Monday for the first time
 - Customer B purchased Tuesday for the 5th time
 - Customer A purchased Friday again for the 2nd time

The report will show you **one** new customer (on Monday), and **two** returning customers (one Tuesday, one Friday), as customer A will be counted as both a new and returning customer.

New customer count for a time period will always show the number of people who have made a **first purchase** in this time period accurately, while returning customer count may include new customers who also come back to make a repeat purchase.

### Contributing

We're happy to accept contributions! Feel free to add an issue or submit a PR :) Please follow WordPress code standards in your commits.

### Helpful Links

 - The [screenshots](/skyverge/woocommerce-new-customer-report/tree/master/screenshots) will show you the plugin in action.
 - Found it useful? We love hearing feedback, and we always [appreciate donations](https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=paypal@skyverge.com&item_name=Donation+for+WooCommerce+New+Customer+report) to fund more free development.

### License

This plugin is licensed under the GPL v3: [GNU General Public License v3.0](http://www.gnu.org/licenses/gpl-3.0.html)
