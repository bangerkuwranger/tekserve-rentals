<?php 
/* Form handler for checkout data. Accepts postdata from simplecart.js and translates to an array that creates a Rental Request entry in wpdb
*/
$content = $_POST;
var_dump($content);
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
	"notes_from_cust" 		=> rtrim($content["comments"], 'Â')
);
$lineitems = array();
$line_item_qty = intval($content["itemCount"]);
for ($i=1; $i<=$line_item_qty; $i++) {
	$lineitems[$i] = array (
		"name" => $content["item_name_".$i],
		"qty" => $content["item_quantity_".$i],
		"price" => $content["item_price_".$i],
	);
}
$orderinfo["start"] = strtotime($orderinfo["start"]);
$orderinfo["end"] = strtotime($orderinfo["end"]);
echo "<br/><br/><h1>Customer Info</h1>";
echo "<div class='tekserverentals-customer-info'>";
echo "<p><b>Name:</b> ".$custinfo["first_name"]." ".$custinfo["last_name"]."</p>";
if ($custinfo["company"]) {
	echo "<p><b>Company:</b> ".$custinfo["company"]."</p>";
}
echo "<p><b>Email:</b> ".$custinfo["email"]."</p>";
echo "<p><b>Phone:</b> ".$custinfo["phone"]."</p>";
echo "<p><b>Address:</b><br/>".$custinfo["address"]."<br/>".$custinfo["city"].", ".$custinfo["state"]." ".$custinfo["zip"]."</p>";
echo "</div>";
echo "<br/><br/><h1>Order Info</h1>";
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
echo "<p><b>Total Cost:</b> ".$orderinfo["total"]."</p>";
echo "<p><b>Amount Due for Reservation:</b> (includes deposit) ".$orderinfo["total_with_deposit"]."</p>";
echo "<p><b>Additional Notes:</b> ".$orderinfo["notes_from_cust"]."</p>";
echo "</div>";
echo "<br/><br/><h1>Line Items</h1>";
echo "<div class='tekserverentals-order-info'><table>";
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