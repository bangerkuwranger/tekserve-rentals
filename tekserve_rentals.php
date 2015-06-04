<?php
/**
 * Plugin Name: Tekserve Rentals
 * Plugin URI: https://github.com/bangerkuwranger
 * Description: Enables a rentals system, complete with individual skus and rates.
 * Version: 1.4
 * Author: Chad A. Carino
 * Author URI: http://www.chadacarino.com
 * License: MIT
 */
/*
The MIT License (MIT)
Copyright (c) 2015 Chad A. Carino
 
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// exit();
// 
// //check for posts-2-posts plugin - exit if not (admin is warned)
// function tekserverentals_need_ptop() {
// 
// 	echo '<div class="error"><p><strong>Tekserve Rentals Cannot Run:</strong></p><p>This plugin uses scribu\'s posts-to-posts plugin for a lot of its functions. Please make sure this is installed and working. If it is not installed, you can download it from the <a href="https://wordpress.org/plugins/posts-to-posts/" target="_blank">posts-to-posts plugin page on worpress.org</a>. (if Tekserve Rentals was working before, the data should still be there; you will see your entries again once this issue is resolved.)</p></div>';
// 
// }	//end tekserverentals_need_ptop()
// 
// add_action( 'plugins_loaded', 'tekserverentals_ptop_check' );
// 
// function tekserverentals_ptop_check() {
// 
// 	if( function_exists( 'p2p_register_connection_type' ) ) {
// 
// 		if ( is_admin() ) {
// 		
// 			add_action('admin_notices', 'tekserverentals_need_ptop');
// 		
// 		}
// 		exit();
// 
// 	}	//end if( ! function_exists( 'p2p_register_connection_type' ) )
// 	
// }	//end tekserverentals_ptop_check()




// include simplecart, validation, js, & styles
//simplecart has been modified to include additional triggers - afterIncrement, afterDecrement, and afterRemove
add_action( 'wp_enqueue_scripts', 'tekserverentals_enqueuing' );
function tekserverentals_enqueuing() {

	wp_register_script( 'simplecart', plugins_url()."/tekserve-rentals/simpleCart.js", array( 'jquery' ), '3.0.5', true );
	wp_register_script( 'tekserverentals', plugins_url()."/tekserve-rentals/tekserve-rentals.js", array( 'jquery', 'simplecart' ), '1.4', true );
	wp_register_style( 'tekserverentalscss',  plugins_url()."/tekserve-rentals/tekserve-rentals.css" );
	wp_register_script( 'jqvalidate', "//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js", array( 'jquery' ), false, true );
	wp_register_script( 'jqvalidateExtra', "//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/additional-methods.min.js", array( 'jquery' ), false, true );
	$tekserverentalsjsdata = array(
	
		'tekserveRentalsUrl' => plugins_url().'/tekserve-rentals/'
	
	);
	wp_localize_script( 'tekserverentals', 'tekserveRentalsData', $tekserverentalsjsdata );
	if( get_post_type() == 'page' ) { 
		
		wp_enqueue_script( 'simplecart' );
		wp_enqueue_script( 'jqvalidate' );
		wp_enqueue_script( 'jqvalidateExtra' );
		wp_enqueue_script( 'tekserverentals' );
	
	}	//end if( get_post_type() == 'page' )
	wp_enqueue_style( 'tekserverentalscss' );

}	//end tekserverentals_enqueuing()



if( ! function_exists('tekserverentals_rental_product') ) {

	// Register Custom Post Type
	add_action( 'init', 'tekserverentals_rental_product', 0 );
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
	
	}	//end tekserverentals_rental_product()

}	//end if( ! function_exists('tekserverentals_rental_product') )



if( ! function_exists('tekserverentals_rental_sku') ) {

	// Register Custom Post Type
	add_action( 'init', 'tekserverentals_rental_sku', 0 );
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

	}	//end tekserverentals_rental_sku()

}	//end if( ! function_exists('tekserverentals_rental_sku') )



// Connect SKUs to General Product
add_action( 'p2p_init', 'tekserverentals_connect_sku_to_product' );
function tekserverentals_connect_sku_to_product() {

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

}	//end tekserverentals_connect_sku_to_product()



//create custom fields for SKU
add_action( 'admin_init', 'tekserverentals_add_sku_meta_box' );
function tekserverentals_add_sku_meta_box() {

    add_meta_box( 'tekserverentals_sku_meta_box', 'SKU Details', 'tekserverentals_display_sku_meta_box', 'rentalsku', 'normal', 'core' );

}	//end tekserverentals_add_sku_meta_box

// Retrieve current details based on SKU ID and display in meta box fields (if they exist)
function tekserverentals_display_sku_meta_box( $rentalsku ) {

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

}	//end tekserverentals_display_sku_meta_box( $rentalsku )



//store custom field data
add_action( 'save_post', 'tekserverentals_save_sku_fields', 5, 2 );
function tekserverentals_save_sku_fields( $tekserverentals_sku_id, $rentalsku ) {

	if( ! isset( $_POST['tekserverentals_nonce'] ) ) {
	
    	return $tekserverentals_sku_id;
    
    }	//end if( ! isset( $_POST['tekserverentals_nonce'] ) )
    $nonce = $_POST['tekserverentals_nonce'];
	if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) ) {
	
	  return $tekserverentals_sku_id;
	
	}	//end if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	
		return $tekserverentals_sku_id;
	
	}	//end if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    // Check post type for 'rentalsku'
    if( $rentalsku->post_type == 'rentalsku' && current_user_can( 'edit_pages' ) ) {
    
        // Store data in post meta table if present in post data
        if( isset( $_POST['tekserverentals_sku_sku'] ) && $_POST['tekserverentals_sku_sku'] != '' ) {
        
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_sku', sanitize_text_field( $_REQUEST['tekserverentals_sku_sku'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_sku_sku'] ) && $_POST['tekserverentals_sku_sku'] != '' )
        if( isset( $_POST['tekserverentals_sku_serial'] ) && $_POST['tekserverentals_sku_serial'] != '' ) {
        
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_serial', sanitize_text_field( $_REQUEST['tekserverentals_sku_serial'] ) );
    	
    	}	//end if( isset( $_POST['tekserverentals_sku_serial'] ) && $_POST['tekserverentals_sku_serial'] != '' )
    	if( isset( $_POST['tekserverentals_sku_status'] ) ) {
    	
            update_post_meta( $tekserverentals_sku_id, 'tekserverentals_sku_status', $_REQUEST['tekserverentals_sku_status'] );
        
        }	//end if( isset( $_POST['tekserverentals_sku_status'] ) )
    
    }	//end  if( $rentalsku->post_type == 'rentalsku' && current_user_can( 'edit_pages' ) )

}	//end tekserverentals_save_sku_fields( $tekserverentals_sku_id, $rentalsku )



//create meta box for custom fields for Product Rates
add_action( 'admin_init', 'tekserverentals_add_product_rates_meta_box' );
function tekserverentals_add_product_rates_meta_box() {

    add_meta_box( 'tekserverentals_product_rates', 'Rates', 'tekserverentals_display_product_rates_meta_box', 'rentalproduct', 'normal', 'core' );

}	//end tekserverentals_add_product_rates_meta_box()



// Retrieve current details based on SKU ID
function tekserverentals_display_product_rates_meta_box( $rentalproduct ) {

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

}	//end tekserverentals_display_product_rates_meta_box( $rentalproduct )



//store custom field data for product rates
add_action( 'save_post', 'tekserverentals_save_product_rate_meta_box_fields', 5, 2 );
function tekserverentals_save_product_rate_meta_box_fields( $tekserverentals_product_id, $rentalproduct ) {

	if( ! isset( $_POST['tekserverentals_nonce'] ) ) {
	
    	return $tekserverentals_product_id;
    
    }	//end if( ! isset( $_POST['tekserverentals_nonce'] ) )
    $nonce = $_POST['tekserverentals_nonce'];
	if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) ) {
	
	  return $tekserverentals_product_id;
	
	}	//end if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	
	  return $tekserverentals_product_id;
	
	}	//end if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    // Check post type for 'rentalsku'
    if( $rentalproduct->post_type == 'rentalproduct' && current_user_can( 'edit_pages' ) ) {
    
        // Store data in post meta table if present in post data
        if( isset( $_POST['tekserverentals_product_deposit'] ) && $_POST['tekserverentals_product_deposit'] != '' ) {
        
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_deposit', sanitize_text_field( $_REQUEST['tekserverentals_product_deposit'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_product_deposit'] ) && $_POST['tekserverentals_product_deposit'] != '' )
        if( isset( $_POST['tekserverentals_product_firstday_rate'] ) && $_POST['tekserverentals_product_firstday_rate'] != '' ) {
        
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_firstday_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_firstday_rate'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_product_firstday_rate'] ) && $_POST['tekserverentals_product_firstday_rate'] != '' )
        if( isset( $_POST['tekserverentals_product_addday_rate'] ) && $_POST['tekserverentals_product_addday_rate'] != '' ) {
        
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_addday_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_addday_rate'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_product_addday_rate'] ) && $_POST['tekserverentals_product_addday_rate'] != '' )
        if( isset( $_POST['tekserverentals_product_firstweek_rate'] ) && $_POST['tekserverentals_product_firstweek_rate'] != '' ) {
        
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_firstweek_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_firstweek_rate'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_product_firstweek_rate'] ) && $_POST['tekserverentals_product_firstweek_rate'] != '' )
        if( isset( $_POST['tekserverentals_product_addweek_rate'] ) && $_POST['tekserverentals_product_addweek_rate'] != '' ) {
        
            update_post_meta( $tekserverentals_product_id, 'tekserverentals_product_addweek_rate', sanitize_text_field( $_REQUEST['tekserverentals_product_addweek_rate'] ) );
        
        }	//end if( isset( $_POST['tekserverentals_product_addweek_rate'] ) && $_POST['tekserverentals_product_addweek_rate'] != '' )
    
    }	//end if( $rentalproduct->post_type == 'rentalproduct' && current_user_can( 'edit_pages' ) )

}	//end tekserverentals_save_product_rate_meta_box_fields( $tekserverentals_product_id, $rentalproduct )


	// Register Custom Post Type for rental requests
if ( ! function_exists('tekserverentals_rental_request') ) {

	// Register Custom Post Type
	function tekserverentals_rental_request() {

		$labels = array(
			'name'                => 'Rental Requests',
			'singular_name'       => 'Rental Request',
			'menu_name'           => 'Rental Request',
			'parent_item_colon'   => 'Parent Rental Request:',
			'all_items'           => 'All Rental Requests',
			'view_item'           => 'View Rental Request',
			'add_new_item'        => 'Add New Rental Request',
			'add_new'             => 'New Rental Request',
			'edit_item'           => 'Edit Rental Request',
			'update_item'         => 'Update Rental Request',
			'search_items'        => 'Search Rental Requests',
			'not_found'           => 'No Rental Requests found',
			'not_found_in_trash'  => 'No Rental Requests found in Trash',
		);
		$rewrite = array(
			'slug'                => 'rental-request',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => 'rentalrequest',
			'description'         => 'Requests for rentals',
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
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
			'query_var'           => 'rentalrequest',
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
			'menu_icon'			  => 'dashicons-testimonial',
		);
		register_post_type( 'rentalrequest', $args );

	}	//end function tekserverentals_rental_request()

}	//end if ( ! function_exists('tekserverentals_rental_request') )

// Hook into the 'init' action
add_action( 'init', 'tekserverentals_rental_request', 0 );


//create meta box for custom fields for Requests
add_action( 'admin_init', 'tekserverentals_requests_custom_fields_meta_box' );
function tekserverentals_requests_custom_fields_meta_box() {

    add_meta_box( 'tekserverentals_requests_meta_box', 'Rental Details', 'tekserverentals_display_request_meta_box_fields', 'rentalrequest', 'normal', 'high' );

}	//end tekserverentals_requests_custom_fields_meta_box()



// Retrieve current details based on request ID
function tekserverentals_display_request_meta_box_fields( $rentalrequest ) {

    $tekserverentals_request_firstname = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_firstname', true ) );
    $tekserverentals_request_lastname = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_lastname', true ) );
	$tekserverentals_request_company = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_company', true ) );
	$tekserverentals_request_email = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_email', true ) );
	$tekserverentals_request_phone = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_phone', true ) );
	$tekserverentals_request_address = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_address', true ) );
	$tekserverentals_request_city = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_city', true ) );
	$tekserverentals_request_state = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_state', true ) );
	$tekserverentals_request_zip = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_zip', true ) );
	$tekserverentals_request_start = esc_html( date( 'm/d/Y', get_post_meta( $rentalrequest->ID, 'tekserverentals_request_start', true ) ) );
	$tekserverentals_request_end = esc_html( date( 'm/d/Y', get_post_meta( $rentalrequest->ID, 'tekserverentals_request_end', true ) ) );
	$tekserverentals_request_shipping = esc_html( round( ltrim( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_shipping', true ), "$" ), 2 ) );
	$tekserverentals_request_delivery = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_delivery', true ) );
	$tekserverentals_delivery_loc = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_delivery_loc', true ) );
	$tekserverentals_request_pickup = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_pickup', true ) );
	$tekserverentals_request_pickup_loc = esc_html( get_post_meta( $rentalrequest->ID, 'tekserverentals_pickup_loc', true ) );
	$tekserverentals_request_tax = esc_html( round( ltrim( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_tax', true ), "$" ), 2 ) );
	$tekserverentals_request_deposits = esc_html( round( ltrim( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_deposits', true ), "$" ), 2 ) );
	$tekserverentals_request_total = esc_html( round( ltrim( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_total', true ), "$" ), 2 ) );
	$tekserverentals_request_total_wdeposits = esc_html( round( ltrim( get_post_meta( $rentalrequest->ID, 'tekserverentals_request_total_wdeposits', true ), "$" ), 2 ) );
	$tekserverentals_request_duration = floor(((get_post_meta( $rentalrequest->ID, 'tekserverentals_request_end', true )) - (get_post_meta( $rentalrequest->ID, 'tekserverentals_request_start', true )))/86400);
	wp_nonce_field( 'tekserverentals_meta_box', 'tekserverentals_nonce' );
	?>
	<h2>Order Info</h2>
	<table>
		<tr>
		<td>Rental Starts: </td>
		<td><input type="text" size="12" name="tekserverentals_request_start" class="hasDatepicker" value="<?php echo $tekserverentals_request_start; ?>" /></td>
		<td>Rental Ends: </td>
		<td><input type="text" size="12" name="tekserverentals_request_end" class="hasDatepicker" value="<?php echo $tekserverentals_request_end; ?>" /></td>
		</tr>
		<tr>
		<td>Duration: </td>
		<td colspan="3"><?php echo $tekserverentals_request_duration ?> Days</td>
		</tr>
		<tr>
		<td>Delivery Requested? </td>
		<td><input type="checkbox" readonly name="tekserverentals_request_delivery" value="delivery" <?php checked( $tekserverentals_request_delivery, 'delivery' ); ?> /></td>
		<td>Where to?</td>
		<td>
		<select disabled name="tekserverentals_delivery_loc">
			<option value="manhattan" <?php selected( $tekserverentals_delivery_loc, 'manhattan' ); ?>>Manhattan below 96th</option>
			<option value="borough" <?php selected( $tekserverentals_delivery_loc, 'borough' ); ?>>Bronx, Brooklyn, Manhattan above 96th, Queens, or SI</option>
		</select>
		</td>
		</tr>
		<tr>
		<td>Pickup Requested? </td>
		<td><input type="checkbox" readonly name="tekserverentals_request_pickup" value="pickup" <?php checked( $tekserverentals_request_pickup, 'pickup' ); ?> /></td>
		<td>Where to?</td>
		<td>
		<select disabled name="tekserverentals_request_pickup_loc">
			<option value="manhattan" <?php selected( $tekserverentals_request_pickup_loc, 'manhattan' ); ?>>Manhattan below 96th</option>
			<option value="borough" <?php selected( $tekserverentals_request_pickup_loc, 'borough' ); ?>>Bronx, Brooklyn, Manhattan above 96th, Queens, or SI</option>
		</select>
		</td>
		</tr>
		<tr>
		<td>Shipping: </td>
		<td>$<input type="number" size="12" name="tekserverentals_request_shipping" value="<?php echo $tekserverentals_request_shipping; ?>" /></td>
		<td>Tax: </td>
		<td>$<input type="number" readonly size="12" name="tekserverentals_request_tax" value="<?php echo $tekserverentals_request_tax; ?>" /></td>
		</tr>
		<td>Total Deposits: </td>
		<td colspan="3">$<input readonly type="number" size="12" name="tekserverentals_request_deposits" value="<?php echo $tekserverentals_request_deposits; ?>" /></td>
		<tr>
		<td>Total: </td>
		<td colspan="3">$<input type="number" readonly size="12" name="tekserverentals_request_total" value="<?php echo $tekserverentals_request_total; ?>" /></td>
		</tr>
	</table>
	<h2>Customer Info</h2>
    <table>
        <tr>
		<td>Name</td>
		<td><input type="text" size="30" name="tekserverentals_request_firstname" value="<?php echo $tekserverentals_request_firstname; ?>" /></td>
		<td><input type="text" size="30" name="tekserverentals_request_lastname" value="<?php echo $tekserverentals_request_lastname; ?>" /></td>
        </tr>
        <tr>
        <td>Email</td>
        <td colspan="2"><input type="text" size="30" name="tekserverentals_request_email" value="<?php echo $tekserverentals_request_email; ?>" /></td>
        </tr>
        <td>Company</td>
        <td colspan="2"><input type="text" size="30" name="tekserverentals_request_company" value="<?php echo $tekserverentals_request_company; ?>" /></td>
        </tr>
        <tr>
        <td>Phone Number</td>
        <td colspan="2"><input type="text" size="14" name="tekserverentals_request_phone" value="<?php echo $tekserverentals_request_phone; ?>" /></td>
        </tr>
        <tr>
        <td>Address</td>
        <td colspan="2"><input type="text" size="30" name="tekserverentals_request_address" value="<?php echo $tekserverentals_request_address; ?>" /></td>
		</tr>
		<tr>
		<td>City <input type="text" size="30" name="tekserverentals_request_city" value="<?php echo $tekserverentals_request_city; ?>" /></td>
        <td>State <input type="text" size="2" name="tekserverentals_request_state" value="<?php echo $tekserverentals_request_state; ?>" /></td>
        <td>Zip Code <input type="text" size="10" name="tekserverentals_request_zip" value="<?php echo $tekserverentals_request_zip; ?>" /></td>
		</tr>
    </table>
    <?php

}	//end tekserverentals_display_request_meta_box_fields( $rentalrequest )



//store custom field data
add_action( 'save_post', 'tekserverentals_save_request_fields', 5, 2 );
function tekserverentals_save_request_fields( $tekserverentals_request_id, $rentalrequest ) {

	if( ! isset( $_POST['tekserverentals_nonce'] ) ) {
	
    	return $tekserverentals_request_id;
    
    }	//end if( ! isset( $_POST['tekserverentals_nonce'] ) )
    $nonce = $_POST['tekserverentals_nonce'];
	if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) ) {
	
	  return $tekserverentals_request_id;
	
	}	//end if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	
		return $tekserverentals_request_id;
	
	}	//end if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    // Check post type for 'rentalrequest'
    if( $rentalrequest->post_type == 'rentalrequest' && current_user_can( 'edit_pages' ) ) {
    
    	//first, get current duration of rental
    	$current_duration = floor(((get_post_meta( $rentalrequest->ID, 'tekserverentals_request_end', true )) - (get_post_meta( $rentalrequest->ID, 'tekserverentals_request_start', true )))/86400);
    	//update start and end dates if changed
    	if( isset( $_POST['tekserverentals_request_start'] ) && $_POST['tekserverentals_request_start'] != '' ) {
    	
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_start', sanitize_text_field( strtotime( $_REQUEST['tekserverentals_request_start'] ) ) );
    	
    	}	//end if( isset( $_POST['tekserverentals_request_start'] ) && $_POST['tekserverentals_request_start'] != '' ) {
    	if( isset( $_POST['tekserverentals_request_end'] ) && $_POST['tekserverentals_request_end'] != '' ) {
    	
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_end', sanitize_text_field( strtotime( $_REQUEST['tekserverentals_request_end'] ) ) );
    	
    	}	//end if( isset( $_POST['tekserverentals_request_end'] ) && $_POST['tekserverentals_request_end'] != '' )
    	//then calculate and update new duration in db
    	update_post_meta($tekserverentals_request_id, 'tekserverentals_request_duration', floor(((get_post_meta( $rentalrequest->ID, 'tekserverentals_request_end', true )) - (get_post_meta( $rentalrequest->ID, 'tekserverentals_request_start', true )))/86400));
    	$new_duration = get_post_meta($tekserverentals_request_id, 'tekserverentals_request_duration', true);
    	//additional calcs for changing fields:
    	// if duration has changed
    		//recalculate line item prices
    		//recalculate tax
    		//recaculate totals based on line item price changes
    	if( $current_duration != $new_duration ) {
    	
    		$request = p2p_type( 'line_items_to_rental_requests' )->get_connected($tekserverentals_request_id);
    		while ( $request->have_posts() ) : $request->the_post();
    		
    			$child_id = get_the_ID();
    			//get current qty & price
    			$current_qty = get_post_meta( $child_id, 'tekserverentals_line_item_qty', true );
    			$current_price = get_post_meta( $child_id, 'tekserverentals_line_item_price', true );
    			//update lineitem price
				$new_price = tekserverentals_update_line_item_price($child_id, $new_duration, $current_qty);
				update_post_meta( $child_id, 'tekserverentals_line_item_price', $new_price );
				//update totals
				tekserverentals_update_line_item_parent_totals ( $child_id, $new_price, $current_price, $current_qty, $current_qty );
			
			endwhile;
    	
    	}	//end if( $current_duration != $new_duration )
    	//now, check if shipping has been changed, and updated tax/total if so
    	if( isset( $_POST['tekserverentals_request_shipping'] ) && $_POST['tekserverentals_request_shipping'] != '' ) {
    	
			$new_shipping = round( ltrim( sanitize_text_field( $_REQUEST['tekserverentals_request_shipping'] ), "$" ), 2 );
			// if shipping price has changed
			if(  $new_shipping != ( get_post_meta($tekserverentals_request_id, 'tekserverentals_request_shipping', true ) ) ) {
			
				//update tax and total
				tekserverentals_update_rental_request_shipping($tekserverentals_request_id, $new_shipping);
			
			}
			//otherwise, accept input as is
			else {
			
				update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_shipping', round( ltrim( sanitize_text_field( $_REQUEST['tekserverentals_request_shipping'] ), "$" ), 2 ) );
			
			}	//end if(  $new_shipping != ( get_post_meta($tekserverentals_request_id, 'tekserverentals_request_shipping', true ) ) )
    	
    	}	//end if( isset( $_POST['tekserverentals_request_shipping'] ) && $_POST['tekserverentals_request_shipping'] != '' )
    	
        // Store non-calculated data in post meta table if present in post data
        if( isset( $_POST['tekserverentals_request_firstname'] ) && $_POST['tekserverentals_request_firstname'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_firstname', sanitize_text_field( $_REQUEST['tekserverentals_request_firstname'] ) );
        }
        if( isset( $_POST['tekserverentals_request_lastname'] ) && $_POST['tekserverentals_request_lastname'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_lastname', sanitize_text_field( $_REQUEST['tekserverentals_request_lastname'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_email'] ) && $_POST['tekserverentals_request_email'] != '' && is_email($_POST['tekserverentals_request_email'])) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_email', sanitize_email( $_REQUEST['tekserverentals_request_email'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_phone'] ) && $_POST['tekserverentals_request_phone'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_phone', sanitize_text_field( $_REQUEST['tekserverentals_request_phone'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_address'] ) && $_POST['tekserverentals_request_address'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_address', sanitize_text_field( $_REQUEST['tekserverentals_request_address'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_city'] ) && $_POST['tekserverentals_request_city'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_city', sanitize_text_field( $_REQUEST['tekserverentals_request_city'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_state'] ) && $_POST['tekserverentals_request_state'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_state', sanitize_text_field( $_REQUEST['tekserverentals_request_state'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_zip'] ) && $_POST['tekserverentals_request_zip'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_zip', sanitize_text_field( $_REQUEST['tekserverentals_request_zip'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_company'] ) && $_POST['tekserverentals_request_company'] != '' ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_company', sanitize_text_field( $_REQUEST['tekserverentals_request_company'] ) );
    	}
    	if( isset( $_POST['tekserverentals_request_delivery'] ) ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_delivery', $_REQUEST['tekserverentals_request_delivery'] );
        }
        if( isset( $_POST['tekserverentals_request_pickup'] ) ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_request_pickup', $_REQUEST['tekserverentals_request_pickup'] );
        }
        if( isset( $_POST['tekserverentals_delivery_loc'] ) ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_delivery_loc', $_REQUEST['tekserverentals_delivery_loc'] );
        }
        if( isset( $_POST['tekserverentals_request_pickup_loc'] ) ) {
            update_post_meta( $tekserverentals_request_id, 'tekserverentals_pickup_loc', $_REQUEST['tekserverentals_request_pickup_loc'] );
        }
   
	}	//end if( $rentalrequest->post_type == 'rentalrequest' && current_user_can( 'edit_pages' ) )

}	//end tekserverentals_save_request_fields( $tekserverentals_request_id, $rentalrequest )



if ( ! function_exists('tekserverentals_line_items') ) {

	// Register Custom Post Type for Line Items
	function tekserverentals_line_items() {

		$labels = array(
			'name'                => 'Line Items',
			'singular_name'       => 'Line Item',
			'menu_name'           => 'Line Item',
			'parent_item_colon'   => 'Parent Line Item:',
			'all_items'           => 'All Line Items',
			'view_item'           => 'View Line Item',
			'add_new_item'        => 'Add New Line Item',
			'add_new'             => 'New Line Item',
			'edit_item'           => 'Edit Line Item',
			'update_item'         => 'Update Line Item',
			'search_items'        => 'Search Line Items',
			'not_found'           => 'No Line Items found',
			'not_found_in_trash'  => 'No Line Items found in Trash',
		);
		$rewrite = array(
			'slug'                => 'line-item',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => 'lineitem',
			'description'         => 'Line Items in Rental Requests',
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
			'query_var'           => 'lineitem',
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
			'menu_icon'			  => 'dashicons-list-view',
		);
		register_post_type( 'lineitem', $args );

	}	//end tekserverentals_line_items()

}	//end if ( ! function_exists('tekserverentals_line_items') )

// Hook into the 'init' action
add_action( 'init', 'tekserverentals_line_items', 0 );



//create meta box for custom fields for Line Items
add_action( 'admin_init', 'tekserverentals_add_line_item_fields_meta_box' );
function tekserverentals_add_line_item_fields_meta_box() {

    add_meta_box( 'tekserverentals_line_items', 'Rental Line Item', 'tekserverentals_display_line_item_meta_box_fields', 'lineitem', 'normal', 'core' );

}	//end tekserverentals_add_line_item_fields_meta_box()

// Retrieve current details based on Line Item ID
function tekserverentals_display_line_item_meta_box_fields( $lineitem ) {

    $tekserverentals_line_item_qty = absint( get_post_meta( $lineitem->ID, 'tekserverentals_line_item_qty', true ) );
	$tekserverentals_line_item_price = round( ltrim( get_post_meta( $lineitem->ID, 'tekserverentals_line_item_price', true ), "$" ), 2 );
	wp_nonce_field( 'tekserverentals_meta_box', 'tekserverentals_nonce' );
	?>
    <table>
        <tr>
            <td>Quantity: <input type="text" size="4" name="tekserverentals_line_item_qty" value="<?php echo $tekserverentals_line_item_qty; ?>" /></td>
        </tr>
        <tr>
            <td>Price: $<input type="text" size="8" name="tekserverentals_line_item_price" value="<?php echo $tekserverentals_line_item_price; ?>" readonly /></td>
        </tr>
    </table>
    <?php

}	//end tekserverentals_display_line_item_meta_box_fields( $lineitem )



//make sure internal comments aren't shown to cust
add_filter( 'content_save_pre', 'tekserverentals_private_rental_notes' );
function tekserverentals_private_rental_notes( $content ) {

	$old_content = get_post();
	if( $old_content->post_type == 'rentalrequest' ) {
		$old_content = $old_content->post_content;
		$old_value = explode( '-<b>Original Notes from Customer</b>', $old_content );
		$new_content = $_REQUEST['content'];
		$new_value = explode( '-<b>Original Notes from Customer</b>', $new_content );
		$need_privacy = strpos( $new_content, '-<b>Original Notes from Customer</b>' );
		if( $need_privacy === false ) {
		
			return htmlspecialchars( $old_value[0] ) . '-<b>Original Notes from Customer</b><br />' . htmlspecialchars( $new_value[0] );
		
		}
		else {
		
			return htmlspecialchars( $old_value[0] ) . '-<b>Original Notes from Customer</b><br/>' . htmlspecialchars( $new_value[1] );
		
		}	//end if( $need_privacy === false )
	
	}
	else {
	
		return $content;
	
	}	//end if( $old_content->post_type == 'rentalrequest' )

}	//end tekserverentals_private_rental_notes( $content )


//store custom field data for line items
//line items alway import rates whther generated by customer request or in the back end
//included values are:
/*
* _tekserverentals_line_item_deposit - deposit per 1 qty
* _tekserverentals_line_item_dprice - price per 1st day
* _tekserverentals_line_item_edprice - price per extra day
* _tekserverentals_line_item_wprice - price per 1st week (7day)
* _tekserverentals_line_item_ewprice - price per extra week
*/
add_action( 'save_post', 'tekserverentals_save_line_item_fields', 5, 2 );
function tekserverentals_save_line_item_fields( $lineitem_id, $lineitem ) {

	if( ! isset( $_POST['tekserverentals_nonce'] ) ) {
	
    	return $lineitem_id;
    
    }	//end if( ! isset( $_POST['tekserverentals_nonce'] ) )
    $nonce = $_POST['tekserverentals_nonce'];
	if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) ) {
	
	  return $lineitem_id;
	
	}	//end if( ! wp_verify_nonce( $nonce, 'tekserverentals_meta_box' ) )
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	
		return $lineitem_id;
	
	}	//end if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    // Check post type for 'lineitem'
    if( $lineitem->post_type == 'lineitem' ) {
    
    	$currentprice = get_post_meta( $lineitem_id, 'tekserverentals_line_item_price', true );
    	$current_qty = get_post_meta( $lineitem_id, 'tekserverentals_line_item_qty', true );
    	//get latest info from parent request before saving
		$request = p2p_type( 'line_items_to_rental_requests' )->get_connected( $lineitem_id );
		while ( $request->have_posts() ) : $request->the_post();
		
			$parent_id = get_the_ID();
			$duration = get_post_meta( $parent_id, 'tekserverentals_request_duration', true );
		
		endwhile;
		//calculate new price based on entered qty and returned duration (allows user to manually edit price arbitrarily... because tekserve)
		$new_qty = absint( sanitize_text_field( $_REQUEST['tekserverentals_line_item_qty'] ) );
		$new_price = tekserverentals_update_line_item_price( $lineitem_id, $duration, $new_qty );
		//compare, and update line item price, tax, grand total, deposit, and total w/ deposits if the line item price changes
		if( $currentprice != $new_price ) {
		
			//update parent totals, keeping the new lineitem price
			tekserverentals_update_line_item_parent_totals( $lineitem_id, $new_price, $currentprice, $new_qty, $current_qty );
		
		}
		else {
		
			//otherwise, just accept new input
			$new_price = round( ltrim( sanitize_text_field( $_REQUEST['tekserverentals_line_item_price'] ), "$" ), 2 );
		
		}	//end if( $currentprice != $new_price )
        // Store data in post meta table if present in post data. Uses calculated price or entered price.
        if( isset( $_POST['tekserverentals_line_item_qty'] ) && $_POST['tekserverentals_line_item_qty'] != '' ) {
        
            update_post_meta( $lineitem_id, 'tekserverentals_line_item_qty', $new_qty );
        
        }	//end if( isset( $_POST['tekserverentals_line_item_qty'] ) && $_POST['tekserverentals_line_item_qty'] != '' )
        if( isset( $_POST['tekserverentals_line_item_price'] ) && $_POST['tekserverentals_line_item_price'] != '' ) {
        
            update_post_meta( $lineitem_id, 'tekserverentals_line_item_price', $new_price );
        
        }	//end if( isset( $_POST['tekserverentals_line_item_price'] ) && $_POST['tekserverentals_line_item_price'] != '' )
    
    }	//end if( $lineitem->post_type == 'lineitem' )

}	//end tekserverentals_save_line_item_fields( $lineitem_id, $lineitem )



//function updates totals for rental request given a request ID, and new shipping value
function tekserverentals_update_rental_request_shipping($request_id, $new_shipping) {

	//shipping from parent request
	$shipping = get_post_meta( $request_id, 'tekserverentals_request_shipping', true );
	//tax from parent request
	$tax = get_post_meta( $request_id, 'tekserverentals_request_tax', true );
	//parent request total before tax & shipping
	$old_total = get_post_meta( $request_id, 'tekserverentals_request_total', true ) - $tax - $shipping;
	//calculate total tax with new shipping value
	$new_tax = $tax - ( /*$global_tax_rate*/ .08875 * $shipping )  + ( /*$global_tax_rate*/ .08875 * $new_shipping );
	//calculate new total after tax & shipping, save it
	$new_total = $old_total + $new_tax + $new_shipping;
	update_post_meta( $request_id, 'tekserverentals_request_shipping', $new_shipping, $shipping );
	$deposit = get_post_meta( $parent_id, 'tekserverentals_request_deposits', true );
	$new_total_wdeposits = $new_total + $deposit;
	update_post_meta( $request_id, 'tekserverentals_request_tax', $new_tax, $tax );
	update_post_meta( $request_id, 'tekserverentals_request_total', $new_total);
	update_post_meta( $request_id, 'tekserverentals_request_total_wdeposits', $new_total_wdeposits );//update total+deposit (ugh...)

}	//end tekserverentals_update_rental_request_shipping



//function updates parent request's totals given a old & new price & qty
function tekserverentals_update_line_item_parent_totals( $lineitem, $new_price, $old_price, $new_qty, $old_qty ) {

	$deposit = get_post_meta( $lineitem, '_tekserverentals_line_item_deposit', true );
	//get parent id
	$request = p2p_type( 'line_items_to_rental_requests' )->get_connected($lineitem);
	while ( $request->have_posts() ) : $request->the_post();
	
		$parent_id = get_the_ID();
	
	endwhile;
	//shipping from parent request
	$shipping = get_post_meta( $parent_id, 'tekserverentals_request_shipping', true );
	//tax from parent request
	$tax = get_post_meta( $parent_id, 'tekserverentals_request_tax', true );
	//get old total from parent before tax & shipping
	$old_total = get_post_meta( $parent_id, 'tekserverentals_request_total', true ) - $tax - $shipping;
	//calculate new total
	$new_total = $old_total - $old_price + $new_price; 
	//get old deposit from parent, minus (old) deposit for this line item
	$old_deposit = get_post_meta( $parent_id, 'tekserverentals_request_deposits', true ) - ( $old_qty * $deposit );
	//calculate new total deposit with updated qty
	$new_deposit = $old_deposit + ( $new_qty * $deposit );
	//calculate new tax using new price
	$new_tax = $tax - ( /*global_tax_rate*/ .08875 * $old_price )  + ( /*global_tax_rate*/ .08875 * $new_price );
	//calculate new total, save the new values to parent request
	$new_total = $new_total + $new_tax + $shipping;
	$new_total_wdeposits = $new_total + $new_deposit;
	update_post_meta( $parent_id, 'tekserverentals_request_tax', $new_tax, $tax );//update tax
	update_post_meta( $parent_id, 'tekserverentals_request_deposits', $new_deposit );//update deposit
	update_post_meta( $parent_id, 'tekserverentals_request_total', $new_total);//update total
	update_post_meta( $parent_id, 'tekserverentals_request_total_wdeposits', $new_total_wdeposits );//update total+deposit

}	//end tekserverentals_update_line_item_parent_totals( $lineitem, $new_price, $old_price, $new_qty, $old_qty )



//function calculates new line item price after duration or quantity change
function tekserverentals_update_line_item_price($lineitem, $new_duration, $new_qty) {

	$dprice = get_post_meta( $lineitem, '_tekserverentals_line_item_dprice', true );
	$edprice = get_post_meta( $lineitem, '_tekserverentals_line_item_edprice', true );
	$wprice = get_post_meta( $lineitem, '_tekserverentals_line_item_wprice', true );
	$ewprice = get_post_meta( $lineitem, '_tekserverentals_line_item_ewprice', true );
	$new_price_duration = tekserverentals_calculate_duration_price($new_duration, $dprice, $edprice, $wprice, $ewprice );
	$new_price = $new_price_duration["price"] * $new_qty;
	return $new_price;

}	//end tekserverentals_update_line_item_price

//here's a php version of the front-end calcs we did with simplecart.js for charged weeks, extra days, extra weeks, and final line item price. Could've made that an AJAX call, I suppose. At least this didn't take as long as the JS date approximations...
function tekserverentals_calculate_duration_price($days, $dprice, $edprice, $wprice, $ewprice ) {

	$edays = 0;
	$week = 0;
	$eweeks = 0;
	$dprice = intval( $dprice );
	$edprice = intval( $edprice );
	$wprice = intval( $wprice );
	$ewprice = intval( $ewprice );
	if( $days == 1 ) {
	
		$price = round( $dprice, 2 );
	
	}
	else {
	
		if( $days > 6 ) {
		
			$week = 1;
			$edays = intval( $days - 7 );
			if( $days > 13 ) {
			
				$eweeks = floor( ( $days - 7 ) / 7 );
				$edays = intval( $days - ( ( $eweeks + 1 ) * 7 ) );
				if( $edays > 0 ) {
				
					$dtot = intval( $wprice ) + intval( $edprice * $edays ) + intval( $ewprice * $eweeks );
					$wtot = intval( $wprice ) + intval( $ewprice ) + intval( $ewprice * $eweeks );
					if( $wtot < $dtot ) {
					
						$price = round( $wtot, 2 );
					
					}
					else {
					
						$price = round( $dtot, 2 );
					
					}	//end if( $wtot < $dtot )
				
				}
				else {
				
					$price = round( intval( $wprice ) + intval( $ewprice * $eweeks ), 2 );
				
				}	//end if( $edays > 0 )
			
			}
			else {
			
				$dtot = intval( $wprice ) + intval( $edprice * $edays );
				$wtot = intval( $wprice ) + intval( $ewprice );
				if( $wtot < $dtot ) {
				
					$price = round( $wtot, 2 );
				
				}
				else {
				
					$price = round( $dtot, 2 );
				
				}	//end if( $wtot < $dtot )
			
			}	//end if( $days > 13 )
		
		}
		else {
			$dtot = intval( $wprice ) + intval( $edprice * $edays );
			if( $wprice < $dtot ) {
			
				$price = round( $wprice, 2 );
			
			}
			else {
			
				$price = round( $dtot, 2 );
			
			}	//end if( $wprice < $dtot )
		
		}	//end if( $days > 6 )
	
	}	//end if( $days == 1 )
	$price_durations = array (
	
		"price" 	=>	$price,
		"days"		=>	$days,
		"edays"		=>	$edays,
		"week"		=>	$week,
		"eweeks"	=>	$eweeks
	
	);
	return $price_durations;

}	//end tekserverentals_calculate_duration_price($days, $dprice, $edprice, $wprice, $ewprice )



// Connect Line Items to Requests
add_action( 'p2p_init', 'tekserverentals_connect_line_items_to_request' );
function tekserverentals_connect_line_items_to_request() {

	p2p_register_connection_type( array(
	
		'name' => 'line_items_to_rental_requests',
		'from' => 'lineitem',
		'to' => 'rentalrequest',
		'cardinality' => 'many-to-one',
		'admin_box' => array(
			'show' => 'from',
			'context' => 'advanced'
		),
		'title' => array(
			'from' => __( 'Return to Rental', 'tekserve-rentals' ),
			'to' => __( 'Line Items', 'tekserve-rentals' )
		), 
			'from_labels' => array(
			'singular_name' => __( 'Line Item', 'tekserve-rentals' ),
			'search_items' => __( 'Search Line Items', 'tekserve-rentals' ),
			'not_found' => __( 'No Line Items found.', 'tekserve-rentals' ),
			'create' => __( 'Add Line Items', 'tekserve-rentals' ),
		),
			'to_labels' => array(
			'singular_name' => __( 'Rental Request', 'tekserve-rentals' ),
			'search_items' => __( 'Search Rental Requests', 'tekserve-rentals' ),
			'not_found' => __( 'No Rental Requests found.', 'tekserve-rentals' ),
			'create' => __( 'Add Line Item to Rental Request', 'tekserve-rentals' ),
		),	
	
	) );

}	//end tekserverentals_connect_line_items_to_request()


//meta box for displaying line items in edit request admin pages
add_action( 'admin_init', 'tekserverentals_requests_line_items_meta_box' );
function tekserverentals_requests_line_items_meta_box() {

    add_meta_box( 'tekserverentals_requests_line_items', 'Rental Line Items', 'tekserverentals_display_request_line_items_meta_box', 'rentalrequest', 'normal', 'high' );

}	//end tekserverentals_requests_line_items_meta_box



function tekserverentals_display_request_line_items_meta_box( $post, $no_output = NULL ) {

	$line_items = p2p_type( 'line_items_to_rental_requests' )->get_connected($post);
	$line_items_output = '
	<table>
		<thead>
			<tr style="border: 1px solid #004d72;">
				<td style="font-weight: bold; padding: 1em 1em 1em 0; font-weight: 900;">Name</td>
				<td style="font-weight: bold; padding: 1em 1em 1em 0; font-weight: 900;">Qty</td>
				<td style="font-weight: bold; padding: 1em 1em 1em 0; font-weight: 900;">Price</td>';
	if( is_array( $no_output && $no_output['output'] != 'yes' ) ) {
	
		$line_items_output .= '<td class="line_item_update_link"></td>';
	
	}	//end if( is_array( $no_output && $no_output['output'] != 'yes' ) )
	$line_items_output .= '
			</tr>
		</thead>
		<tbody>';
	$itemtotal = 0;
	while ( $line_items->have_posts() ) : $line_items->the_post();
	
			$line_items_output .= '<tr>
				<td style="padding: 1em 1em 1em 0;">' . get_the_title() . '</td>
				<td style="padding: 1em 1em 1em 0;">' . get_post_meta( get_the_ID(), 'tekserverentals_line_item_qty', true ) . '</td>
				<td style="padding: 1em 1em 1em 0;">$' . get_post_meta( get_the_ID(), 'tekserverentals_line_item_price', true ) . '</td>';
			$itemtotal = $itemtotal + ( intval( get_post_meta( get_the_ID(), 'tekserverentals_line_item_qty', true ) ) * intval( get_post_meta( get_the_ID(), 'tekserverentals_line_item_price', true ) ) );
			if( is_array( $no_output ) && $no_output['output'] != 'yes' ) {
			
				$line_items_output .= '<td style="padding: 1em;" class="line_item_update_link"><a href="' . get_edit_post_link( get_the_ID() ) . '">Update This Item</a></td>'; 
			
			}	//end if( is_array( $no_output ) && $no_output['output'] != 'yes' )
	
	endwhile;
	$line_items_output .= '</table>';
	if( is_array( $no_output ) ) {
	
		echo $line_items_output;
		return $itemtotal;
	
	}
	else {
	
		return $line_items_output; 
	
	}	//end if( is_array( $no_output ) )

}	//end tekserverentals_display_request_line_items_meta_box( $post, $no_output = NULL )



// Add Shortcode for Generating rental item by ID
add_shortcode( 'rentalitem', 'tekserverental_item' );
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

}	//end tekserverental_item( $atts )



// Add Shortcode for Generating rental category by slug
add_shortcode( 'rentalitemcategory', 'tekserverental_item_category' );
function tekserverental_item_category( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'slug'		=>	'',
			'drawer'	=>	'',
			'expanded'	=>	'false',
			'id'		=>	'',
		), $atts )
	);
	//get rental items using category slug
	$args = array(
	
		'category_name' 	=> $slug,
		'post_status'		=> 'publish',
		'post_type'			=> 'rentalproduct',
		'numberposts'		=> 50
	
	);
	$products = get_posts( $args );
	// Code to create output; includes class names to create items for simplecart checkout
	//format rental items into vc rows
	$cat_out = '';
	$cat_content = '';
	//use vc codes for nice row/column layout if vc is installed 
	if( function_exists( 'vc_map' ) ) {
	
		$i = 0;
		foreach( $products as $product ) {
		
			if( $i == 0 ) {
			
				$cat_content .= '[vc_row_inner]';
			
			}	//end if( $i == 0 ) 
			$cat_content .= '[vc_column width="1/3"][rentalitem id="';
			$cat_content .= $product->ID;
			$cat_content .= '" /][/vc_column]';
			if( $i == 2 ) {
			
				$cat_content .= '[/vc_row_inner]';
				$i = 0;
			
			}
			else {
			
				$i++;
			
			}	//end if( $i == 2 )
		
		}	//end foreach( $products as $product )
		//add drawer shortcode if drawer is selected
		if( $drawer != '' ) {
		
			if( !$id ) {
			
				$id = 'drawer' . rand(8,8);
			
			}	//end if( !$id )
			$cat_content = '[drawer id="'. $id . '" expanded="' . $expanded . '"]' . $cat_content . '[/drawer]';
		
		}	//end if( $drawer != '' )
// 		/get code from all of the shortcodes in the content 
		$cat_out .= do_shortcode($cat_content);
	
	}
// 	kinda useless if not used w/vc. but if not, will just output formatted array contents.
	else {
	
		$cat_out .= print_r( $products, true );
	
	}	//end if( function_exists( 'vc_map' ) )
	return $cat_out;

}	//end tekserverental_item_category( $atts )



// Add Shortcode for Checkout Form
add_shortcode( 'rentalcheckout', 'tekserverental_checkout' );
function tekserverental_checkout( $atts ) {

	extract( shortcode_atts(
		array(
			'title'		=> '',
			'container'	=> 'h2',
		), $atts )
	);
	if( ! empty( $title ) ) {
	
		$title = '<' . $container . ' class="tekserverental-dates-title">' . $title . '</' . $container . '>';
	
	}
	$custinfo = '<div class="tekserverental-checkout-custinfo">';
	$custinfo .= '<div class="tekserverental-name">
	<label>Name </label>
	<span class="firstname"><input id="entry_0" name="firstname" class="required" title="We need to know your name." maxlength="255" size="25" value=""><label for="entry_0" class="lower required">First Name</label>
		</span>
		<span class="lastname">
			<input id="entry_1" name="lastname" class="required" title="We need to know your name." maxlength="255" size="25" value=""><label  for="entry_1" class="lower required">Last Name</label>
		</span>
		</div>';
	$custinfo .= '<div class="tekserverental-company-name">
		<label class="description" for="entry_2">Company Name (if applicable) </label>
		<div>
			<input id="entry_2" name="companyname" class="element text medium" type="text" size="50" maxlength="255" value=""> 
		</div> 
		</div>';
	$custinfo .= '<div class="tekserverental-email">
		<label class="req" for="entry_3" >Email</label>
		<div>
			<input id="entry_3" name="emailaddress" class="email required" title="We need to know your valid email address to send you a quote." type="email" size="50" maxlength="255" value=""> 
		</div> 
		</div>';
	$custinfo .= '<div class="tekserverental-phone-number">
		<label class="req" for="entry_7">Phone</label>
		<span>
			<input id="entry_7" name="phonenumber" class=" digits required" size="14" maxlength="14" value="" type="tel">
			<label class="lower">(10 digits)</label>
		</span>
		</div>';
	$custinfo .= '<div class="tekserverental-address">
		<label for="element_5" >Billing Address </label>
		<div class="addressone">
			<input id="entry_9" name="addressone" class="large required" value="" type="text">
			<label for="entry_9" class="lower required">Street Address</label>
		</div>
		<div class="addresstwo">
			<input id="entry_10" name="addresstwo" class="element large text" value="" type="text">
			<label for="" class="lower">Address Line 2</label>
		</div>
		<div class="city">
			<input id="entry_11" name="city" class="large required" value="" type="text">
			<label for="entry_11" class="lower required">City</label>
		</div>
		<div class="state">
			<input id="entry_12" name="state" maxlength="2" class="medium required" value="" type="text">
			<label for="entry_12" class="lower required">State</label>
		</div>
		<div class="zip">
			<input id="entry_13" name="zip" class="medium required" maxlength="15" value="" type="text">
			<label for="entry_13" class="lower required">Zip Code</label>
		</div>
	</div>';
	$custinfo .= '</div>';
	$shipping = '<div class="tekserverental-checkout-shipping">';
	$shipping .= '<div class="tekserverental-delivery"><label for="tekserverentals-delivery">Would you like messenger delivery? </label><input type="checkbox" name="tekserverentals-delivery" id="tekserverentals-delivery" value="true"> Yes, bring it to me.';
	$shipping .= '<label for="tekserverentals-delivery-loc">Where would you like us to deliver your rental items? </label><input type="radio" name="tekserverentals-delivery-loc" value="manhattan" checked="checked"> Manhattan <input type="radio" name="tekserverentals-delivery-loc" value="borough"> Bronx, Brooklyn, Queens, or Staten Island</div>';
	$shipping .= '<div class="tekserverental-pickup"><label for="tekserverentals-pickup">Would you like messenger pickup at the end of your rental? </label><input type="checkbox" name="tekserverentals-pickup" id="tekserverentals-pickup" value="true"> Yes, pick it up for me.';
	$shipping .= '<label for="tekserverentals-pickup-loc">Where would you like us to pick up your rental items? </label><input type="radio" name="tekserverentals-pickup-loc" value="manhattan" checked="checked"> Manhattan <input type="radio" name="tekserverentals-pickup-loc" value="borough"> Bronx, Brooklyn, Queens, or Staten Island</div>';
	$shipping .= '</div>';
	$form = '<form id="tekserverentals-checkout-form">';
	$form .= $shipping;
	$form .= $custinfo;
	$form .= '<div class="tekserverental-additional-info"><label for="entry_14">Enter any additional requests </label><textarea name="additionalinfo" id="entry_14" rows="4" cols="60">&nbsp;</textarea></div>';
	$form .= '</form>';
	$buttons = '<div class="tekserverentals-show-cart-button-container"></div><a href="javascript:;" class="simpleCart_checkout button tekserverental-checkout-submit">Submit Request</a>';
	$out = '<div class="tekserverental-checkout-form">' . $title;
	$out .= $form;
	$out .= $buttons;
	$out .= '<div class="validationerrors"></div></div>';
	return $out;

}	//end tekserverental_checkout( $atts )




// Add Shortcode for Dates Form
//e.g. [rentaldateform title="The Title" container="h1"]
add_shortcode( 'rentaldateform', 'tekserverental_date_form' );
function tekserverental_date_form( $atts ) {
	
	extract( shortcode_atts(
		array(
			'title'		=> '',
			'container'	=> 'h2',
		), $atts )
	);
	if( ! empty( $title ) ) {
	
		$title = '<' . $container . ' class="tekserverental-dates-title">' . $title . '</' . $container . '>';
	
	}
	$form = '<div class="tekserverental-dates-form"><form id="tekserverentals-checkout-dates">';
	$form .= '<div class="tekserverental-dates-start"><h3><label class="required" for="tekserverentalStartDate">Choose the Start Date for Your Rental</label></h3><div><input type="text" class="required" id="tekserverentalStartDate" name="startdate"></div></div>';
	$form .= '<div class="tekserverental-dates-end"><h3><label class="required" for="tekserverentalEndDate">Choose the End Date for Your Rental</label></h3><div><input type="text" class="required" id="tekserverentalEndDate" name="enddate"></div></div>';
	$form .= '</form></div>';
	$button = '<a class="button tekserverentals-dates-button" href="javascript:;">Set Dates</a>';
	$out = '<div class="tekserverental-dates">';
	$out .= $title;
	$out .= $form;
	$out .= '<div class="tekserverentals-show-cart-button-container"></div>';
	$out .= '</div>';
	return $out;

}	//end tekserverental_date_form( $atts )



// Add Shortcode for Rental Cart
add_shortcode( 'rentalcart', 'tekserverental_cart' );
function tekserverental_cart( $atts ) {

	extract( shortcode_atts(
		array(
			'title'		=> '',
			'container'	=> 'h2',
		), $atts )
	);
	if( ! empty( $title ) ) {
	
		$title = '<' . $container . ' class="tekserverental-cart-title">' . $title . '</' . $container . '>';
	
	}
	$cart = '<div class="tekserverentals-cart-container" style="display: none;">' . $title;
	$cart .= '<div class="tekserverentals-cart-message" style="display: none;"><h3>Important Note</h3><p class="duration"></p><p class="shipping"></p></div>';
	$cart .= '<div class="tekserverentals-cart">';
	$cart .= '<div class="tekserverentals-cart-duration">';
	$cart .= '<span>Rental Duration:</span>&nbsp;<div class="tekserverentals-cart-duration-value"></div>&nbsp;days.';
	$cart .= '</div>';
	$cart .= '<div class="tekserverentals-cart-items-title"><span>Rental Items:</span></div>';
	$cart .= '<div class="simpleCart_items tekserverentals-cart-items"></div>';
	$cart .= '<div class="tekserverentals-cart-totals">';
	$cart .= '<div class="tekserverentals-cart-totals-subtotal"><span>Subtotal</span><div class="simpleCart_total"></div></div>';
	$cart .= '<div class="tekserverentals-cart-totals-shipping"><span>Shipping</span><div class="simpleCart_shipping"></div></div>';
	$cart .= '<div class="tekserverentals-cart-totals-tax"><span>Tax</span><div class="simpleCart_tax"></div></div>';
	$cart .= '<div class="tekserverentals-cart-totals-deposit"><span>Total Deposit</span><div class="tekserverentals-cart-deposits"></div></div>';
	$cart .= '<div class="tekserverentals-cart-totals-grand"><span>Total Cost</span><div class="simpleCart_grandTotal"></div></div>';
	$cart .= '</div>';
	$cart .= '<div class="tekserverentals-cart-buttons">';
	$cart .= '<a class="button tekserve-cart-buttons-changedates" href="#tekserve-rentals-dates">Change Dates</a>';
	$cart .= '<div class="button emptyCart-button tekserve-cart-buttons-removeall">Remove All Items</div>';
	$cart .= '<a href="#tekserve-rentals-checkout" class="button tekserve-cart-buttons-gotoform" title="Enter Personal / Shipping Info and Submit Request">Enter Info & Send Request</a>';
	$cart .= '</div>';
	$cart .= '</div>';
	$cart .= '</div>';
	return $cart;

}	//end tekserverental_cart( $atts )



//Add VC buttons for shortcodes if VC is installed
if (function_exists('vc_map')) {

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
	
	$args = array (
		'title_li'	=> '',
		'echo'		=> 0
	);
	$category_list = get_categories( $args );
	$slug_array = array();
	$i = 0;
	foreach( $category_list as $category ) {
	
		$slug_array[$i] = $category->slug;
		$i++;
	
	}	//end foreach( $category_list as $category )
	$cat_array = print_r( $slug_array, 'true' );
	
	vc_map( array(
	   "name" => __("Rental Item Category Drawer"),
	   "base" => "rentalitemcategory",
	   "class" => "",
	   "icon" => "icon-wpb-rentalitemcat",
	   "category" => __('Content'),
	   "params" => array(
		   array(
				 "type" => "dropdown",
				 "holder" => "div",
				 "class" => "",
				 "heading" => __("Category "),
				 "param_name" => "slug",
				 "value" => $slug_array,
				 "description" => __("Select the slug of the Rental Item Category to display. Required."),
				 "admin_label" => TRUE
			  ),
			array(
				 "type" => "textfield",
				 "holder" => "div",
				 "class" => "",
				 "heading" => __("ID"),
				 "param_name" => "id",
				 "value" => "",
				 "description" => __("Unique ID of this drawer."),
				 "admin_label" => TRUE
			),
			array(
			 "type" => "dropdown",
			 "holder" => "div",
			 "class" => "",
			 "heading" => __("Open on Page Load"),
			 "param_name" => "expanded",
			 "value" => array("false", "true"),
			 "description" => __("Select whether this drawer will be opened or closed when the page loads. False (closed) is default."),
			 "admin_label" => True
		  ),
			array(
				 "type" => "textfield",
				 "holder" => "div",
				 "class" => "noSe",
				 "heading" => __("drawer"),
				 "param_name" => "drawer",
				 "value" => "yes",
				 "description" => __("It's a drawer. Leave this alone."),
				 "admin_label" => FALSE
			)
		)
	)	);
	
	$header_dropdown = array(
		'Default'					=> '',
		'<h1>Header One </h1>'		=> 'h1',
		'<h2>Header Two</h2>'		=> 'h2',
		'<h3>Header Three</h3>'		=> 'h3',
		'<h4>Header Four</h4>'		=> 'h4',
		'<h5>Header Five</h5>'		=> 'h5',
		'<h6>Header Six</h6>'		=> 'h6',
		'<div>Div</div>'			=> 'div',
		'<span>Span</span>'			=> 'span',
		'<p>Paragraph</p>'			=> 'p',
	);
	
	vc_map( array(
	   "name" 						=> "Rental Request Checkout Form",
	   "base" 						=> "rentalcheckout",
	   "class" 						=> "",
	   "show_settings_on_create"	=> false,
	   "icon" 						=> "icon-wpb-rentalcheckout",
	   "category" 					=> 'Content',
	   "params"						=> array(
											array(
											 "type" => "textfield",
											 "holder" => "div",
											 "class" => "",
											 "heading" => __("Title"),
											 "param_name" => "title",
											 "value" => "",
											 "description" => __("Text for title of this form. Leave blank for no title."),
											 "admin_label" => TRUE
											),
											array(
											 "type" => "dropdown",
											 "holder" => "div",
											 "class" => "",
											 "heading" => "Container Element",
											 "param_name" => "container",
											 "value" => $header_dropdown,
											 "description" => __("Select type of element to display title in. Default is H2."),
											 "admin_label" => TRUE
										  ),
										),
	)	);
	
	vc_map( array(
	   "name" 						=> "Rental Request Dates Form",
	   "base"						=> "rentaldateform",
	   "class"						=> "",
	   "show_settings_on_create" 	=> false,
	   "icon" 						=> "icon-wpb-rentaldateform",
	   "category" 					=> __('Content'),
	   "params"						=> array(
											array(
											 "type" => "textfield",
											 "holder" => "div",
											 "class" => "",
											 "heading" => __("Title"),
											 "param_name" => "title",
											 "value" => "",
											 "description" => __("Text for title of this form. Leave blank for no title."),
											 "admin_label" => TRUE
											),
											array(
											 "type" => "dropdown",
											 "holder" => "div",
											 "class" => "",
											 "heading" => "Container Element",
											 "param_name" => "container",
											 "value" => $header_dropdown,
											 "description" => __("Select type of element to display title in. Default is H2."),
											 "admin_label" => TRUE
										  ),
										),
	)	);
	
	vc_map( array(
	   "name" 						=> __("Rental Request Cart"),
	   "base" 						=> "rentalcart",
	   "class" 						=> "",
	   "show_settings_on_create"	=> false,
	   "icon"						=> "icon-wpb-rentalcart",
	   "category"					=> __('Content'),
	   "params"						=> array(
											array(
											 "type" => "textfield",
											 "holder" => "div",
											 "class" => "",
											 "heading" => __("Title"),
											 "param_name" => "title",
											 "value" => "",
											 "description" => __("Text for title of this form. Leave blank for no title."),
											 "admin_label" => TRUE
											),
											array(
											 "type" => "dropdown",
											 "holder" => "div",
											 "class" => "",
											 "heading" => "Container Element",
											 "param_name" => "container",
											 "value" => $header_dropdown,
											 "description" => __("Select type of element to display title in. Default is H2."),
											 "admin_label" => TRUE
										  ),
										),
	)	);

}	//end if (function_exists('vc_map'))

//use custom template to display rental request
add_filter( 'template_include', 'tekserverentals_include_request_template', 1 );

function tekserverentals_include_request_template( $template_path ) {
    if( get_post_type() == 'rentalrequest' && is_single() ) {
    
		// checks if the file exists in the theme first,
		// otherwise serve the file from the plugin
		if( $theme_file = locate_template( array ( 'single-rentalrequest.php' ) ) ) {
		
			$template_path = $theme_file;
		
		}
		else {
		
			$template_path = plugin_dir_path( __FILE__ ) . 'single-rentalrequest.php';
		
		}	//end if( $theme_file = locate_template( array ( 'single-rentalrequest.php' ) ) )
    
    }	//end if( get_post_type() == 'rentalrequest' && is_single() )
    return $template_path;

}	//end tekserverentals_include_request_template( $template_path )



//register option to set thank you text shown to cust after request submission, includes default message
add_action( 'admin_init', 'tekserve_rentals_register_settings' );
function tekserve_rentals_register_settings() {

	add_option( 'tekserve_rentals_ty_title', 'Thank You!');
	add_option( 'tekserve_rentals_ty_body', '<p style="margin-bottom: 1em;">Your request has been sent to Tekserve\'s Rental Department folks, who will take a gander at it and contact you shortly. We just need a bit of time to make sure everything will be ready and waiting for you wherever you need it, and then we can set up your deposit and complete your reservation.</p><p style="margin-bottom: 2.5em;">Take a look below and make sure everything is set up the way you want it; if you need to make changes, just send us a quick email at rentals@tekserve.com, or call us up at 212.929.3645 and we\'ll help you out. Talk you you soon!</p>');
	register_setting( 'default', 'tekserve_rentals_ty_title' ); 
	register_setting( 'default', 'tekserve_rentals_ty_body' ); 

}	//end tekserve_rentals_register_settings()



//register thank you text options page
add_action('admin_menu', 'tekserve_rentals_register_options_page');
function tekserve_rentals_register_options_page() {

	add_options_page('Tekserve Rentals', 'Tekserve Rentals', 'manage_options', 'tekserve-rentals-options', 'tekserve_rentals_options_page');

}	//end tekserve_rentals_register_options_page()



//generate content of thank you text options page
function tekserve_rentals_options_page() {

	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Tekserve Rentals</h2>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'default' ); ?>
		<h3>"Thank-You" Text</h3>
			<p>Edit the heading and body of the text that appears at the top of the confirmation screen after a user submits a request for a rental.</p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="tekserve_rentals_ty_title">Heading</label></th>
					<td><input type="text" id="tekserve_rentals_ty_title" name="tekserve_rentals_ty_title" value="<?php echo get_option('tekserve_rentals_ty_title'); ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="tekserve_rentals_ty_body">Body</label></th>
					<td><textarea rows="10" cols="80" id="tekserve_rentals_ty_body" name="tekserve_rentals_ty_body" ><?php echo get_option('tekserve_rentals_ty_body'); ?></textarea></td>
				</tr>
			</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php

}	//end tekserve_rentals_options_page()