<?php
/**
 * Plugin Name: Tekserve Rentals
 * Plugin URI: https://github.com/bangerkuwranger
 * Description: Enables a rentals system, complete with individual skus and rates.
 * Version: 1.0
 * Author: Chad A. Carino
 * Author URI: http://www.chadacarino.com
 * License: MIT
 */
/*
The MIT License (MIT)
Copyright (c) 2014 Chad A. Carino
 
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// include simplecart, js, & styles
//simplecart has been modified to include additional triggers - afterIncrement, afterDecrement, and afterRemove
function tekserverentals_simplecart() {
	wp_register_script( 'simplecart', plugins_url()."/tekserve-rentals/simpleCart.js", false, '3.0.5', true );
	wp_register_script( 'tekserverentals', plugins_url()."/tekserve-rentals/tekserve-rentals.js", false, false, true );
	wp_register_style( 'tekserverentalscss',  plugins_url()."/tekserve-rentals/tekserve-rentals.css" );
	wp_enqueue_script( 'simplecart' );
	wp_enqueue_script( 'tekserverentals' );
	wp_enqueue_style( 'tekserverentalscss' );
	$tekserverentalsjsdata = array(
		'tekserveRentalsUrl' => plugins_url().'/tekserve-rentals/'
		);
	wp_localize_script( 'tekserverentals', 'tekserveRentalsData', $tekserverentalsjsdata );
}

// Hook into the 'wp_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', 'tekserverentals_simplecart' );

if ( ! function_exists('tekserverentals_rental_product') ) {

// Register Custom Post Type
function tekserverentals_rental_product() {

	$labels = array(
		'name'                => 'Rental Products',
		'singular_name'       => 'Rental Product',
		'menu_name'           => 'Rental Product',
		'parent_item_colon'   => 'Parent Rental Product:',
		'all_items'           => 'All Rental Products',
		'view_item'           => 'View Rental Product',
		'add_new_item'        => 'Add New Rental Product',
		'add_new'             => 'New Rental Product',
		'edit_item'           => 'Edit Rental Product',
		'update_item'         => 'Update Rental Product',
		'search_items'        => 'Search Rental Products',
		'not_found'           => 'No Rental Products found',
		'not_found_in_trash'  => 'No Rental Products found in Trash',
	);
	$rewrite = array(
		'slug'                => 'rental-product',
		'with_front'          => true,
		'pages'               => false,
		'feeds'               => true,
	);
	$args = array(
		'label'               => 'rentalproduct',
		'description'         => 'General Products Available for Rental',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', ),
		'taxonomies'          => array( 'category', 'post_tag', 'skus' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'rentalproduct',
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
		'menu_icon'			  => 'dashicons-products',
	);
	register_post_type( 'rentalproduct', $args );

}

// Hook into the 'init' action
add_action( 'init', 'tekserverentals_rental_product', 0 );

}

if ( ! function_exists('tekserverentals_rental_sku') ) {

// Register Custom Post Type
function tekserverentals_rental_sku() {

	$labels = array(
		'name'                => 'Rental SKUs',
		'singular_name'       => 'Rental SKU',
		'menu_name'           => 'Rental SKU',
		'parent_item_colon'   => 'Parent Rental SKU:',
		'all_items'           => 'All Rental SKUs',
		'view_item'           => 'View Rental SKU',
		'add_new_item'        => 'Add New Rental SKU',
		'add_new'             => 'New Rental SKU',
		'edit_item'           => 'Edit Rental SKU',
		'update_item'         => 'Update Rental SKU',
		'search_items'        => 'Search Rental SKUs',
		'not_found'           => 'No Rental SKUs found',
		'not_found_in_trash'  => 'No Rental SKUs found in Trash',
	);
	$rewrite = array(
		'slug'                => 'rental-sku',
		'with_front'          => true,
		'pages'               => false,
		'feeds'               => true,
	);
	$args = array(
		'label'               => 'rentalsku',
		'description'         => 'Individual SKUs of products available for rental',
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'taxonomies'          => array( '' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'rentalsku',
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
		'menu_icon'			  => 'dashicons-feedback',
	);
	register_post_type( 'rentalsku', $args );

}

// Hook into the 'init' action
add_action( 'init', 'tekserverentals_rental_sku', 0 );

}

// Connect SKUs to General Product
function connect_sku_to_product() {
	p2p_register_connection_type( array(
		'name' => 'skus_to_rental_products',
		'from' => 'rentalsku',
		'to' => 'rentalproduct',
		'cardinality' => 'many-to-one',
		'admin_box' => array(
			'show' => 'any',
			'context' => 'advanced'
		),
		'title' => array(
			'from' => __( 'Rental Product Type', 'tekserve-rentals' ),
			'to' => __( 'Rental Product SKUs', 'tekserve-rentals' )
		), 
			'from_labels' => array(
			'singular_name' => __( 'SKU', 'tekserve-rentals' ),
			'search_items' => __( 'Search SKUs', 'tekserve-rentals' ),
			'not_found' => __( 'No SKUs found.', 'tekserve-rentals' ),
			'create' => __( 'Add SKUs', 'tekserve-rentals' ),
		),
			'to_labels' => array(
			'singular_name' => __( 'Rental Product', 'tekserve-rentals' ),
			'search_items' => __( 'Search Rental Products', 'tekserve-rentals' ),
			'not_found' => __( 'No Rental Products found.', 'tekserve-rentals' ),
			'create' => __( 'Select SKU\'s Rental Product Type', 'tekserve-rentals' ),
		),	
	) );
}
add_action( 'p2p_init', 'connect_sku_to_product' );

//create custom fields for SKU
add_action( 'admin_init', 'tekserverentals_sku_custom_fields' );
function tekserverentals_sku_custom_fields() {
    add_meta_box( 'tekserverentals_sku_meta_box', 'SKU Details', 'display_tekserverentals_sku_meta_box', 'rentalsku', 'normal', 'core' );
}

// Retrieve current details based on SKU ID
function display_tekserverentals_sku_meta_box( $rentalsku ) {
    $tekserverentals_sku_sku = esc_html( get_post_meta( $rentalsku->ID, 'tekserverentals_sku_sku', true ) );
	$tekserverentals_sku_serial = esc_html( get_post_meta( $rentalsku->ID, 'tekserverentals_sku_serial', true ) );
	$tekserverentals_sku_status = esc_html( get_post_meta( $rentalsku->ID, 'tekserverentals_sku_status', true ) );
	wp_nonce_field( 'tekserverentals_meta_box', 'tekserverentals_nonce' );
	?>
    <table>
        <tr>
            <td style="width: 100%">SKU</td>
        </tr>
        <tr>
            <td><input type="text" size="30" name="tekserverentals_sku_sku" value="<?php echo $tekserverentals_sku_sku; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 100%">Serial Number</td>
        </tr>
        <tr>
            <td><input type="text" size="30" name="tekserverentals_sku_serial" value="<?php echo $tekserverentals_sku_serial; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 100%">Current Status</td>
        </tr>
        <tr>
        <td>
        	<input type="radio" name="tekserverentals_sku_status" value="available" <?php checked( 'tekserverentals_sku_status', 'available' ); ?> /> Available<br/>
			<input type="radio" name="tekserverentals_sku_status" value="rented" <?php checked( 'tekserverentals_sku_status', 'rented' ); ?> /> Rented<br/>
			<input type="radio" name="tekserverentals_sku_status" value="unavailable-service" <?php checked( 'tekserverentals_sku_status', 'unavailable-service' ); ?> /> Being Serviced<br/>
			<input type="radio" name="tekserverentals_sku_status" value="unavailable-other" <?php checked( 'tekserverentals_sku_status', 'unavailable-other' ); ?> /> Unavailable
		</td>
		</tr>
    </table>
    <?php
}

//store custom field data
add_action( 'save_post', 'add_tekserverentals_sku_fields', 5, 2 );
function add_tekserverentals_sku_fields( $tekserverentals_sku_id, $rentalsku ) {
	if ( ! isset( $_POST['tekserverentals_nonce'] ) ) {
    	return $tekserverentals_sku_id;
    }
    $nonce = $_POST['tekserverentals_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	  return $tekserverentals_sku_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $tekserverentals_sku_id;
    // Check post type for 'rentalsku'
    if ( $rentalsku->post_type == 'rentalsku' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['tekserverentals_sku_sku'] ) && $_POST['tekserverentals_sku_sku'] != '' ) {
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_sku', sanitize_text_field( $_REQUEST['tekserverentals_sku_sku'] ) );
        }
        if ( isset( $_POST['tekserverentals_sku_serial'] ) && $_POST['tekserverentals_sku_serial'] != '' ) {
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_serial', sanitize_text_field( $_REQUEST['tekserverentals_sku_serial'] ) );
    	}
    	if ( isset( $_POST['tekserverentals_sku_status'] ) ) {
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_status', $_REQUEST['tekserverentals_sku_status'] );
        }
    }
}

//create custom fields for Product Rates
add_action( 'admin_init', 'tekserverentals_product_rates_fields' );
function tekserverentals_product_rates_fields() {
    add_meta_box( 'tekserverentals_product_rates', 'Rates', 'display_tekserverentals_product_rates', 'rentalproduct', 'normal', 'core' );
}

// Retrieve current details based on SKU ID
function display_tekserverentals_product_rates( $rentalproduct ) {
    $tekserverentals_product_deposit = esc_html( get_post_meta( $rentalproduct->ID, 'tekserverentals_product_deposit', true ) );
	$tekserverentals_product_firstday_rate = esc_html( get_post_meta( $rentalproduct->ID, 'tekserverentals_product_firstday_rate', true ) );
	$tekserverentals_product_addday_rate = esc_html( get_post_meta( $rentalproduct->ID, 'tekserverentals_product_addday_rate', true ) );
	$tekserverentals_product_firstweek_rate = esc_html( get_post_meta( $rentalproduct->ID, 'tekserverentals_product_firstweek_rate', true ) );
	$tekserverentals_product_addweek_rate = esc_html( get_post_meta( $rentalproduct->ID, 'tekserverentals_product_addweek_rate', true ) );
	wp_nonce_field( 'tekserverentals_meta_box', 'tekserverentals_nonce' );
	?>
    <table>
        <tr>
            <td>DEPOSIT: $<input type="text" size="10" name="tekserverentals_product_deposit" value="<?php echo $tekserverentals_product_deposit; ?>" /></td>
        </tr>
        <tr>
        	<td style="width: 25%">1st DAY: $<input type="text" size="10" name="tekserverentals_product_firstday_rate" value="<?php echo $tekserverentals_product_firstday_rate; ?>" /></td>
        	<td style="width: 25%">Extra DAY: $<input type="text" size="10" name="tekserverentals_product_addday_rate" value="<?php echo $tekserverentals_product_addday_rate; ?>" /></td>
        	<td style="width: 25%">1st WEEK: $<input type="text" size="10" name="tekserverentals_product_firstweek_rate" value="<?php echo $tekserverentals_product_firstweek_rate; ?>" /></td>
        	<td style="width: 25%">Extra WEEK: $<input type="text" size="10" name="tekserverentals_product_addweek_rate" value="<?php echo $tekserverentals_product_addweek_rate; ?>" /></td>
		</tr>
    </table>
    <?php
}

//store custom field data for product rates
add_action( 'save_post', 'add_tekserverentals_product_rate_fields', 5, 2 );
function add_tekserverentals_product_rate_fields( $tekserverentals_product_id, $rentalproduct ) {
	if ( ! isset( $_POST['tekserverentals_nonce'] ) ) {
    	return $tekserverentals_product_id;
    }
    $nonce = $_POST['tekserverentals_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	  return $tekserverentals_product_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return $tekserverentals_product_id;
    // Check post type for 'rentalsku'
    if ( $rentalproduct->post_type == 'rentalproduct' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['tekserverentals_product_deposit'] ) && $_POST['tekserverentals_product_deposit'] != '' ) {
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_deposit', sanitize_text_field( $_REQUEST['tekserverentals_product_deposit'] ) );
        }
        if ( isset( $_POST['tekserverentals_product_firstday_rate'] ) && $_POST['tekserverentals_product_firstday_rate'] != '' ) {
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_firstday_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_firstday_rate'] ) );
        }
        if ( isset( $_POST['tekserverentals_product_addday_rate'] ) && $_POST['tekserverentals_product_addday_rate'] != '' ) {
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_addday_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_addday_rate'] ) );
        }
        if ( isset( $_POST['tekserverentals_product_firstweek_rate'] ) && $_POST['tekserverentals_product_firstweek_rate'] != '' ) {
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_firstweek_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_firstweek_rate'] ) );
        }
        if ( isset( $_POST['tekserverentals_product_addweek_rate'] ) && $_POST['tekserverentals_product_addweek_rate'] != '' ) {
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_addweek_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_addweek_rate'] ) );
        }
    }
}


	// Register Custom Post Type for rental requests
	function tekserverentals_rental_request() {

		$labels = array(
			'name'                => 'Rental Requests',
			'singular_name'       => 'Rental Request',
			'menu_name'           => 'Rental Requests',
			'parent_item_colon'   => 'Parent Rental Request:',
			'all_items'           => 'All Rental Requests',
			'view_item'           => 'View Rental Request',
			'add_new_item'        => 'Add New Rental Request',
			'add_new'             => 'Add Rental Request',
			'edit_item'           => 'Edit Rental Request',
			'update_item'         => 'Update Rental Request',
			'search_items'        => 'Search Rental Request',
			'not_found'           => 'Rental Request Not found',
			'not_found_in_trash'  => 'Rental Request Not found in Trash',
		);
		$args = array(
			'label'               => 'tekserverentals_rental_request',
			'description'         => 'Requests from customers for rental',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'comments' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-cart',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'query_var'           => 'rentalrequest',
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'tekserverentals_rental_request', $args );

	}

	// Hook into the 'init' action
	add_action( 'init', 'tekserverentals_rental_request', 0 );

// Add Shortcode for Generating rental item by ID
function tekserverental_item( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'id' => '',
		), $atts )
	);

	// Code to create output; includes class names to create items for simplecart checkout
	//get rental item using id
	$item = get_post( $id );
	$out = '';
	$meta = get_post_custom($id);
		$name = '<h3 class="tekserverentals-item-name item_name">' . get_the_title( $id ) . '</h3>';
		$prices = '<div class="tekserverentals-item-prices">';
		$prices .= '<p><b><i>Deposit: </i></b>$<span class="item_deposit">';
		$prices .= $meta['tekserverentals_product_deposit'][0];
		$prices .= '.00</p>';
		$prices .= '<p><b>1st Day: </b>$<span class="item_dprice">';
		$prices .= $meta['tekserverentals_product_firstday_rate'][0];
		$prices .= '.00</span></p>';
		$prices .= '<p><b>Addtl. Day: </b>$<span class="item_edprice">';
		$prices .= $meta['tekserverentals_product_addday_rate'][0];
		$prices .= '.00</span></p>';
		$prices .= '<p><b>1st Week: </b>$<span class="item_wprice">';
		$prices .= $meta['tekserverentals_product_firstweek_rate'][0];
		$prices .= '.00</span></p>';
		$prices .= '<p><b>Addtl. Week: </b>$<span class="item_ewprice">';
		$prices .= $meta['tekserverentals_product_addweek_rate'][0];
		$prices .= '.00</span></p>';
		$prices .= '</div>';
		$addToCart = '<div class="tekserverentals-item-add-to-cart">';
		$addToCart .= '<b>QTY</b> <input style="width: 3em;" type="text" value="1" class="item_Quantity" />';
		$addToCart .= '<a class="item_add button tekserverentals-item-add-to-cart-button" href="javascript:;">Add to Cart</a>';
		$addToCart .= '</div>';
		$thumb = '<div class="tekserverentals-item-image">' . get_the_post_thumbnail($id, 'full') . '</div>';
		//output single div with item
		$out = '<div class="tekserverentals-item simpleCart_shelfItem">';
		//output image and name
		$out .= '<div class="tekserverentals-item-info">'.$thumb.$name.'</div>';
		//output pricing
		$out .= $prices;
		//output add to cart button and qty box
		$out .= $addToCart;
		//output add to cart control
		//put additional info in a drawer
		$out .=	"<a id='find-tekserverentals-item-".$id."' name=''></a>";
		$out .=	'<span class="collapseomatic find-me drawertrigger" title="Learn More About '.get_the_title( $id ).'" id="tekserverentals-item-'.$id.'" >Learn More About '.get_the_title( $id ).'</span>';
		$out .= '<div id="target-tekserverentals-item-'.$id.'" class="collapseomatic_content">' . $item->post_content . '</div>';
		$out .= '</div>';
	return $out;
}
add_shortcode( 'rentalitem', 'tekserverental_item' );

// Add Shortcode for Checkout Form
function tekserverental_checkout( $atts ) {
	$custinfo = '<div class="tekserverental-checkout-custinfo">';
	$custinfo .= '<div id="tekserverental-name">
	<label class="required" for="entry_0" style="width: 6em;">Name </label>
	<span><label class="lower">First</label><input id="entry_0" name="firstname" class="required" title="We need to know your name." maxlength="255" size="25" value="">
		</span>
		<span>
			<label class="lower">Last</label><input id="entry_1" name="lastname" class="required" title="We need to know your name." maxlength="255" size="25" value="">
		</span>
		</div>';
	$custinfo .= '<div id="tekserverental-company-name">
		<label class="description" for="entry_2">Company Name (if applicable) </label>
		<div>
			<input id="entry_2" name="companyname" class="element text medium" type="text" maxlength="255" value=""> 
		</div> 
		</div>';
	$custinfo .= '<div id="tekserverental-email">
		<label class="req" for="entry_3" style="width: 35px;">Email</label>
		<div>
			<input id="entry_3" name="emailaddress" class="email required" title="We need to know your valid email address to send you a quote." type="email" size="50" maxlength="255" value=""> 
		</div> 
		</div>';
	$custinfo .= '<div id="tekserverental-phone-number">
		<label class="req" for="entry_7" style="width: 35px;">Phone</label>
		<span>
			<input id="entry_7" name="phonenumber" class=" digits required" size="14" maxlength="14" value="" type="tel">
			<label for="entry_7" class="lower">(###) ###-####</label>
		</span>
		</div>';
	$custinfo .= '<div id="tekserverental-">
		<label class="req" for="element_5" style="width: 9em;">Billing Address </label>
		<div>
			<input id="entry_9" name="addressone" class="large required" value="" type="text">
			<label for="entry_9" class="lower">Street Address</label>
		</div>
	
		<div>
			<input id="entry_10" name="addresstwo" class="element large text" value="" type="text">
			<label for="" class="lower">Address Line 2</label>
		</div>
	
		<div>
			<input id="entry_11" name="city" class="large required" value="" type="text">
			<label for="entry_11" class="lower">City</label>
		</div>
	
		<div class="left">
			<input id="entry_12" name="state" class="medium required" value="" type="text">
			<label for="entry_12" class="lower">State / Province / Region</label>
		</div>
	
		<div class="right">
			<input id="entry_13" name="zip" class="medium required" maxlength="15" value="" type="text">
			<label for="entry_13" class="lower">Postal / Zip Code</label>
		</div>
	</div>';
	$custinfo .= '</div>';
	$shipping = '<div class="tekserverental-shipping">';
	$shipping .= '<div class="tekserverental-delivery"><label for="tekserverentals-delivery">Would you like messenger delivery? </label><input type="checkbox" name="tekserverentals-delivery" id="tekserverentals-delivery" value="true"> Yes, bring it to me.';
	$shipping .= '<label for="tekserverentals-delivery-loc">Where you like messenger delivery? </label><input type="radio" name="tekserverentals-delivery-loc" value="manhattan" checked="checked"> Manhattan <input type="radio" name="tekserverentals-delivery-loc" value="borough"> Bronx, Brooklyn, Queens, or Staten Island</div>';
	$shipping .= '<div class="tekserverental-pickup"><label for="tekserverentals-pickup">Would you like messenger pickup at the end of your rental? </label><input type="checkbox" name="tekserverentals-pickup" id="tekserverentals-pickup" value="true"> Yes, pick it up for me.';
	$shipping .= '<label for="tekserverentals-pickup-loc">Where you like messenger pickup? </label><input type="radio" name="tekserverentals-pickup-loc" value="manhattan" checked="checked"> Manhattan <input type="radio" name="tekserverentals-pickup-loc" value="borough"> Bronx, Brooklyn, Queens, or Staten Island</div>';
	$shipping .= '</div>';
	$form = '<div class="tekserverental-checkout-form"><form id="tekserverentals-checkout-form">';
	$form .= $custinfo;
	$form .= '<div><label for="entry_14">Enter any additional requests </label><textarea name="additionalinfo" id="entry_14">&nbsp;</textarea></div>';
	$form .= '</form></div>';
	$button = '<a href="javascript:;" class="simpleCart_checkout">Checkout</a>';
	$out = '<div class="tekserverental-checkout">';
	$out .= $shipping;
	$out .= $form;
	$out .= $button;
	$out .= '</div>';
	return $out;
}
add_shortcode( 'rentalcheckout', 'tekserverental_checkout' );

// Add Shortcode for Dates Form
function tekserverental_date_form( $atts ) {
	$form = '<div class="tekserverental-dates-form"><form id="tekserverentals-checkout-dates">';
	$form .= '<div class="tekserverental-dates-start"><h3>Choose the Start Date for Your Rental</h3><div><input type="date" id="tekserverental-start-date" name="startdate"></div></div>';
	$form .= '<div class="tekserverental-dates-end"><h3>Choose the End Date for Your Rental</h3><div><input type="date" id="tekserverental-end-date" name="enddate"></div></div>';
	$form .= '</form></div>';
	$button = '<a class="button tekserverentals-dates-button" href="javascript:;">Set Dates</a>';
	$out = '<div class="tekserverental-dates">';
	$out .= $form;
	$out .= '</div>';
	return $out;
}
add_shortcode( 'rentaldateform', 'tekserverental_date_form' );


//Add VC buttons if VC is installed
if (function_exists('vc_map')) { //check for vc_map function before mapping buttons
	vc_map( array(
	   "name" => __("Rental Item"),
	   "base" => "rentalitem",
	   "class" => "",
	   "icon" => "icon-wpb-rentalitem",
	   "category" => __('Content'),
	   "params" => array(
		   array(
				 "type" => "textfield",
				 "holder" => "div",
				 "class" => "",
				 "heading" => __("Rental Item ID"),
				 "param_name" => "id",
				 "value" => __(""),
				 "description" => __("Enter the ID number of the Rental Item to display. Required."),
				 "admin_label" => True
			  )
		)
	)	);
	
	vc_map( array(
	   "name" => __("Rental Checkout Form"),
	   "base" => "rentalcheckout",
	   "class" => "",
	   "icon" => "icon-wpb-rentalcheckout",
	   "category" => __('Content')
	)	);
	
	vc_map( array(
	   "name" => __("Rental Dates Form"),
	   "base" => "rentaldateform",
	   "class" => "",
	   "icon" => "icon-wpb-rentaldates",
	   "category" => __('Content')
	)	);
}