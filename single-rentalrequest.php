<?php
 
/**
 * Template Name: Tekserve Rental Reques - Single
 * Description: Used as a page template to display a single rental request after the form is filled out by the requester.
 */
 
/*****
	Heading
*****/
get_header();if ( is_front_page() ) {
		// We don't need it on frontpage
		$tmq_brcr = 'tmq_hide';
	} else {
		$tmq_brcr = ot_get_option( 'tmq_brcr' );
		if ( empty( $tmq_brcr ) ) {
			$tmq_brcr = 'tmq_show';
		}
	}
	
    // Get Banner Settings
	$tmq_slidertype = get_post_meta( $post->ID, 'tmq_slidertype', true );
	
	if ( empty( $tmq_slidertype ) ) {
		// Default value if nothing is set - Usually when theme is installed on a site with old content
		$tmq_banner_area_setting = ot_get_option( 'tmq_banner_area_setting' );
		if ( !empty( $tmq_banner_area_setting ) ) {
			// read from theme options
			$tmq_slidertype = $tmq_banner_area_setting;
		} else {
			// Set my favorite value as default
			$tmq_slidertype = 'tmq_text';
		}
	}
	
	if ( $tmq_slidertype == 'tmq_text' ) {

		// TEXT AND BACKGROUND is SET **********
		
		// Get page heading / title
		$tmq_banner_title = get_post_meta( $post->ID, 'tmq_banner_title', true );
		if ( empty( $tmq_banner_title ) ) {
			// Empty? Read from default and add span
			$tmq_banner_title = '<span>' . ot_get_option( 'tmq_default_banner_title' ) . '</span>';
		} else {
			// Add H1 tag to it
			$tmq_banner_title = '<h1>' . $tmq_banner_title . '</h1>';
		}
		
		// Replace post title if default banner title is empty too. v1.2
		if ( $tmq_banner_title == '<span></span>' ) {
			$tmq_banner_title = '<span>' . get_the_title() . '</span>';
		}
		
		// Get page sub-heading / sub-title
		$tmq_banner_subtitle = get_post_meta( $post->ID, 'tmq_banner_subtitle', true );
		if ( empty( $tmq_banner_subtitle ) ) {
			// Empty? Read from default
			$tmq_banner_subtitle = ot_get_option( 'tmq_default_banner_subtitle' );
		}
		
		// Get page background ( I Should Move This to Header )
		$tmq_banner_background_image = get_post_meta( $post->ID, 'tmq_banner_background_image', true );	
		
	} elseif ( $tmq_slidertype == 'tmq_revolution' ) {
		
		// REVOLUTION SLIDER is SET **********
		$tmq_revolution_slider = get_post_meta($post->ID, 'tmq_revolution_slider', true);
		
	} elseif ( $tmq_slidertype == 'tmq_flex' ) {
	
		// Flex Slider Images
		$tmq_flex_gallery = get_post_meta($post->ID, 'tmq_flex_gallery', true);
	}
?>
		<!-- content 
			================================================== -->
		<div id="content">
			<div class="inner-content">
				<div id="page-content">
				<!-- slider 
					================================================== -->
				<?php if ( $tmq_slidertype == 'tmq_text' ) { ?>
				<div id="page-banner">
					<?php echo $tmq_banner_title;?>
					<p><?php echo $tmq_banner_subtitle;?></p>
					<?php
						if ( ( function_exists( 'tmq_show_bc' ) ) && ( $tmq_brcr == 'tmq_show' ) ) {
								tmq_show_bc();
						}
					?>
				</div>
				<?php } ?>
				<?php if ( $tmq_slidertype == 'tmq_revolution' ) { ?>
				<div id="slider">
					<!--
					#################################
						- THEMEPUNCH BANNER -
					#################################
					-->
					<?php
						if ( function_exists( 'putRevSlider' ) ) {
							putRevSlider( $tmq_revolution_slider );
						}
					?>
				</div>
				<?php } ?>
				<?php if ( $tmq_slidertype == 'tmq_flex' ) { 
					// Check if it has more than one image to show it as slideshow
					if ( !empty( $tmq_flex_gallery ) ) {
						$tmq_flex_gallery_array = explode(',', $tmq_flex_gallery);
						?>
						<div id="slider">
							<!--
							#################################
									- FlexSlider Banner -
							#################################
							-->
							<div class="flexslider">
								<ul class="slides">
								<?php 
									foreach ( $tmq_flex_gallery_array as $tmq_flex_image ) {
										$img_title = get_the_title( $tmq_flex_image );   // title
										//$img_caption = get_post_field('post_excerpt', $tmq_flex_image ); // Get Caption - We don't use it now
										
										// Get slideshow size
										$big_array = image_downsize( $tmq_flex_image, 'full' );
										$img_url = $big_array[0];
										?>
											<li><img src="<?php echo esc_url($img_url); ?>" alt="" title="<?php echo esc_attr($img_title); ?>" /></li>
										<?php
									}
								?>
								</ul>
							</div>
						</div>
						<?php }
					} ?>
				<!-- End slider -->

				<!-- Content sections 
					================================================== -->
				<div class="content-sections">
					<div class="blog-box no-sidebar">
					<?php
								tekserve_rentals_output_thanks();
								tekserve_rentals_output_request();
					?>
					</div>
			</div>
				<!-- End content sections -->
<?php get_footer();

 
 
//* Customize the post info function to display custom fields */
// add_action( 'genesis_entry_content', 'tekserve_rentals_output_request', 1 );
function tekserve_rentals_output_request() {

	//get the id and use it here, can now get other post's data as necessary
	global $wp_query;
	$postid = $wp_query->post->ID;
	wp_reset_query();
	
	//get info from custom fields, build customer data output
	$delivery = get_post_meta( $postid, 'tekserverentals_request_delivery', true );
	$pickup = get_post_meta( $postid, 'tekserverentals_request_pickup', true );
	$cust_data = '<div class="tekserve_rental_request_customer">';
	if( $company = get_post_meta( $postid, 'tekserverentals_request_company', true ) ) {
	
		$cust_data .= "<div><b>COMPANY</b><br />" . $company . "</div>";
	
	}	//end if( $company = get_post_meta( $postid, 'tekserverentals_request_company', true ) )
	$cust_data .= "<div><b>ADDRESS</b><br />". get_post_meta( $postid, 'tekserverentals_request_address', true ) . "<br />";
	$cust_data .= get_post_meta( $postid, 'tekserverentals_request_city', true ) . ", " . get_post_meta($postid, 'tekserverentals_request_state', true) . " " . get_post_meta($postid, 'tekserverentals_request_zip', true);
	$cust_data .= "</div>";
	if( $phone = get_post_meta( $postid, 'tekserverentals_request_phone', true ) ) {
	
		$phone = substr( $phone, 0, 3 ) . "." . substr( $phone, 3, 3 ) . "." . substr( $phone, 6 );
		$cust_data .= "<div><b>PHONE</b><br />" . $phone . "</div>";
	
	}	//end if( $phone = get_post_meta( $postid, 'tekserverentals_request_phone', true ) )
	if( $email = get_post_meta( $postid, 'tekserverentals_request_email', true ) ) {
	
		$cust_data .= "<div><b>EMAIL</b><br />" . $email . "</div>";
	
	}	//end if( $email = get_post_meta( $postid, 'tekserverentals_request_email', true ) )
	$cust_data .= "</div>";
	
	//get delivery ingo from custom fields, build shipping info
	$delivery_data = "";
	if( $delivery OR $pickup ) {
	
		$delivery_data = '<div class="tekserve_rental_request_delivery"><h3>Shipping:</h3><div>';
		if( $delivery ) {
		
			$delivery_data .= '<b>DELIVERED TO</b><br />';
			$dloc = ( get_post_meta( $postid, 'tekserverentals_delivery_loc', true ) == 'manhattan' ) ? 'Manhattan (below 96th St.)' : 'Bronx, Brooklyn, Manhattan above 96th, Queens, or SI';
			$delivery_data .= $dloc;
			$delivery_data .= '<br />';
		
		}	//end if( $delivery )
		if( $pickup ) {
		
			$delivery_data .= '<b>PICKED UP IN</b><br />';
			$ploc = ( get_post_meta( $postid, 'tekserverentals_pickup_loc', true ) == 'manhattan' ) ? 'Manhattan (below 96th St.)' : 'Bronx, Brooklyn, Manhattan above 96th, Queens, or SI';
			$delivery_data .= $ploc;
			$delivery_data .= '<br />';
		
		}	//end if( $pickup )
		$delivery_data .= '</div></div>';
	
	}	//end if( $delivery OR $pickup )
	
	//build and output final html
	$request_data1 = '<div class="tekserve_rental_request_container">';
	$request_data1 .= '<h2>Your Info</h2>';
	$request_data1 .= '<div class="tekserve_rental_request_your_info">' . $cust_data . '</div>';
	$request_data1 .= '<h2>Rental Info</h2><div class="tekserve_rental_request_rental_info">';
	$request_data1 .= '<div class="tekserve_rental_request_line_items">';
	$request_data1 .= '<h3>Equipment:</h3>';
	echo $request_data1;
	//this is in main plugin file; also used in admin for showing line items on edit request pages. will directly output line items table, div
	$line_items_total = tekserverentals_display_request_line_items_meta_box( $postid, array( 'output' => 'yes' ) );
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



// Remove note about customer notes from frontend display (i.e. anything added to $content after cust. has submitted request)
// add_filter( 'the_content', 'tekserve_rental_filter_content' );
function tekserve_rental_filter_content( $value ) {

	$value = explode( '-<b>Original Notes from Customer</b>', $value );
	$notecontent = ( strlen( $value[0] ) < 6 ) ? 'No notes entered.' : $value[0];
	$notes = '<div class="tekserve_rental_request_container"><h2>Additional Notes</h2><div class="tekserve_rental_request_notes">' . $notecontent . '</div></div>';
	return $notes;

}	//end tekserve_rental_filter_content( $value )



// Output Message after form submission above the title. Includes default

function tekserve_rentals_output_thanks() {

	$thankshead = '<h1>' . get_option('tekserve_rentals_ty_title', 'Thank You!') . '</h1>';
	$thanksbody = get_option('tekserve_rentals_ty_body', '<p style="margin-bottom: 1em;">Your request has been sent to Tekserve\'s Rental Department folks, who will take a gander at it and contact you shortly. We just need a bit of time to make sure everything will be ready and waiting for you wherever you need it, and then we can set up your deposit and complete your reservation.</p><p style="margin-bottom: 2.5em;">Take a look below and make sure everything is set up the way you want it; if you need to make changes, just send us a quick email at rentals@tekserve.com, or call us up at 212.929.3645 and we\'ll help you out. Talk you you soon!</p>');
	$thanks = '<div class="tekserve_rentals_thank_you">' . $thankshead . $thanksbody . '</div>';
	echo $thanks;

}	//end tekserve_rentals_output_thanks()

