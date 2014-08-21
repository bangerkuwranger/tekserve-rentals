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
	wp_reset_query();
	$delivery = get_post_meta($postid, 'tekserverentals_request_delivery', true );
	$pickup = get_post_meta($postid, 'tekserverentals_request_pickup', true );
	$cust_data = '<div class="tekserve_rental_request_customer">';
	if ( $company = get_post_meta($postid, 'tekserverentals_request_company', true) ) {
		$cust_data .= "<div><b>COMPANY</b><br />" . $company . "</div>";
	}
	$cust_data .= "<div><b>ADDRESS</b><br />". get_post_meta($postid, 'tekserverentals_request_address', true) . "<br />";
	$cust_data .= get_post_meta($postid, 'tekserverentals_request_city', true) . ", " . get_post_meta($postid, 'tekserverentals_request_state', true) . " " . get_post_meta($postid, 'tekserverentals_request_zip', true);
	$cust_data .= "</div>";
	if ( $phone = get_post_meta($postid, 'tekserverentals_request_phone', true) ) {
		$phone = substr( $phone, 0, 3 ) . "." . substr( $phone, 3, 3 ) . "." . substr( $phone, 6 );
		$cust_data .= "<div><b>PHONE</b><br />" . $phone . "</div>";
	}
	if ( $email = get_post_meta($postid, 'tekserverentals_request_email', true) ) {
		$cust_data .= "<div><b>EMAIL</b><br />" . $email . "</div>";
	}
	$cust_data .= "</div>";
	$delivery_data = "";
	if ( ( $delivery ) OR ( $pickup ) ) {
		$delivery_data = '<div class="tekserve_rental_request_delivery"><h3>Shipping:</h3><div>';
		if ( $delivery ) {
			$delivery_data .= '<b>DELIVERED TO</b><br />';
			$dloc = ( get_post_meta( $postid, 'tekserverentals_delivery_loc', true ) == 'manhattan' ) ? 'Manhattan (below 96th St.)' : 'Bronx, Brooklyn, Manhattan above 96th, Queens, or SI';
			$delivery_data .= $dloc;
			$delivery_data .= '<br />';
		}
		if ( $pickup ) {
			$delivery_data .= '<b>PICKED UP IN</b><br />';
			$ploc = ( get_post_meta( $postid, 'tekserverentals_pickup_loc', true ) == 'manhattan' ) ? 'Manhattan (below 96th St.)' : 'Bronx, Brooklyn, Manhattan above 96th, Queens, or SI';
			$delivery_data .= $ploc;
			$delivery_data .= '<br />';
		}
		$delivery_data .= '</div></div>';
	}
	$request_data1 = '<div class="tekserve_rental_request_container">';
	$request_data1 .= '<h2>Your Info</h2>';
	$request_data1 .= '<div class="tekserve_rental_request_your_info">' . $cust_data . '</div>';
	$request_data1 .= '<h2>Rental Info</h2><div class="tekserve_rental_request_rental_info">';
	$request_data1 .= '<div class="tekserve_rental_request_line_items">';
	$request_data1 .= '<h3>Equipment:</h3>';
	echo $request_data1;
	$line_items_total = display_tekserverentals_request_line_items( $postid, array('output' => 'yes') );
	$request_data2 = '</div>';//line items
	$totals = '<div class="tekserve_rental_request_totals"><h3>Totals:</h3><div>';
	$totals .= '<b>EQUIPMENT</b><br />$' . esc_html( round( ltrim( $line_items_total, "$" ), 2 ) ) . '<br />';
	$totals .= '<b>SHIPPING</b><br />$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_shipping', true), "$" ), 2 ) ) . '<br />';
	$totals .= '<b>TAX</b><br />$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_tax', true), "$" ), 2 ) ) . '<br />';
	$totals .= '<b>DEPOSIT</b><br />$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_deposits', true), "$" ), 2 ) ) . '<br />';
	$totals .= '<h4><b>TOTAL</b><br />$' . esc_html( round( ltrim( get_post_meta($postid, 'tekserverentals_request_total', true), "$" ), 2 ) ) . '</h4>';
	$totals .= '</div></div>';
	$request_data2 .= $delivery_data;
	$request_data2 .= $totals;
	$request_data2 .= '</div>';//request info
	$request_data2 .= '</div>';//container
	echo $request_data2;
	
}

//* Remove note about customer notes from frontend display */
add_filter( 'the_content', 'tekserve_rental_filter_content' );
function tekserve_rental_filter_content($value) {
	$value = explode( '-<b>Original Notes from Customer</b>', $value );
	$notecontent = ( strlen( $value[0] ) < 6 ) ? 'No notes entered.' : $value[0];
	$notes = '<h2 style="font-size: 1.5em; color: #f36f37; text-transform: uppercase; margin-left: 1em;">Additional Notes:</h2><div class="tekserve_rental_request_notes">' . $notecontent . '</div>';
	return $notes;
}

//* Output Message after form submission above the title */
add_action( 'genesis_before_post_title', 'tekserve_rentals_output_thanks' );
function tekserve_rentals_output_thanks() {
//Should have an option in settings to change this text from admin, along with global tax rate, shipping rules, etc. For now, hardcoded.
$thankshead = '<h1>' . get_option('tekserve_rentals_ty_title', 'Thank You!') . '</h1>';
$thanksbody = get_option('tekserve_rentals_ty_body', '<p style="margin-bottom: 1em;">Your request has been sent to Tekserve\'s Rental Department folks, who will take a gander at it and contact you shortly. We just need a bit of time to make sure everything will be ready and waiting for you wherever you need it, and then we can set up your deposit and complete your reservation.</p><p style="margin-bottom: 2.5em;">Take a look below and make sure everything is set up the way you want it; if you need to make changes, just send us a quick email at rentals@tekserve.com, or call us up at 212.929.3645 and we\'ll help you out. Talk you you soon!</p>');
$thanks = '<div class="tekserve_rentals_thank_you">' . $thankshead . $thanksbody . '</div>';
echo $thanks;
}

/** Remove Post Info */
remove_action( 'genesis_after_post_title', 'genesis_post_meta' );
 
genesis();