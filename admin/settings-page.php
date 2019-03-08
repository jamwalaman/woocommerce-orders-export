<?php // MyPlugin - Settings Page


// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

use Automattic\WooCommerce\Client;


function td_html($order_property, $list_item = false, $money = false) {

	if ( !empty($order_property) ) {
		if (!$list_item) {
			// html for when order property is not a list item
			$table_data_html = '<td><p>' . $order_property . '</p></td>';
		} else {
			// html for when order property is a list item
			$table_data_html =  '<p><b>' . $list_item . ': </b> <br>';
			if($money) {
				$table_data_html .= $money . $order_property . '</p>';
			} else {
				$table_data_html .= $order_property . '</p>';
			}
		}
	} elseif ( empty($order_property) && !$list_item ) {
		// html for when there is no value in order property. creates an empty td tag
		$table_data_html = '<td></td>';
	}

	return $table_data_html;

}

// display the plugin settings page
function myplugin_display_settings_page() {

	
	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;
	
	?>

	<div class='wrap'>

		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<?php

		$woocommerce = new Client($GLOBALS['home_url'], $GLOBALS['consumer_key'], $GLOBALS['consumer_secret']);

		// all the orders stored in the $orders variable
		$orders = $woocommerce->get('orders');

		// convert $orders into json
		$orders_json =  json_encode($orders);

		// convert $orders_json to an object
		$orders_object = json_decode($orders_json);

		?>


		<p style="margin-bottom: 20px">
			<button class="button button-primary" onclick="generate()">Download orders pdf</button>
			<button class="button button-primary" onclick="fewercols()">Download summary pdf</button>
		</p>

		<table id="orders_table">
			<thead>
				<tr>
					<?php
					$table_header_array = array('booking', 'order', 'total', 'billing', 'payment method', 'meta data', 'coupon', 'refunds');
					foreach($table_header_array as $th) {
						echo '<th>' . ucfirst($th) . '</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($orders_object as $order) {
					echo '<tr class="table_row">';
					// BOOKING COL //
					echo '<td>';
					foreach ($order->line_items as $line_item) {
						echo '<p><b> name: </b> ' . $line_item->name . ', <b> quantity: </b>' . $line_item->quantity . '</p>';
					}
					echo '</td>';
					// ORDER COL //
					echo '<td>';
					// id
					$link = $home_url . '/wp-admin/post.php?post=' . $order->id . '&action=edit';
					echo '<p><b> id: </b><br> <a target="_blank" href="'. $link . '">' . $order->id . '</a></p>';
					// status
					echo td_html($order->status, 'status');
					// date created
					$d_created = strtotime($order->date_created);
					echo '<p><b> Date created: </b><br>' . date("d/m/Y h:i:sa", $d_created) . '</p>';
					// date modified
					$d_modified = strtotime($order->date_modified);
					echo '<p><b> Date modified: </b><br>' . date("d/m/Y h:i:sa", $d_modified) . '</p>';
					echo '</td>';
					// TOTAL COL //
					echo '<td>';
					echo td_html($order->discount_total, 'Discount total', '$');
					echo td_html($order->total, 'Total','$');
					echo '</td>';
					// BILLING COL //
					echo '<td>';
					echo td_html($order->billing->first_name, 'First name');
					echo td_html($order->billing->last_name, 'Last name');
					echo td_html($order->billing->address_1, 'Address1');
					echo td_html($order->billing->address_2, 'Address2');
					echo td_html($order->billing->city, 'City');
					echo td_html($order->billing->postcode, 'Postcode');
					echo td_html($order->billing->email, 'Email');
					echo td_html($order->billing->phone, 'Phone');
					echo '</td>';
					// PAYMENT METHOD COL
					echo td_html($order->payment_method_title);
					// META DATA COL
					echo '<td>';
					foreach($order->meta_data as $meta_data) {
						if ( !property_exists($meta_data->value, 'enabled') && substr($meta_data->key,0, 1)!=='_' ) {
							$key = ucfirst($meta_data->key);
							if ($key === 'Name-age-participants') {
								$key = 'Participants';
							}
							echo '<p><b>';
							$key_no_dash = explode('-', $key);
							foreach ($key_no_dash as $k) {
								echo $k . ' ';
							}
							echo ': </b><br>' . $meta_data->value . '</p>';
						}
					}
					echo '</td>';
					// coupon
					echo '<td>';
					foreach ($order->coupon_lines as $coupon_lines) {
						foreach ($coupon_lines->meta_data as $coupon_meta_data) {
							echo td_html($coupon_meta_data->value->amount, 'amount','$');
							echo td_html($coupon_meta_data->value->description, 'description');
						}
					}
					echo '</td>';
					// refunds
					echo '<td>';
					foreach ($order->refunds as $refund) {
						echo td_html($refund->reason, 'reason');
						echo td_html($refund->total, 'total','$');
					}
					echo '</td>';

					echo '</tr>';
				}
				?>
			</tbody>
		</table>

		<script src="https://unpkg.com/jspdf@1.5.3/dist/jspdf.min.js"></script>
		<script src="https://unpkg.com/jspdf-autotable@3.0.10/dist/jspdf.plugin.autotable.js"></script>

	</div>


	<?php
}
