//takes plugin folder location from php upon page init
var tekserveRentalsUrl = tekserveRentalsData.tekserveRentalsUrl + "request.php";

//init commands for simplecart
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
        type: "SendForm" , 
        url: tekserveRentalsUrl ,

        // http method for form, "POST" or "GET", default is "POST"
        method: "POST" , 

        // url to return to on successful checkout, default is null
//         success: "success.html" , 

        // url to return to on cancelled checkout, default is null
//         cancel: "cancel.html" ,

        // an option list of extra name/value pairs that can
        // be sent along with the checkout data
        extra_data: {
          storename: "Tekserve Rentals"
        }
    },
    cartStyle: "div",
    currency: "USD",
    shippingCustom: function(){ 
         //set up function to calculate costs based on item quantity and location
         return 0;
    },
    taxRate: .08875,
    taxShipping: true
});

//output the checkout box
jQuery('.tekserverental-checkout').after('<div class="tekserverentals-cart-container" style="display: none;"><div class="tekserverentals-cart-showbutton button" style="display: none;">Show Cart</div><div class="tekserverentals-cart" style="display:none;"><div class="tekserverentals-cart-long-term" style="display: none;"><h3>Important Note</h3>This quote is for a long term rental. The actual price of this rental will be lower than the estimate below. Please contact us directly at 212.929.3645 or rentals@tekserve.com to get the lowest price for this rental.</div><span style="font-weight: 900">Rental Duration: </span><div class="tekserverentals-cart-duration"></div><span> days.</span><div class="simpleCart_items"></div><hr style="height:1px; color: #004f72; background: #004d72;"><div class="tekserverentals-cart-totals"><div><span>Subtotal</span><div class="simpleCart_total"></div></div><div><span>Shipping</span><div class="simpleCart_shipping"></div></div><div><span>Tax</span><div class="simpleCart_tax"></div></div><div><span>Total Deposit</span><div class="tekserverentals-cart-deposits"></div></div><div><span style="font-weight: 900;">Total</span><div class="tekserverentals-cart-grand-total" style="font-weight: 900;"></div></div></div><div class="tekserverentals-cart-hidebutton button" style="">Hide Cart</div></div></div>');

//bind show/click buttons for cart
jQuery('.tekserverentals-cart-showbutton').click(function() {
	jQuery('.tekserverentals-cart').show();
	jQuery('.tekserverentals-cart-showbutton').hide();
	jQuery('.tekserverentals-cart-container').css('background', '#fff').css('box-shadow','1px 1px 10px #334');
});

jQuery('.tekserverentals-cart-hidebutton').click(function() {
	jQuery('.tekserverentals-cart').hide();
	jQuery('.tekserverentals-cart-showbutton').show();
	jQuery('.tekserverentals-cart-container').css('background', 'rgba(255,255,255,0.4)').css('box-shadow','1px 1px 10px #fff');
});

//date validation function
function isValidDate(d) {
  if ( Object.prototype.toString.call(d) !== "[object Date]" )
    return false;
  return !isNaN(d.getTime());
}

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
  		}
  	}
  	else if (differenceDays > 1) {
  		differenceEdays = differenceDays - 1;
  	}
  	var differences = new Array();
  	differences[0] = differenceDays;
  	differences[1] = differenceEdays;
  	differences[2] = differenceWeeks;
  	differences[3] = differenceEweeks;
  	return differences;
}

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
	simpleCart.each(function( item , x ){
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
		jQuery('.tekserverentals-cart-duration').html(days);
		jQuery('.tekserverentals-cart-deposits').html('$'+depositTotal);
		jQuery('.tekserverentals-cart-grand-total').html('$'+gTotal.toFixed(2));
		if (termFlag == 'longTerm') {
			jQuery('.tekserverentals-cart-long-term').show();
		}
	});
	simpleCart.update();
}

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
				}
			}
			else {
				totalPrice = parseInt(wPrice) + (ewPrice * eWeeks);
				chargedDays = 0;
				chargedWeeks = parseInt(eWeeks) + 1;
			}
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
				}
		}
		else {
			totalPrice = wPrice;
			chargedDays = 0;
			chargedWeeks = 1;
		}
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
		}
	}
	if (eWeeks > 2) {
		termFlag = 'longTerm';
	}
	var finalPrice = new Array();
	finalPrice[0] = totalPrice;
	finalPrice[1] = days;
	finalPrice[2] = chargedDays;
	finalPrice[3] = chargedWeeks;
	finalPrice[4] = termFlag;
	return finalPrice;
}

//bind function for updating date to button
jQuery('.tekserverentals-dates-button').click(function() {
	var startDate = new Date(jQuery('#tekserverental-start-date').val());
	var endDate = new Date(jQuery('#tekserverental-end-date').val());
	var today = new Date();
	if (isValidDate(startDate) && isValidDate(endDate)) {
		if ((startDate < today) || (endDate < today)) {
			alert("Time machine is for data backup, not actual time travel. Please choose dates in the future.");
			jQuery('.tekserverentals-cart').hide();
		}
		else if (endDate < startDate) {
			tekserveRentalsUpDates(endDate,startDate);
			jQuery('.tekserverentals-cart').show();
			jQuery('.tekserverentals-cart-container').show();
		}
		else {
			tekserveRentalsUpDates(startDate,endDate);
			jQuery('.tekserverentals-cart').show();
			jQuery('.tekserverentals-cart-container').show();
		}
	}
	else {
		alert("Please enter both a start and end date for this rental.");
		jQuery('.tekserverentals-cart').hide();
	}
});
//simple function to update all totals. Called by updates.
function updateTotals() {
	var startDate = new Date(jQuery('#tekserverental-start-date').val());
	var endDate = new Date(jQuery('#tekserverental-end-date').val());
	if (isValidDate(startDate) && isValidDate(endDate)) {
		tekserveRentalsUpDates(startDate,endDate);
	}
}

//bind after add date calculation
simpleCart.bind( "afterAdd" , function( item ){
	updateTotals();
});

//bind update to increment click
simpleCart.bind( "afterIncrement" , function( item ){
	updateTotals();
});

//bind update to decrement click
simpleCart.bind( "afterDecrement" , function( item ){
	updateTotals();
});

//bind update to decrement click
simpleCart.bind( "afterRemove" , function( item ){
	updateTotals();
});