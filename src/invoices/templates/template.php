<?php
#table
#define("BROWSE","browse");
include('./include/include_print.php');
include("./include/functions.php");

$template = "default";

#get the invoice id
$master_invoice_id = $_GET['submit'];

#Info from DB print
$conn = mysql_connect( $db_host, $db_user, $db_password );
mysql_select_db( $db_name, $conn );


#master invoice id select
$print_master_invoice_id = "SELECT * FROM si_invoices WHERE inv_id =$master_invoice_id";
$result_print_master_invoice_id  = mysql_query($print_master_invoice_id , $conn) or die(mysql_error());

$invoice = mysql_fetch_array($result_print_master_invoice_id);
$invoice['date'] = date( $config['date_format'], strtotime( $invoice['inv_date'] ));


#invoice_type query

$sql_invoice_type = "SELECT inv_ty_description FROM si_invoice_type WHERE inv_ty_id = $invoice[inv_type] ";
$result_invoice_type = mysql_query($sql_invoice_type, $conn) or die(mysql_error());


#Seems to be unused...
/*$invoice_type =  mysql_fetch_array($result_invoice_type);
		while ($invoice_typeArray = mysql_fetch_array($result_invoice_type)) {
				$inv_ty_descriptionField = $invoice_typeArray['inv_ty_description'];
	};*/

#customer query
$print_customer = "SELECT * FROM si_customers WHERE c_id = $invoice[inv_customer_id]";
$result_print_customer = mysql_query($print_customer, $conn) or die(mysql_error());

#biller query
$print_biller = "SELECT * FROM si_biller WHERE b_id = $invoice[inv_biller_id]";
$result_print_biller = mysql_query($print_biller, $conn) or die(mysql_error());

$customer = mysql_fetch_array($result_print_customer);
$biller = mysql_fetch_array($result_print_biller);


#preferences query
$print_preferences = "SELECT * FROM si_preferences where pref_id = $invoice[inv_preference] ";
$result_print_preferences  = mysql_query($print_preferences, $conn) or die(mysql_error());

$pref = mysql_fetch_array($result_print_preferences);

#Accounts - for the invoice - start
#invoice total calc - start
$invoice['total'] = calc_invoice_total($invoice['inv_id']);
$invoice['total_format'] = number_format($invoice['total'],2);
#invoice total calc - end


#amount paid calc - start
$invoice['paid'] = calc_invoice_paid($invoice['inv_id']);
$invoice['paid_format'] = number_format($invoice['paid'],2);
#amount paid calc - end

#amount owing calc - start
$invoice['owing'] = number_format($invoice['total'] - $invoice['paid'],2);
#amount owing calc - end
#Accounts - for the invoice - end


for($i=1;$i<=4;$i++) {
	$biller["custom_field_label$i"] = get_custom_field_label("biller_cf$i");
	$customer["custom_field_label$i"] = get_custom_field_label("customer_cf$i");
	$product_cf["custom_field_label$i"] = get_custom_field_label("product_cf$i");
	$show["custom_field$i"] = show_custom_field("invoice_cf$i",$invoice["invoice_custom_field$i"],"read",'','tbl1-left','tbl1-right',3,':');
}


#logo field support - if not logo show nothing else show logo

if(!empty($biller['b_co_logo'])) {
	$logo = "./images/logo/$biller[b_co_logo]";
}
else {
	$logo = "./images/logo/_default_blank_logo.png";
}
#end logo section





#PRINT DETAILS FOR THE TOTAL STYLE INVOICE

	#get all the details for the total style
	#items invoice id select
	$print_master_invoice_items = "SELECT * FROM si_invoice_items WHERE  inv_it_invoice_id =$master_invoice_id";
	$result_print_master_invoice_items = mysql_query($print_master_invoice_items, $conn) or die(mysql_error());


	#invoice_total Style
	$master_invoice = mysql_fetch_array($result_print_master_invoice_items);
	$master_invoice['inv_it_tax_amount'] = number_format($master_invoice['inv_it_tax_amount'],2);
	$master_invoice['inv_it_gross_total'] = number_format($master_invoice['inv_it_gross_total'],2);
	$master_invoice['inv_it_total'] = number_format($master_invoice['inv_it_total'],2);
	#invoice_total Style

	#all the details have bee got now print them to screen
	
	
	#invoice total tax
	$print_invoice_total_tax ="SELECT SUM(inv_it_tax_amount) AS total_tax FROM si_invoice_items WHERE inv_it_invoice_id =$master_invoice_id";
	$result_print_invoice_total_tax = mysql_query($print_invoice_total_tax, $conn) or die(mysql_error());
	$tax = mysql_fetch_array($result_print_invoice_total_tax);
	$tax['total_tax'] = number_format($tax['total_tax'],2);	

				
	#invoice_total total query
	$print_invoice_total_total ="SELECT SUM(inv_it_total) AS total FROM si_invoice_items WHERE inv_it_invoice_id =$master_invoice_id";
	$result_print_invoice_total_total = mysql_query($print_invoice_total_total, $conn) or die(mysql_error());

	$invoice_total = mysql_fetch_array($result_print_invoice_total_total);
	$invoice_total['total'] = number_format($invoice_total['total'],2);


/* The Export code - supports any file extensions - excel/word/open office - what reads html */
if (isset($_GET['export'])) {
	$file_extension = $_GET['export'];
	header("Content-type: application/octet-stream");
	/*header("Content-type: application/x-msdownload");*/
	header("Content-Disposition: attachment; filename=$pref[pref_inv_heading]$invoice	[inv_id].$file_extension");
	header("Pragma: no-cache");
	header("Expires: 0");
}
/* End Export code */


	include("./src/invoices/templates/${template}/${template}.tmpl");

	
	
	#if itemised style show the invoice note field - START
	if(!( $_GET['invoice_style'] === 'Itemised' && !empty($invoice['inv_note']) OR 'Consulting' && !empty($invoice['inv_note']))) {
		$notes = "";
	}
	#END - if itemised style show the invoice note field
	
	
	$heading = "";
	#show column heading for itemised style
	if ( $_GET['invoice_style'] === 'Itemised' ) {
		$heading = $itemised_heading;
	}
	#show column heading for consulting style
	if ( $_GET['invoice_style'] === 'Consulting' ) {
		$heading = $consulting_heading;
	}
	#show column heading for total style
	if ( $_GET['invoice_style'] === 'Total' ) {
		$heading = $total_heading;
	}
	

	
	#INVOICE ITEMEISED SECTION
	$lines = "";
		
if ($_GET['invoice_style'] === 'Itemised' || $_GET['invoice_style'] === 'Consulting' )  {
	
	#INVOIVE_ITEMS SECTION
	#items invoice id sgseelect
	$print_master_invoice_items = "SELECT * FROM si_invoice_items WHERE  inv_it_invoice_id =$master_invoice_id";
	$result_print_master_invoice_items = mysql_query($print_master_invoice_items, $conn) or die(mysql_error());


	while ($master_invoice = mysql_fetch_array($result_print_master_invoice_items)) {
	
		$master_invoice['inv_it_quantity_formatted'] = number_format($master_invoice['inv_it_quantity'],2);
		$master_invoice['inv_it_unit_price'] = number_format($master_invoice['inv_it_unit_price'],2);
		$master_invoice['inv_it_tax_amount'] = number_format($master_invoice['inv_it_tax_amount'],2);
		$master_invoice['inv_it_gross_total'] = number_format($master_invoice['inv_it_gross_total'],2);
		$master_invoice['inv_it_total'] = number_format($master_invoice['inv_it_total'],2);
		
		#products query
		$print_products = "SELECT * FROM si_products WHERE prod_id = $master_invoice[inv_it_product_id]";
		$result_print_products = mysql_query($print_products, $conn) or die(mysql_error());
		
		$product = mysql_fetch_array($result_print_products);

		#END INVOICE ITEMS SECTION
	
	
		#calculation for each line item
		$gross_total_itemised = $product['prod_unit_price'] * $master_invoice['inv_it_quantity'] ;
	
		#MERGE ITEMISED AND CONSULTING HERE
		#PRINT the line items
		#show the itemised invoice
		include("./src/invoices/templates/${template}/${template}.tmpl");
		
		if ($_GET['invoice_style'] === 'Itemised' ) {
			$line = addslashes($itemised_line);
			eval('$lines .=  "'.$line.'";');

		}
		#show the consulting invoice
		if ($_GET['invoice_style'] === 'Consulting' ) {
			$line = addslashes($consulting_line);
			eval('$lines .=  "'.$line.'";');
		}
	}
}

#END INVOICE ITEMEISED/CONSULTING SECTION
	
	$css = "./src/invoices/templates/${template}/${template}.css";
	include('./config/config.php');

	$temp = file_get_contents("./src/invoices/templates/${template}/${template}.html");
	$temp = addslashes($temp);


	echo $content;
?>