<?php 
/* 
Form handler for checkout data. Accepts postdata from simplecart.js and translates to an array that creates a Rental Request entry in wpdb
*/

//start WP
$wp_load = realpath("../../../wp-load.php");
if( !file_exists( $wp_load ) ) {

	$wp_config = realpath("../../../wp-config.php");
	if( !file_exists( $wp_config ) ) {
	
		exit( "Can't find wp-config.php or wp-load.php" );
	
	}	
	else {
	
		require_once( $wp_config );
	
	}	//end if( !file_exists( $wp_config ) )

}
else {

  require_once( $wp_load );

}	//end if( !file_exists( $wp_load ) )



//arrange postdata into usable arrays
$content = $_POST;
$custinfo = array(
	"first_name" 	=> $content["first_name"],
	"last_name" 	=> $content["last_name"],
	"company" 		=> $content["company"],
	"email"			=> $content["email"],
	"phone" 		=> $content["phone"],
	"address" 		=> $content["address_one"]." ".$content["address_two"],
	"city" 			=> $content["city"],
	"state" 		=> $content["state"],
	"zip" 			=> $content["zip"]
);
$orderinfo = array(
	"start" 				=> $content["start_date"],
	"end" 					=> $content["end_date"],
	"durationdays" 			=> $content["duration"],
	"delivery" 				=> $content["delivery"],
	"pickup" 				=> $content["pickup"],
	"where_deliver" 		=> $content["delivery_loc"],
	"where_pickup" 			=> $content["pickup_loc"],
	"deposit_total" 		=> $content["deposits_total"],
	"shipping_total" 		=> $content["shipping"],
	"tax_total" 			=> $content["tax"],
	"total" 				=> $content["total_price"],
	"total_with_deposit" 	=> $content["total_due"],
	"notes_from_cust" 		=> str_replace( 'Â', '', $content["comments"] )
);
$lineitems = array();
$line_item_qty = intval($content["itemCount"]);
for( $i=1; $i<=$line_item_qty; $i++ ) {

	$price_list = $content["item_options_".$i];
	$price_list = stripcslashes($price_list);
	$pricing = json_decode($price_list, true);
	$lineitems[$i] = array (
		"name" => $content["item_name_".$i],
		"qty" => $content["item_quantity_".$i],
		"price" => $content["item_price_".$i],
		"pricing" => $pricing
	);

}	//end for( $i=1; $i<=$line_item_qty; $i++ )
$orderinfo["start"] = strtotime($orderinfo["start"]);
$orderinfo["end"] = strtotime($orderinfo["end"]);



//output strings for debugging

echo "<h1>Debug</h1>";
var_dump($lineitems);
echo "<br /><br /><h1>Customer Info</h1>";
echo "<div class='tekserverentals-customer-info'>";
echo "<p><b>Name:</b> ".$custinfo["first_name"]." ".$custinfo["last_name"]."</p>";
if ($custinfo["company"]) {
	echo "<p><b>Company:</b> ".$custinfo["company"]."</p>";
}
echo "<p><b>Email:</b> ".$custinfo["email"]."</p>";
echo "<p><b>Phone:</b> ".$custinfo["phone"]."</p>";
echo "<p><b>Address:</b><br />".$custinfo["address"]."<br />".$custinfo["city"].", ".$custinfo["state"]." ".$custinfo["zip"]."</p>";
echo "</div>";
echo "<br /><br /><h1>Order Info</h1>";
echo "<div class='tekserverentals-order-info'>";
echo "<p><b>Rental Starts:</b> ".date('l, F jS, Y' , $orderinfo["start"])." <b>Rental Ends:</b> ".date('l, F jS, Y', $orderinfo["end"])."</p>";
if (($orderinfo["delivery"] == 1) && ($orderinfo["where_deliver"] == "manhattan")) {
	echo "<p><b>Delivery Requested to:</b> Manhattan</p>";
}
if (($orderinfo["delivery"] == 1) && ($orderinfo["where_deliver"] == "borough")) {
	echo "<p><b>Delivery Requested to:</b> Outer Boroughs</p>";
}
if (($orderinfo["pickup"] == 1) && ($orderinfo["where_pickup"] == "manhattan")) {
	echo "<p><b>Delivery Requested to:</b> Manhattan</p>";
}
if (($orderinfo["pickup"] == 1) && ($orderinfo["where_pickup"] == "borough")) {
	echo "<p><b>Delivery Requested to:</b> Outer Boroughs</p>";
}
if ($orderinfo["shipping_total"] != 0) {
	echo "<p><b>Shipping Total:</b> ".$orderinfo["shipping_total"]."</p>";
}
echo "<p><b>Tax:</b> ".$orderinfo["tax_total"]."</p>";
echo "<p><b>Deposits:</b> ".$orderinfo["deposit_total"]."</p>";
echo "<p><b>Total Cost:</b> ".$orderinfo["total"]."<br/>clean-". floatval(str_replace( ',', '', ltrim( sanitize_text_field( $orderinfo["total"] ), "$" ) ) )."</p>";
//echo "<p><b>Amount Due for Reservation:</b> (includes deposit) ".$orderinfo["total_with_deposit"]."</p>";
echo "<p><b>Additional Notes:</b> ".$orderinfo["notes_from_cust"]."</p>";
echo "</div>";
echo "<br /><br /><h1>Line Items</h1>";
echo "<div class='tekserverentals-line-items'><table>";
echo "<thead><tr><td>Item</td><td>Qty</td><td>Price</td></tr></thead><tbody>";
foreach($lineitems as $item){
	echo "<tr>";
	echo "<td>";
	echo $item["name"];
	echo "</td>";
	echo "<td>";
	echo $item["qty"];
	echo "</td>";
	echo "<td>";
	echo $item["price"];
	echo "</td>";
	echo "</tr>";
}
unset($item);
echo "</tbody></table></div>";

//end debugging output



//check for and create user if !(user already exists). All paths end with $user_name containing user object for this request.
$user_name = get_user_by('email', $custinfo['email']);
if( !$user_name ) {

	$user_name = $custinfo['email'];
	$user_id = username_exists( $user_name );
	if( !$user_id and email_exists( $user_email ) == false ) {
	
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$user_id = wp_create_user( $user_name, $random_password, $custinfo['email'] );
		$user_name = get_user_by('ID', $user_id);
	
	}
	else {
	
		$random_password = __('User already exists.  Password inherited.');
		$user_name = get_user_by('ID', $user_id);
	
	}	//end if( !$user_id and email_exists( $user_email ) == false )

}	//end if( !$user_name )



//Set Values for new Request

//generate title
$title = sanitize_text_field( $custinfo["first_name"] )." ".sanitize_text_field( $custinfo["last_name"] )."'s request for ".date('l, F jS, Y' , sanitize_text_field($orderinfo['start']))." - ".date('l, F jS, Y' , sanitize_text_field($orderinfo['end']));

// ADD THE FORM INPUT TO $new_post ARRAY
$new_post = array(

	'post_title'    =>   $title,
	'post_content'	=>	 $orderinfo['notes_from_cust']."-<b>Original Notes from Customer</b>",
	'post_category' =>   '',
	'tags_input'    =>   '',
	'post_status'   =>   'publish',
	'post_type'		=>   'rentalrequest'

);

//SAVE THE POST
$new_rental_request = wp_insert_post($new_post);

//add wp user id to this record
add_post_meta( $new_rental_request, '_wpuserid', $user_name->ID );




//notify rentals dept via email (should set notification email settings in admin and use here...)
//set up fields for wp_mail
add_filter('wp_mail_content_type','set_content_type');
function set_content_type( $content_type ) {

	return 'text/html';

}	//end set_content_type( $content_type )
$headers = 'From: Rental Site <rentals@tekserve.com>' . "\r\n";
//send it
wp_mail( 'rentals@tekserve.com', 'New Rental Request: '.$new_post['post_title'], 'Go to : <a href="'.get_permalink( $new_rental_request ).'">'.$new_post['post_title'].'</a>' , $headers );



//update all of the order data
update_post_meta( $new_rental_request, 'tekserverentals_request_firstname', sanitize_text_field( $custinfo["first_name"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_lastname', sanitize_text_field( $custinfo["last_name"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_email', sanitize_text_field( $custinfo["email"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_phone', sanitize_text_field( $custinfo["phone"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_address', sanitize_text_field( $custinfo["address"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_city', sanitize_text_field( $custinfo["city"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_state', sanitize_text_field( $custinfo["state"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_zip', sanitize_text_field( $custinfo["zip"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_company', sanitize_text_field( $custinfo["company"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_start', sanitize_text_field( $orderinfo["start"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_end', sanitize_text_field( $orderinfo["end"] ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_duration', absint( $orderinfo["durationdays"] ) );
if( $orderinfo["delivery"] == 1 ) {

	update_post_meta( $new_rental_request, 'tekserverentals_request_delivery', "delivery" );

}	//end if( $orderinfo["delivery"] == 1 )
if( $orderinfo["pickup"] == 1 ) {

	update_post_meta( $new_rental_request, 'tekserverentals_request_pickup', "pickup" );

}	//if( $orderinfo["pickup"] == 1 )
update_post_meta( $new_rental_request, 'tekserverentals_delivery_loc', $orderinfo["where_deliver"] );
update_post_meta( $new_rental_request, 'tekserverentals_pickup_loc', $orderinfo["where_pickup"] );
update_post_meta( $new_rental_request, 'tekserverentals_request_deposits', round( str_replace( ',', '', ltrim( sanitize_text_field( $orderinfo["deposit_total"] ), "$" ) ), 2 ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_shipping', round( str_replace( ',', '', ltrim( sanitize_text_field( $orderinfo["shipping_total"] ), "$" ) ), 2 ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_tax', round( str_replace( ',', '', ltrim( sanitize_text_field( $orderinfo["tax_total"] ), "$" ) ), 2 ) );
update_post_meta( $new_rental_request, 'tekserverentals_request_total', floatval( str_replace( ',', '', ltrim( sanitize_text_field( $orderinfo["total"] ), "$" ) ) ) );
//update_post_meta( $new_rental_request, 'tekserverentals_request_total_wdeposits', round( ltrim( sanitize_text_field( $orderinfo["total_with_deposit"] ), "$" ), 2 ) );



//create line items and connect to request
foreach( $lineitems as $item ) {

	$item_name = $item["name"];
	$item_qty = $item["qty"];
	$item_price = $item["price"];
	$item_deposit = $item["pricing"]["deposit"];
	$item_dprice = $item["pricing"]["dprice"];
	$item_edprice = $item["pricing"]["edprice"];
	$item_wprice = $item["pricing"]["wprice"];
	$item_ewprice = $item["pricing"]["ewprice"];
	$new_line_item_obj = array(
	
		'post_title'    =>   $item_name,
		'post_category' =>   '',
		'tags_input'    =>   '',
		'post_status'   =>   'publish',
		'post_type' 	=>   'lineitem'
	
	);
	$new_line_item = wp_insert_post($new_line_item_obj);
	update_post_meta( $new_line_item, 'tekserverentals_line_item_qty', intval( ltrim($item_qty, "0" ) ) );
	update_post_meta( $new_line_item, 'tekserverentals_line_item_price', round( str_replace( ',', '', ltrim( $item_price, "$" ) ), 2 ) );
	update_post_meta( $new_line_item, '_tekserverentals_line_item_deposit', round( str_replace( ',', '', ltrim( $item_deposit, "$" ) ), 2 ) );
	update_post_meta( $new_line_item, '_tekserverentals_line_item_dprice', round( str_replace( ',', '', ltrim( $item_dprice, "$" ) ), 2 ) );
	update_post_meta( $new_line_item, '_tekserverentals_line_item_edprice', round( str_replace( ',', '', ltrim( $item_edprice, "$" ) ), 2 ) );
	update_post_meta( $new_line_item, '_tekserverentals_line_item_wprice', round( str_replace( ',', '', ltrim( $item_wprice, "$" ) ), 2 ) );
	update_post_meta( $new_line_item, '_tekserverentals_line_item_ewprice', round( str_replace( ',', '', ltrim( $item_ewprice, "$" ) ), 2 ) );
	p2p_type( 'line_items_to_rental_requests' )->connect( $new_line_item, $new_rental_request, array() );
	unset( $new_line_item );

}	//end foreach( $lineitems as $item )



//redirect to user facing request page (confirmation)
unset( $item );
echo '<p>post set:</p>';
var_dump( $new_rental_request );
echo get_permalink( $new_rental_request );
$link = get_permalink( $new_rental_request );
// wp_redirect( $link );



//create post
do_action('wp_insert_post', 'wp_insert_post');