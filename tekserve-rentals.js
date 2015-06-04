//takes plugin folder location from php upon page init
var tekserveRentalsUrl = tekserveRentalsData.tekserveRentalsUrl + "request.php";



//set alert messages content object values
var tekserveRentalsMessages = {
	duration: 'This quote is for a long term rental. The actual price of this rental will be lower than the estimate below. Please contact us directly at 212.929.3645 or rentals@tekserve.com to get the lowest price for this rental.',
	shipping: 'This order has too many items to provide an accurate shipping estimate. Please contact us directly at rentals@tekserve.com or 212.929.3645 to get the price for shipping this rental.',
}



//set anchors content object values
var tekserveRentalsAnchors = {
	dates: '<a class="shift-up-16em" style="position: relative; top: -16em;" id="tekserve-rentals-dates" name="tekserve-rentals-dates"></a>',
	checkout: '<a class="shift-up-16em" style="position: relative; top: -16em;" id="tekserve-rentals-checkout" name="tekserve-rentals-checkout"></a>',
	submit: '<a class="shift-up-16em" style="position: relative; top: -16em;" id="tekserve-rentals-submit" name="tekserve-rentals-submit"></a>',
	cart: '<a class="shift-up-16em" style="position: relative; top: -16em;" id="tekserve-rentals-cart" name="tekserve-rentals-cart"></a>',
}



//set shipping costs object values
var tekserveRentalsShipcost = {
	mansm: 42,
	manlg: 62,
	borsm: 55,
	borlg: 62,
	error: 500,
}



//create object for 'canonical' fields used in calculations (first form of each type created in each page is used, in case user uses shortcode or vc item more than once per page)
var $tekserveRentalsFields = {};



//after jQuery is loaded
jQuery( document ).ready(function($j) {

	//set values in object for 'canonical' fields used in calculations
	var $dates = $j('.tekserverental-dates-form').first();
	var $shipping = $j('.tekserverental-checkout-form').first().find('.tekserverental-checkout-shipping');
	var $cust = $j('.tekserverental-checkout-form').first().find('.tekserverental-checkout-custinfo');
	$tekserveRentalsFields.startDate = $j($dates).find('.tekserverental-dates-start input');
	$tekserveRentalsFields.endDate = $j($dates).first().find('.tekserverental-dates-end input');
	$tekserveRentalsFields.delivery = $j($shipping).find('.tekserverental-delivery input:checkbox');
	$tekserveRentalsFields.deliveryLoc = $j($shipping).find('.tekserverental-delivery input[name="tekserverentals-delivery-loc"]:radio');
	$tekserveRentalsFields.pickup = $j($shipping).find('.tekserverental-pickup input:checkbox');
	$tekserveRentalsFields.pickupLoc = $j($shipping).find('.tekserverental-pickup input[name="tekserverentals-pickup-loc"]:radio');
	$tekserveRentalsFields.firstName = $j($cust).find('.tekserverental-name span.firstname input');
	$tekserveRentalsFields.lastName = $j($cust).find('.tekserverental-name span.lastname input');
	$tekserveRentalsFields.company = $j($cust).find('div.tekserverental-company-name input');
	$tekserveRentalsFields.email = $j($cust).find('div.tekserverental-email input');
	$tekserveRentalsFields.phone = $j($cust).find('div.tekserverental-phone-number input');
	$tekserveRentalsFields.addressOne = $j($cust).find('div.tekserverental-addressone input');
	$tekserveRentalsFields.addressTwo = $j($cust).find('div.tekserverental-addresstwo input');
	$tekserveRentalsFields.city = $j($cust).find('div.tekserverental-city input');
	$tekserveRentalsFields.state = $j($cust).find('div.tekserverental-state input');
	$tekserveRentalsFields.zip = $j($cust).find('div.tekserverental-zip input');
	$tekserveRentalsFields.additionalInfo = $j('.tekserverental-checkout-form').first().find('.tekserverental-additional-info textarea');

	//init simplecart
	simpleCart({
	
		cartColumns: [
			{ attr: "name" , label: "Name" } ,
			{ attr: "price" , label: "Price", view: 'currency' } ,
			{ attr: "deposit" , label: "Deposit", view: 'currency' },
			{ view: "decrement" , label: false , text: "-" } ,
			{ attr: "quantity" , label: "Qty" } ,
			{ view: "increment" , label: false , text: "+" } ,
			{ view: "remove" , text: "Remove" , label: false }
		],
		checkout: { 
			type: "SendForm", 
			url: tekserveRentalsUrl,
			// http method for form, "POST" or "GET", default is "POST"
			method: "POST",
			// an option list of extra name/value pairs that can be sent along with the checkout data
			extra_data: {
				storename: "Tekserve Rentals",
				start_date: $j($tekserveRentalsFields.startDate).val(),
				end_date: $j($tekserveRentalsFields.endDate).val(),
				deposits_total: $j('.tekserverentals-cart-deposits').first().html(),
				duration: $j('.tekserverentals-cart-duration-value').first().html(),
				delivery: 0,
				delivery_loc: $j($tekserveRentalsFields.deliveryLoc).filter(':checked').val(),
				pickup: 0,
				pickup_loc: $j($tekserveRentalsFields.pickupLoc).filter(':checked').val(),
				first_name: $j($tekserveRentalsFields.firstName).val(),
				last_name: $j($tekserveRentalsFields.lastName).val(),
				company: $j($tekserveRentalsFields.company).val(),
				email: $j($tekserveRentalsFields.email).val(),
				phone: $j($tekserveRentalsFields.phone).val(),
				address_one: $j($tekserveRentalsFields.addressOne).val(),
				address_two: $j($tekserveRentalsFields.addressTwo).val(),
				city: $j($tekserveRentalsFields.city).val(),
				state: $j($tekserveRentalsFields.state).val(),
				zip: $j($tekserveRentalsFields.zip).val(),
				comments: $j($tekserveRentalsFields.additionalInfo).val(),
				total_price: $j('.simpleCart_grandTotal').first().html()
			}
		},
		cartStyle: "table",
		currency: "USD",
		shippingCustom: function(){ 
			 //set up function to calculate costs based on item quantity and location
			 var tekShipCost = updateShipping();
			 return tekShipCost;
		},
		taxRate: 0.08875,
		taxShipping: true
	
	});	//end simpleCart
	
	//datepicker for dates fields
	$j($tekserveRentalsFields.startDate).datepicker();
    $j($tekserveRentalsFields.endDate).datepicker();
    
    //set old value to zero (keeps user from being nagged when entering date for the first time)
    $j($tekserveRentalsFields.endDate).data('old', 0);
	
	//bind change startDate
	$j($tekserveRentalsFields.startDate).change(function() {
	
		var validates;
		if ($j($tekserveRentalsFields.endDate).data('old') == 0) {
		
			validates = dateButton('first');
		
		}
		else {	
		
			validates = dateButton('');
		
		}	//end if ($j($tekserveRentalsFields.endDate).data('old') == 0)
	
		if (validates) {
		
			updateTotals();
		
		}
		$j(this).removeClass('error');

	
	});	//end $j($tekserveRentalsFields.startDate).change(function()

	//bind change endDate
	$j($tekserveRentalsFields.endDate).change(function() {
	
		if (($j(this).data('old') == 0) && !($j(this).val())) {
		
			$j(this).data('old', 'empty');
		
		}
		else {
		
			$j(this).data('old', $j(this).val());
		
		}		
		var validates = dateButton('');
		if(validates) {
			updateTotals();
		}
		$j(this).removeClass('error');
	
	});	//end $j($tekserveRentalsFields.endDate).change(function()

	//bind changes to shipping info
	$j($shipping).find('input').change(function() {
	
		simpleCart.update();
		updateTotals();
	
	});	//end $j($shipping).find('input').change(function()
    
    //bind empty cart button(s)
	$j('.emptyCart-button').click(function() {
	
		simpleCart.update();
		simpleCart.empty();
		simpleCart.update();
		updateTotals();
		simpleCart.save()
	
	});	//end $j('.emptyCart-button').click(function()
	
	
	//bind all simplecart actions
	bindAllCartActions();

	
	//Data Validation
	$j.validator.setDefaults({
	  debug: true,
	  success: "valid"
	});
	
	$j(".tekserverentals-checkout-form form").first().validate({
	
		rules: {
			firstname: {
				required: true
			},
			lastname: {
				required: true
			},
			emailaddress: {
				required: true,
				email: true
			},
			phonenumber: {
				required: true,
				phoneUS: true
			},
			addressone: {
				required: true
			},
			city: {
				required: true
			},
			state: {
				required: true,
				stateUS: true
			},
			zip: {
				required: true,
				zipcodeUS: true
			}
		}
	
	});	//end
	
	$j(".tekserverentals-dates-form form").first().validate({
	
		rules: {
			startdate: {
			  required: true,
			  date: true
			},
			enddate: {
			  required: true,
			  date: true
			}
		}

	});	//end $j(".tekserverentals-dates-form form").first().validate

});	//end jQuery( document ).ready(function($j)



//after page has loaded
jQuery(window).bind('load', function() {

	//add anchors
	if (jQuery('.tekserverental-dates').length > 0) {
	
		jQuery('.tekserverental-dates').first().before(tekserveRentalsAnchors.dates);
	
	}	//end if (jQuery('.tekserverental-dates').length > 0)
	if (jQuery('.tekserverental-checkout-form').length > 0) {
	
		jQuery('.tekserverental-checkout-form').first().before(tekserveRentalsAnchors.checkout);
	
	}	//end if (jQuery('.tekserverental-checkout-form').length > 0)
	if (jQuery('.tekserverental-checkout-form .tekserverental-checkout-submit').length > 0) {
	
		jQuery('.tekserverental-checkout-form .tekserverental-checkout-submit').first().before(tekserveRentalsAnchors.submit);
	
	}	//end if (jQuery('.tekserverental-checkout-form .tekserverental-submit').length > 0)
	if (jQuery('.tekserverentals-cart').length > 0) {
	
		jQuery('.tekserverentals-cart-container').first().prepend(tekserveRentalsAnchors.cart);
	
	}	//end if (jQuery('.tekserverentals-cart-container').length > 0)
	
	//create and bind go to cart buttons
    createCartButtons();

});	//end jQuery(window).bind('load', function()



//date validation function
function isValidDate(d) {

	if (Object.prototype.toString.call(d) !== "[object Date]") {
	
		return false;
	
	}	//end if (Object.prototype.toString.call(d) !== "[object Date]")
	return !isNaN(d.getTime());

}	//end isValidDate(d)



//function to compare two dates and either return that date range is invalid & why, or return the number of days, weeks, extra days, and extra weeks
function tekserveRentalsCompareDates(startDate,endDate) {

	var oneDay=1000*60*60*24;
	var date1ms = startDate.getTime();
  	var date2ms = endDate.getTime();
  	var differencems = date2ms - date1ms;
  	var differenceDays = Math.round(differencems/oneDay);
  	var differenceEdays = 0;
  	var differenceWeeks = 0;
  	var differenceEweeks = 0;
  	if (differenceDays > 7) {
  	
  		if (differenceDays > 13) {
  		
  			differenceWeeks = 1;
  			differenceEweeks = Math.floor((differenceDays-7)/7);
  			differenceEdays = differenceDays - ((differenceEweeks+1)*7);
  		
  		}
  		else {
  		
  			differenceWeeks = 1;
  			differenceEdays = differenceDays - 7;
  		
  		}	//end if (differenceDays > 13)
  	
  	}
  	else if (differenceDays > 1) {
  	
  		differenceEdays = differenceDays - 1;
  	
  	}	//end if (differenceDays > 7)
  	var differences = new Array();
  	differences[0] = differenceDays;
  	differences[1] = differenceEdays;
  	differences[2] = differenceWeeks;
  	differences[3] = differenceEweeks;
  	return differences;

}	//end tekserveRentalsCompareDates(startDate,endDate)



//function to add/update dates for each item
function tekserveRentalsUpDates(startDate,endDate) {

	var dPrice;
	var edPrice;
	var wPrice;
	var ewPrice;
	var deposit;
	var qty;
	var priceArray;
	var depositTotal = parseInt(0);
	var gTotal;
	var days;
	var chargedDays;
	var chargedWeeks;
	var termFlag;
	simpleCart.each(function(item,x) {
	
		//assign dates to each item in the cart
		item.set('start',startDate);
		item.set('end',endDate);
		dPrice = item.get('dprice');
		edPrice = item.get('edprice');
		wPrice = item.get('wprice');
		ewPrice = item.get('ewprice');
		deposit = item.get('deposit');
		qty = item.get('quantity');
		//calculate price for this item for the duration
		priceArray = tekserveRentalsItemPrice(startDate,endDate,dPrice,edPrice,wPrice,ewPrice);
		//set price
		item.set('price',priceArray[0]);
		//set deposit
		deposit = parseInt(deposit) * parseInt(qty);
		depositTotal += deposit;
		//set grand total with deposit
		gTotal = simpleCart.grandTotal() + depositTotal;
		//display calculated rental information for order
		days = priceArray[1];
		termFlag = priceArray[4];
		jQuery('.tekserverentals-cart-duration-value').html(Math.abs(days));
		jQuery('.tekserverentals-cart-deposits').html('$'+depositTotal);
		jQuery('.tekserverentals-cart-grand-total').html('$'+gTotal.toFixed(2));
		if (termFlag == 'longTerm') {
		
			jQuery('.tekserverentals-cart-message p.duration').text(tekserveRentalsMessages.duration);
			jQuery('.tekserverentals-cart-message').show();
		
		}
		else {
		
			jQuery('.tekserverentals-cart-message p.duration').text('');
			if (!(jQuery('.tekserverentals-cart-message p.shipping').text())) {
			
				jQuery('.tekserverentals-cart-message').hide();
			
			}	//end if (!(jQuery('.tekserverentals-cart-message p.shipping').text()))
		
		}	//end if (termFlag == 'longTerm')
	
	});	//end simpleCart.each(function(item,x)
	simpleCart.update();
	simpleCart.save()

}	//end tekserveRentalsUpDates(startDate,endDate)



//function to calculate the best price for the cust based on pricing for no of days, edays, weeks, and eweeks rental is set for
function tekserveRentalsItemPrice(startDate,endDate,dPrice,edPrice,wPrice,ewPrice) {

	var dayArray = tekserveRentalsCompareDates(startDate,endDate);
	var termFlag = 'shortTerm';
	var totalPrice = dPrice;
	var chargedDays = 1;
	var chargedWeeks = 0;
	var days = dayArray[0];
	var eDays = dayArray[1];
	var weeks = dayArray[2];
	var eWeeks = dayArray[3];
	var dTot;
	var wTot;
	if (weeks > 0) {
	
		if (eWeeks > 0) {
		
			if (eDays > 0) {
			
				dTot = parseInt(wPrice) + (edPrice * eDays) + (ewPrice * eWeeks);
				wTot = parseInt(wPrice) + parseInt(ewPrice) + (ewPrice * eWeeks);
				if (wTot < dTot) {
				
					totalPrice = wTot;
					chargedDays = 0;
					chargedWeeks = parseInt(eWeeks) + 1;
				
				}
				else {
				
					totalPrice = dTot;
					chargedDays = eDays;
					chargedWeeks = parseInt(eWeeks) + 1;
				
				}	//end if (wTot < dTot)
			
			}
			else {
			
				totalPrice = parseInt(wPrice) + (ewPrice * eWeeks);
				chargedDays = 0;
				chargedWeeks = parseInt(eWeeks) + 1;
			
			}	//end if (eDays > 0)
		
		}
		else if (eDays > 0) {
		
			dTot = parseInt(wPrice) + (edPrice * eDays) + (ewPrice * eWeeks);
			wTot = parseInt(wPrice) + parseInt(ewPrice) + (ewPrice * eWeeks);
			if (wTot < dTot) {
			
				totalPrice = wTot;
				chargedDays = 0;
				chargedWeeks = parseInt(eWeeks) + 1;
			
			}
			else {
			
				totalPrice = dTot;
				chargedDays = eDays;
				chargedWeeks = parseInt(eWeeks) + 1;
			
			}	//end if (wTot < dTot)
		
		}
		else {
		
			totalPrice = wPrice;
			chargedDays = 0;
			chargedWeeks = 1;
		
		}	//end if (eWeeks > 0)
	
	}
	else if (eDays > 0) {
	
		dTot = parseInt(dPrice) + (edPrice * eDays);
		if (wPrice < dTot) {
		
			totalPrice = wPrice;
			chargedDays = 0;
			chargedWeeks = 1;
		
		}
		else {
		
			totalPrice = dTot;
			chargedDays = days;
		
		}	//end if (wPrice < dTot)
	
	}	//end if (weeks > 0)
	if (eWeeks > 2) {
	
		termFlag = 'longTerm';
	
	}	//end if (eWeeks > 2)
	var finalPrice = new Array();
	finalPrice[0] = totalPrice;
	finalPrice[1] = days;
	finalPrice[2] = chargedDays;
	finalPrice[3] = chargedWeeks;
	finalPrice[4] = termFlag;
	return finalPrice;

}	//end tekserveRentalsItemPrice(startDate,endDate,dPrice,edPrice,wPrice,ewPrice)



//function for updating on date change
function dateButton(isFirst) {

	var startDate = new Date(jQuery($tekserveRentalsFields.startDate).val());
	var endDate = new Date(jQuery($tekserveRentalsFields.endDate).val());
	var today = new Date();
	if (isValidDate(startDate) && isValidDate(endDate)) {
	
		if ((startDate < today) || (endDate < today)) {
		
			alert("Time machine is for data backup, not actual time travel. Please choose dates in the future.");
			toggleCart('hide');
			return false;
		
		}
		else if (endDate < startDate) {
		
			tekserveRentalsUpDates(endDate,startDate);
			toggleCart('show');
			return true;
		
		}
		else {
		
			tekserveRentalsUpDates(startDate,endDate);
			toggleCart('show');
			return true;
		
		}	//end if ((startDate < today) || (endDate < today))
	
	}
	else {
	
		if (isFirst !== 'first') {
		
			alert("Please enter both a start and end date for this rental.");
		
		}	//end if (isFirst !== 'first')
		toggleCart('hide');
		return false;
	
	}	//end if (isValidDate(startDate) && isValidDate(endDate))

}	//end dateButton(isFirst)



//simple function to update all totals. Called by updates.
function updateTotals() {

	var startDate = new Date(jQuery($tekserveRentalsFields.startDate).val());
	var endDate = new Date(jQuery($tekserveRentalsFields.endDate).val());
	if (isValidDate(startDate) && isValidDate(endDate)) {
	
		tekserveRentalsUpDates(startDate,endDate);
	
	}	//end if (isValidDate(startDate) && isValidDate(endDate))

}	//end updateTotals()



//function to update shipping based on quantity and locations
function updateShipping() {

	var delivery = 0;
	var pickup = 0;
	var deliveryLoc;
	var pickupLoc;
	var deliveryCost;
	var pickupCost;
	var shippingTotal;
	//has cust selected pickup or delivery?
	if (jQuery($tekserveRentalsFields.delivery).prop('checked')) {
	
		delivery = 1;
		deliveryLoc = jQuery($tekserveRentalsFields.deliveryLoc).filter(':checked').val();
	
	}	//end if (jQuery($tekserveRentalsFields.delivery).prop('checked'))
	if (jQuery($tekserveRentalsFields.pickup).prop('checked')) {
	
		pickup = 1;
		pickupLoc = jQuery($tekserveRentalsFields.pickupLoc).filter(':checked').val();
	
	}	//end if (jQuery($tekserveRentalsFields.pickup).prop('checked'))
	if ( delivery || pickup ) {
	
		var howManyUnits = 0;
		//exclude accessories
		simpleCart.each(function(item, x) {
		
			if (((item.get('name')).indexOf('Mac') > -1) || ((item.get('name')).indexOf('iPad') > -1)) {
			
				howManyUnits += parseInt(item.get('quantity'));
			
			}	//end if (((item.get('name')).indexOf('Mac') > -1) || ((item.get('name')).indexOf('iPad') > -1))
		
		});	//end simpleCart.each(function(item, x)
	
	}
	//no delivery, set ship to 0 and exit
	else {
	
		jQuery('.tekserverentals-cart-message p.shipping').text('');
		if (!(jQuery('.tekserverentals-cart-message p.duration').text())) {
			
				jQuery('.tekserverentals-cart-message').hide();
			
			}	//end if (!(jQuery('.tekserverentals-cart-message p.duration').text()))
		return 0;
	
	}	//end if ( delivery || pickup )
	//delivery cost
	if (delivery) {
	
		if ((deliveryLoc == 'manhattan') && (howManyUnits < 5)) {
		
			deliveryCost = tekserveRentalsShipcost.mansm;
		
		}
		else if ((deliveryLoc == 'borough') && (howManyUnits < 5)) {
		
			deliveryCost = tekserveRentalsShipcost.borsm;
		
		}
		else if ((deliveryLoc == 'borough') && (howManyUnits > 4) && (howManyUnits < 11)) {
		
			deliveryCost = tekserveRentalsShipcost.borlg;
		
		}
		else if ((deliveryLoc == 'manhattan') && (howManyUnits > 4) && (howManyUnits < 11)) {
		
			deliveryCost = tekserveRentalsShipcost.manlg;
		
		}
		else {
		
			deliveryCost = tekserveRentalsShipcost.error;
		
		}	//end if ((deliveryLoc == 'manhattan') && (howManyUnits < 5))
	
	}
	else {
	
		deliveryCost = 0;
	
	}	//end if (delivery)
	//pickup cost
	if (pickup) {
	
		if ((pickupLoc == 'manhattan') && (howManyUnits < 5)) {
		
			pickupCost = tekserveRentalsShipcost.mansm;
		
		}
		else if ((pickupLoc == 'borough') && (howManyUnits < 5)) {
		
			pickupCost = tekserveRentalsShipcost.borsm;
		
		}
		else if ((pickupLoc == 'borough') && (howManyUnits > 4) && (howManyUnits < 11)) {
		
			pickupCost = tekserveRentalsShipcost.borlg;
		
		}
		else if ((pickupLoc == 'manhattan') && (howManyUnits > 4) && (howManyUnits < 11)) {
		
			pickupCost = tekserveRentalsShipcost.manlg;
		
		}
		else {
		
			pickupCost = tekserveRentalsShipcost.error;
		
		}	//end if ((pickupLoc == 'manhattan') && (howManyUnits < 5))
	
	}
	else {
	
		pickupCost = 0;
	
	}	//end if (pickup)
	shippingTotal = parseInt(deliveryCost) + parseInt(pickupCost);
	//orders with more than 10 big items will not get an estimate
	if (shippingTotal > 499) {
	
		
		jQuery('.tekserverentals-cart-message p.shipping').text(tekserveRentalsMessages.shipping);
		jQuery('.tekserverentals-cart-message').show();
		return 0;
	
	}
	//write shipping cost to cart
	else {
	
		jQuery('.tekserverentals-cart-message p.shipping').text('');
		if (!(jQuery('.tekserverentals-cart-message p.duration').text())) {
		
			jQuery('.tekserverentals-cart-message').hide();
		
		}	//end if (!(jQuery('.tekserverentals-cart-message p.duration').text()))
		return shippingTotal;
	
	}	//end if (shippingTotal > 499)

}	//end updateShipping()



//function to update extra data
function updateExtraData(data) {

	// console.log(data);
	var delivery = 0;
	var pickup = 0;
	if (jQuery($tekserveRentalsFields.delivery).prop('checked')) {
	
		delivery = 1;
	
	}	//end if (jQuery($tekserveRentalsFields.delivery).prop('checked'))
	if (jQuery($tekserveRentalsFields.pickup).prop('checked')) {
	
		pickup = 1;
	
	}	//end if (jQuery($tekserveRentalsFields.pickup).prop('checked'))
	data.start_date = jQuery($tekserveRentalsFields.startDate).val();
	data.end_date = jQuery($tekserveRentalsFields.endDate).val();
	data.deposits_total = jQuery('.tekserverentals-cart-deposits').html();
	data.duration = jQuery('.tekserverentals-cart-duration-value').html();
	data.delivery = delivery;
	data.delivery_loc = jQuery($tekserveRentalsFields.deliveryLoc).filter(':checked').val();
	data.pickup = pickup;
	data.pickup_loc = jQuery($tekserveRentalsFields.pickupLoc).filter(':checked').val();
	data.first_name = jQuery($tekserveRentalsFields.firstName).val();
	data.last_name = jQuery($tekserveRentalsFields.lastName).val();
	data.company = jQuery($tekserveRentalsFields.company).val();
	data.email = jQuery($tekserveRentalsFields.email).val();
	data.phone = jQuery($tekserveRentalsFields.phone).val();
	data.address_one = jQuery($tekserveRentalsFields.addressOne).val();
	data.address_two = jQuery($tekserveRentalsFields.addressTwo).val();
	data.city = jQuery($tekserveRentalsFields.city).val();
	data.state = jQuery($tekserveRentalsFields.state).val();
	data.zip = jQuery($tekserveRentalsFields.zip).val();
	data.comments = jQuery($tekserveRentalsFields.additionalInfo).val();
	data.total_price = jQuery('.simpleCart_grandTotal').html();

}	//end updateExtraData(data)



//simpleCart Binding Function
function bindAllCartActions() {

	//bind after add date calculation
	simpleCart.bind("afterAdd", function(item) {

		if(!(jQuery('.tekserverentals-cart-container').is(':visible'))) {
	
			alert( item.get("name") + " has been added to the cart!");
	 
		 }	//end if(!(jQuery('.tekserverentals-cart-container').is(':visible')))
		updateTotals();

	});	//end simpleCart.bind("afterAdd", function(item)



	//bind update to increment click
	simpleCart.bind("afterIncrement", function(item) {

		updateTotals();

	});	//end simpleCart.bind("afterIncrement", function(item)



	//bind update to decrement click
	simpleCart.bind("afterDecrement", function(item) {

		updateTotals();

	});	//end simpleCart.bind("afterDecrement", function(item)



	//bind update to decrement click
	simpleCart.bind("afterRemove", function(item) {
	
		updateTotals();
	
	});	//end simpleCart.bind( "afterRemove", function(item)



	//bind saving data to checkout
	simpleCart.bind('beforeCheckout', function(data) {
	
		updateExtraData(data);
		var formReady = true;
		var valErrors = new Array();
		var i = 0;
	
		jQuery('.tekserverental-dates-form').first().find('input.required').each(function() {
		
			if (jQuery(this).val()=='' || jQuery(this).val()==null || jQuery(this).hasClass('error')) {
			
				formReady = false;
				jQuery(this).addClass('error');
				valErrors[i] = jQuery(this).attr('id');
				i++;
			
			}	//end if (jQuery(this).val()=='' || jQuery(this).val()==null || jQuery(this).hasClass('error'))
		
		});	//end jQuery('#tekserverentals-checkout-dates').first().find('input.required').each(function()
	
		jQuery('.tekserverental-checkout-form form').first().find('input.required').each(function() {
		
			if (jQuery(this).val()=='' || jQuery(this).val()==null || jQuery(this).hasClass('error')) {
			
				formReady = false;
				jQuery(this).addClass('error');
				valErrors[i] = jQuery(this).attr('id');
				i++;
			
			}	//end if (jQuery(this).val()=='' || jQuery(this).val()==null || jQuery(this).hasClass('error'))
		
		});	//end jQuery('.tekserverentals-checkout-form form').first().find('input.required').each(function()

		if (!formReady) {
		
			var errorHtml = '<label class="error" style="font-size: 2em;">Please Fill Out These Fields:</label><br/><ul class="errorlist">';
			var errortext;
			for (i = 0; i < valErrors.length; i++) {
			
				errortext = jQuery('label[for="' + valErrors[i] + '"]').html();
				errorHtml += '<li><a href="#' + valErrors[i] + '">' + errortext + '</a></li>';
			
			}	//end for (i = 0; i < valErrors.length; i++)
			errorHtml += '</ul>';
			jQuery('.tekserverental-checkout-form').first().find('.validationerrors').html(errorHtml);
			scrollToID('tekserve-rentals-submit');
			if (typeof(bindAnchors) === "function") {
				bindAnchors();
			}
			return false;
		
		}	//end if (!formReady)
	
	});	//end simpleCart.bind('beforeCheckout', function(data)

}	//end bindAllCartActions()



//function to create cart buttons
function createCartButtons() {

	//all .tekserverental-show-cart-button-container (display:none by default) get a button and bind it
	jQuery('.tekserverentals-show-cart-button-container').each( function() {

		var target = areYouMyMother(this);
		jQuery(this).append('<a class="cartbutton button" href="#!">Show Cart</a>');
		jQuery(this).children('a').click( function(e) {
		
			e.preventDefault();
			moveCartHere( target.parent );
// 			setTimeout(function() {
				scrollToID('tekserve-rentals-cart');
// 			}, 150);
		
		});	//end jQuery(this).children('a').click( function(e)
	
	});	//end jQuery('.tekserverental-show-cart-button-container').each( function()

}	//end createCartButtons()



//toggle visibility of cart and cart buttons
function toggleCart(action) {

	if (action === 'show') {
	
		jQuery('.tekserverentals-cart-container').slideDown();
		jQuery('.tekserverentals-show-cart-button-container').fadeIn();
	
	}
	else if (action === 'hide') {
	
		jQuery('.tekserverentals-cart-container').slideUp();
		jQuery('.tekserverentals-show-cart-button-container').fadeOut();
	
	}
	else {
		if (jQuery('.tekserverentals-cart-container').filter(':visible').length > 0 ) {
	
			toggleCart('hide');
	
		}
		else {
	
			toggleCart('show');
	
		}	//end if (jQuery('.tekserverentals-cart-container').filter(':visible').length > 0 )
	
	}	//end if (action==='show')

}	//end toggleCart(action)



//function to move cart to where user has made a change
function moveCartHere($cartTarget) {

	//var $cartContainer from .tekserverentals-cart-container
	var cartContainer = areYouMyMother(jQuery('.tekserverentals-cart-container').first());
	
	//look for correct wrapper for cart: end up with it removed from DOM and stored in $wrappedCart
	if (cartContainer.isVC && cartContainer.hasSiblings) {
	
		var spacer = '<div class="wpb_wrapper"><div class="vc_empty_space" style="height: 2em"><span class="vc_empty_space_inner"></span></div></div>';
		jQuery(cartContainer.parent).after(spacer);
		if (jQuery(cartContainer.parent).closest('.vc_row .innerRowWrap').length > 0 ) {
		
			jQuery(cartContainer.parent).wrap('<div class="innerRowWrap"></div>');
			jQuery(cartContainer.parent).closest('.innerRowWrap').wrap('<div class="vc_row wpb_row vc_row-fluid"></div>');
		}
		else {
		
			jQuery(cartContainer.parent).wrap('<div class="vc_row wpb_row vc_row-fluid"></div>');
		
		}	//end if (jQuery(cartContainer.parent).closest('.vc_row .innerRowWrap').length > 0 )
		cartContainer.parent = jQuery(cartContainer.parent).closest('.vc_row');
	
	}	//end if (cartContainer.isVC && cartContainer.hasSiblings)
	var $wrappedCart = jQuery(cartContainer.parent).detach();
	//var $wrappedCart is now ready and detached; hide it;
	jQuery($wrappedCart).hide();
	//move $wrappedCart to end of $cartTarget
	jQuery($cartTarget).after($wrappedCart);
	//show the relocated cart to user
	jQuery($wrappedCart).slideDown();

}	//end moveCartHere( objectAbove )



//function to check correct wrapper
//return object with .parent($object), .hasSiblings(bool), and isVC(bool)
function areYouMyMother($babyBird) {

	var snort = {
		parent: '',
		isVC: false,
		hasSiblings: false,
	};
	//if has parent .vc_column_container, keep checking
	snort.parent = jQuery($babyBird).closest('.vc_row .vc_column_container');
	//if not, it's flying solo
	if (snort.parent.length < 1) {
	
		snort.parent = $babyBird;
		return snort;
	
	}	//end if (snort.parent.length < 1)
	snort.isVC = true;
	//check for siblings; if it has them, return the column, not row
	if (jQuery(snort.parent).siblings().length > 0) {
	
		snort.hasSiblings = true;
		return snort;
	
	}	//end if (jQuery($babyBird).siblings > 0)
	//otherwise, return the row
	snort.parent = jQuery($babyBird).closest('.vc_row');
	return snort;

}	//end areYouMyMother($babyBird)