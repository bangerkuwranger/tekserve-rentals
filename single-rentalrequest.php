<?php
 
/**
 * Template Name: Tekserve Rental Reques - Single
 * Description: Used as a page template to display a single rental request after the form is filled out for the requester.
 */
 
//* Customize the post info function to display custom fields */
add_action( 'genesis_after_post_title', 'tekserve_rentals_output_request' );
function tekserve_rentals_output_request() {
global $wp_query;
$postid = $wp_query->post->ID;
$delivery = get_post_meta($postid, 'tekserverentals_request_delivery', true );
$pickup = get_post_meta($postid, 'tekserverentals_request_pickup', true );
$cust_data = '<div class="tekserve_rental_request_customer">';
if ( $company = get_post_meta($postid, 'tekserverentals_request_company', true) ) {
	$cust_data .= "<div><b>COMPANY</b><br/>" . $company . "</div>";
}
$cust_data .= "<div><b>ADDRESS</b><br/>". get_post_meta($postid, 'tekserverentals_request_address', true) . "<br/>";
$cust_data .= get_post_meta($postid, 'tekserverentals_request_city', true) . ", " . get_post_meta($postid, 'tekserverentals_request_state', true) . " " . get_post_meta($postid, 'tekserverentals_request_zip', true);
$cust_data .= "</div>";
if ( $phone = get_post_meta($postid, 'tekserverentals_request_phone', true) ) {
	$cust_data .= "<div><b>PHONE</b><br/>" . $phone . "</div>";
}
if ( $email = get_post_meta($postid, 'tekserverentals_request_email', true) ) {
	$cust_data .= "<div><b>EMAIL</b><br/>" . $email . "</div>";
}
$cust_data .= "</div>";
$delivery_data = "";
if ( ( $delivery ) OR ( $pickup ) ) {
	$delivery_data = '<div class="tekserve_rental_request_delivery"><h3>Messenger Requested:</h3>';
	if ( $delivery ) {
		$delivery_data .= '<b>DELIVERED TO</b><br/>' . get_post_meta($postid, 'tekserverentals_delivery_loc', true ) . '<br/>';
	}
	if ( $pickup ) {
		$delivery_data .= '<b>PICKED UP IN</b><br/>' . get_post_meta($postid, 'tekserverentals_pickup_loc', true ) . '<br/>';
	}
	$delivery_data .= '</div>';
}
$totals = '<div class="tekserve_rental_request_delivery"><h3>Totals:</h3>';
$totals .= '<b>SHIPPING</b><br/>$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_shipping', true), "$" ), 2 ) ) . '<br/>';
$totals .= '<b>TAX</b><br/>$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_tax', true), "$" ), 2 ) ) . '<br/>';
$totals .= '<b>DEPOSIT</b><br/>$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_deposits', true), "$" ), 2 ) ) . '<br/>';
$totals .= '<h4><b>TOTAL</b><br/>$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_total', true), "$" ), 2 ) ) . '</h4>';
$totals .= '</div>';
$line_items = '<h3>Line Items:</h3>';
$line_items .= display_tekserverentals_request_line_items($postid, true);
wp_reset_query();
$request_data = '<div class="tekserve_rental_request_container"><div class="tekserve_rental_request">';
$request_data .= '<h2>Your Info</h2>';
$request_data .= $cust_data;
$request_data .= '<h2>Rental Info</h2>';
$request_data .= $delivery_data;
$request_data .= $totals;
$request_data .= '</div>';//request info
$request_data .= '<div class="tekserve_rental_request_line_items">';
$request_data .= $line_items;
$request_data .= '</div>';//line items
$request_data .= '</div>';//container
echo $request_data;
}

//* Remove note about customer notes from frontend display */
add_filter( 'the_content', 'tekserve_rental_filter_content' );
function tekserve_rental_filter_content($value) {
	$value = explode( '-<b>Original Notes from Customer</b>', $value );
	$notes = '<h3>Additional Notes:</h3>' . $value[0];
	return $notes;
}

//* Output Message after form submission above the title */
add_action( 'genesis_before_post_title', 'tekserve_rentals_output_thanks' );
function tekserve_rentals_output_thanks() {
//Should have an option in settings to change this text from admin, along with global tax rate, shipping rules, etc. For now, hardcoded.
$thanks = '<h1>Thank You!</h1><p style="margin-bottom: 1em;">Your request has been sent to Tekserve\'s Rental Department folks, who will take a gander at it and contact you shortly. We just need a bit of time to make sure everything will be ready and waiting for you wherever you need it, and then we can set up your deposit and complete your reservation.</p><p style="margin-bottom: 2.5em;">Take a look below and make sure everything is set up the way you want it; if you need to make changes, just send us a quick email at rentals@tekserve.com, or call us up at 212.929.3645 and we\'ll help you out. Talk you you soon!</p>';
echo $thanks;
}

/** Remove Post Info */
remove_action( 'genesis_after_post_title', 'genesis_post_meta' );
 
genesis();