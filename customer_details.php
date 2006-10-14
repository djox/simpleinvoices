<?php
#table
include('./config/config.php'); 
include("./lang/$language.inc.php");

/* validataion code */
include("./include/validation.php");

jsBegin();
jsFormValidationBegin("frmpost");
jsValidateRequired("c_name","Customer name");
jsFormValidationEnd();
jsEnd();

/* end validataion code */


#get the invoice id
$customer_id = $_GET['submit'];


#Info from DB print
$conn = mysql_connect( $db_host, $db_user, $db_password );
mysql_select_db( $db_name, $conn );

#customer query
$print_customer = 'SELECT * FROM si_customers WHERE c_id = ' . $customer_id;
$result_print_customer = mysql_query($print_customer, $conn) or die(mysql_error());


while ($Array = mysql_fetch_array($result_print_customer) ) {
                $c_idField = $Array['c_id'];
                $c_attentionField = $Array['c_attention'];
                $c_nameField = $Array['c_name'];
                $c_street_addressField = $Array['c_street_address'];
                $c_cityField = $Array['c_city'];
                $c_stateField = $Array['c_state'];
                $c_zip_codeField = $Array['c_zip_code'];
                $c_countryField = $Array['c_country'];
		$c_phoneField = $Array['c_phone'];
		$c_faxField = $Array['c_fax'];
		$c_emailField = $Array['c_email'];
		$c_enabledField = $Array['c_enabled'];

        	if ($c_enabledField == 1) {
	              $wording_for_enabled = $wording_for_enabledField;
	        } else {
	              $wording_for_enabled = $wording_for_disabledField;
	        }

#invoice total calc - start
        $print_invoice_total ="select IF ( isnull( sum(inv_it_total)) ,  '0', sum(inv_it_total)) as total from si_invoice_items, si_invoices where  si_invoices.inv_customer_id  = $c_idField  and si_invoices.inv_id = si_invoice_items.inv_it_invoice_id";
        $result_print_invoice_total = mysql_query($print_invoice_total, $conn) or die(mysql_error());

        while ($Array = mysql_fetch_array($result_print_invoice_total)) {
                $invoice_total_Field = $Array['total'];
#invoice total calc - end

#amount paid calc - start
        $x1 = "select  IF ( isnull( sum(ac_amount)) ,  '0', sum(ac_amount)) as amount from si_account_payments, si_invoices, si_invoice_items where si_account_payments.ac_inv_id = si_invoices.inv_id and si_invoices.inv_customer_id = $c_idField  and si_invoices.inv_id = si_invoice_items.inv_it_id";
        $result_x1 = mysql_query($x1, $conn) or die(mysql_error());
        while ($result_x1Array = mysql_fetch_array($result_x1)) {
                $invoice_paid_Field = $result_x1Array['amount'];
#amount paid calc - end

#amount owing calc - start
        $invoice_owing_Field = $invoice_total_Field - $invoice_paid_Field;
#amount owing calc - end
}
}








};

if ($_GET[action] === 'view') {

$display_block =  "

	<table align=center>
	<tr>
		<td colspan=7 align=center><i>Customer</i></td>
	</tr>	
	<tr>
		<td colspan=7 align=center> </td>
	</tr>	
	<tr>
		<td colspan=4 align=center><i>Customer details</i></td><td width=10%></td><td colspan=2 align=center><i>Summary of accounts</i></td>
	</tr>	
	<tr>
		<td class='details_screen'>Customer ID </td><td>$c_idField</td><td colspan=2></td><td></td><td class='details_screen'>Total Invoices</td><td>$invoice_total_Field </td>
	</tr>
	<tr>
		<td class='details_screen'>Customer name </td><td colspan=2>$c_nameField</td><td colspan=2></td><td class='details_screen'>Total Paid</td><td>$invoice_paid_Field </td>
	</tr>
	<tr>
		<td class='details_screen'>Attn </td><td colspan=2>$c_attentionField</td><td colspan=2></td><td class='details_screen'>Total Owing</td><td><u>$invoice_owing_Field</u> </td>
	</tr>
	<tr>
		<td class='details_screen'>Street address</td><td>$c_street_addressField</td><td class='details_screen'>Phone </td><td>$c_phoneField</td>

	</tr>
	<tr>
		<td class='details_screen'>City</td><td>$c_cityField</td><td class='details_screen'>Fax</td><td>$c_faxField</td>
	</tr>
	<tr>
		<td class='details_screen'> Zip code</td><td>$c_zip_codeField</td><td class='details_screen'>Email</td><td>$c_emailField</td>

	</tr>
	<tr>
		<td class='details_screen'>State</td><td>$c_stateField</td>
	</tr>
	<tr>
		<td class='details_screen'>Country</td><td>$c_countryField</td>
	</tr>
	<tr>
		<td class='details_screen'>$wording_for_enabledField</td><td>$wording_for_enabled</td>
	</tr>	
        </table>

";



#show invoices per client
$sql = "select * from si_invoices where inv_customer_id =$customer_id  ORDER BY inv_id desc";

$display_block .=  "
<br>
Customer invoice listings:";

include('./manage_invoices.inc.php'); 

$footer =  "

<div id='footer'><a href='?submit=$c_idField&action=edit'>Edit</a></div>
";

}

else if ($_GET[action] === 'edit') {
#do the product enabled/disblaed drop down
$display_block_enabled = "<select name=\"c_enabled\">
<option value=\"$c_enabledField\" selected style=\"font-weight: bold\">$wording_for_enabled</option>
<option value=\"1\">$wording_for_enabledField</option>
<option value=\"0\">$wording_for_disabledField</option>
</select>";

$display_block =  "

        <table align=center>
        <tr>
                <td colspan=2 align=center><i>Customer</i></td>
        </tr>
        <tr>
                <td class='details_screen'>Customer ID </td><td>$c_idField</td>
        </tr>
        <tr>
                <td class='details_screen'>Customer name </td><td><input type=text name='c_name' value='$c_nameField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>Attn </td><td><input type=text name='c_attention' value='$c_attentionField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>Street address</td><td><input type=text name='c_street_address' value='$c_street_addressField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>City</td><td><input type=text name='c_city' value='$c_cityField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>Zip code</td><td><input type=text name='c_zip_code' value='$c_zip_codeField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>State</td><td><input type=text name='c_state' value='$c_stateField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>Country</td><td><input type=text name='c_country' value='$c_countryField' size=50></td>
        </tr>
        <tr>
	        <td class='details_screen'>Phone</td><td><input type=text name='c_phone' value='$c_phoneField' size=50></td>
        </tr>
        <tr>
	        <td class='details_screen'>Fax</td><td><input type=text name='c_fax' value='$c_faxField' size=50></td>
        </tr>
        <tr>
                <td class='details_screen'>Email</td><td><input type=text name='c_email' value='$c_emailField' size=50></td
        </tr>
        <tr>
                <td class='details_screen'>$wording_for_enabledField </td><td>$display_block_enabled</td>
        </tr>

        </table>
";

$footer =  "

<p><input type=submit name='action' value='Cancel'>
<input type=submit name='action' value='Save Customer'> <input type=hidden name='op' value='edit_customer'></p>
";


}



?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="include/doFilter.js"></script>

<script type="text/javascript" src="include/jquery.js"></script>
<script type="text/javascript" src="include/tablesorter.js"></script>

<script type="text/javascript">
$(document).ready(function() {
        $("table#large").tableSorter({
                sortClassAsc: 'sortUp', // class name for asc sorting action
                sortClassDesc: 'sortDown', // class name for desc sorting action
                highlightClass: ['highlight'], // class name for sort column highlighting.
                //stripingRowClass: ['even','odd'],
               //alternateRowClass: ['odd','even'],
                headerClass: 'largeHeaders', // class name for headers (th's)
                disableHeader: [0], // disable column can be a string / number or array containing string or number.
                dateFormat: 'dd/mm/yyyy' // set date format for non iso dates default us, in this case override and set uk-format
        })
});
$(document).sortStart(function(){
        $("div#sorting").show();
}).sortStop(function(){
        $("div#sorting").hide();
});
</script>


<?php include('./include/menu.php'); ?>
<script type="text/javascript" src="niftycube.js"></script>
<script type="text/javascript">
window.onload=function(){
Nifty("div#container");
Nifty("div#content,div#nav","same-height small");
Nifty("div#header,div#footer","small");
}
</script>
<title>Simple Invoices - Customer details
</title>
<?php include('./config/config.php'); ?>
</head>
<body>
<?php
$mid->printMenu('hormenu1');
$mid->printFooter();
?>

<link rel="stylesheet" type="text/css" href="themes/<?php echo $theme; ?>/tables.css">
<br>
<form name="frmpost" action="insert_action.php?submit=<?php echo $_GET['submit'];?>" method="post" onsubmit="return frmpost_Validator(this)">


<div id="container">
<div id="header"></div>
<?php echo $display_block; ?>
<div id="footer">
<?php echo $footer; ?>
</div>
</body>
</html>


