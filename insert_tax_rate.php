<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="niftycube.js"></script>
<script type="text/javascript">
window.onload=function(){
Nifty("div#container");
Nifty("div#subheader");
Nifty("div#content,div#nav","same-height small");
Nifty("div#header,div#footer","small");
}
</script>

<title> Simple Invoices - Tax rate to add</title>
<?php include('./config/config.php'); 
include("./lang/$language.inc.php");
include('./include/validation.php');
include('./include/menu.php'); 

jsBegin();
jsFormValidationBegin("frmpost");
jsValidateRequired("tax_description","Tax description");
jsValidateifNum("tax_percentage","Tax percentage");
jsFormValidationEnd();
jsEnd();

#do the product enabled/disblaed drop down
$display_block_enabled = "<select name=\"tax_enabled\">
<option value=\"1\" selected>$wording_for_enabledField</option>
<option value=\"0\">$wording_for_disabledField</option>
</select>";


?>

<BODY>
<?php
$mid->printMenu('hormenu1');
$mid->printFooter();
?>

<link rel="stylesheet" type="text/css" href="themes/<?php echo $theme; ?>/tables.css">
<br>

<FORM name="frmpost" ACTION="insert_action.php" METHOD=POST onsubmit="return frmpost_Validator(this)">
<div id="container">
<div id="header">

<table align=center>
	<tr>
		<td colspan=3 align=center><b>Tax rate to add</b></th>
	</tr>
</table>

</div id="header">
<div id="subheader">

<table align=center>
	<tr>
		<td>Tax description</td><td><input type=text name="tax_description" size=50></td><td></d>
	</tr>
	<tr>
		<td>Tax percentage</td><td><input type=text name="tax_percentage" size=25></td><td>* ie. 10 for 10%</td>
	</tr>
	<tr>
		<td><?php echo $wording_for_enabledField; ?></td><td><?php echo $display_block_enabled;?></td>
	</tr>
	
</table>
</div>

<div id="footer">
	<input type=submit name="submit" value="Insert Tax Rate">
	<input type=hidden name="op" value="insert_tax_rate">
</div>


</FORM>
</BODY>
</HTML>






