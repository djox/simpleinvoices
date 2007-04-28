<?php
include('./include/include_main.php');

//stop the direct browsing to this file - let index.php handle which files get displayed
checkLogin();

include('./include/sql_patches.php');




function check_sql_patch($check_sql_patch_ref, $check_sql_patch_field) {
        #product query
        include('./config/config.php');
        $conn = mysql_connect("$db_host","$db_user","$db_password");
        mysql_select_db("$db_name",$conn);


	#check sql patch 1
	$sql = "select * from {$tb_prefix}sql_patchmanager where sql_patch_ref = $check_sql_patch_ref" ;

	$result = mysql_query($sql, $conn) or die(mysql_error());
	$number_of_rows = mysql_num_rows($result);


	while ($Array = mysql_fetch_array($result)) {
        	$sql_idField = $Array['sql_id'];
	        $sql_patch_refField = $Array['sql_patch_ref'];
	        $sql_patchField = $Array['sql_patch'];
        	$sql_releaseField = $Array['sql_release'];
	}

	if (!empty($sql_idField))  {

	$display_block = "
		<tr><td>SQL patch $sql_patch_refField, $sql_patchField <i>has</i> already been applied in release $sql_releaseField</td></tr>
";
	}

	else if (empty($sql_idField))  {
		$display_block = "

		<tr><td>SQL patch $check_sql_patch_ref, $check_sql_patch_field  <b>has not</b> been applied to the database</td></tr>


	";
	}

	echo $display_block;
}




function run_sql_patch($sql_patch_ref, $sql_patch_name, $sql_patch, $sql_update) {

        include('./config/config.php');
        $conn = mysql_connect("$db_host","$db_user","$db_password");
        mysql_select_db("$db_name",$conn);


	#check sql patch 1
	$sql_run = "select * from {$tb_prefix}sql_patchmanager where sql_patch_ref = $sql_patch_ref" ;

	$result_run = mysql_query($sql_run, $conn) or die(mysql_error());
	$number_of_rows_run = mysql_num_rows($result_run);

        while ($Array_run = mysql_fetch_array($result_run)) {
                $sql_idField = $Array_run['sql_id'];
                $sql_patch_refField = $Array_run['sql_patch_ref'];
                $sql_patchField = $Array_run['sql_patch'];
                $sql_releaseField = $Array_run['sql_release'];
        }

	#forget about it!! the patch as its already been run
        if (!empty($sql_idField))  {
	
	$display_block = "
		</div id='header'>
		<tr><td>Skipping SQL patch $sql_patch_ref, $sql_patch_name as it <i>has</i> already been applied</td></tr>";
	};

	#patch hasnt been run before so run it - this is ganna be trouble :)
	if (empty($sql_idField))  {
		
		#so do the bloody patch
                mysql_query($sql_patch, $conn) or die(mysql_error());


                $display_block  = "

                <tr><td>SQL patch $sql_patch_ref, $sql_patch_name <i>has</i> been applied to the database</td></tr>
                ";
		# now update the {$tb_prefix}sql_patchmanager table
                mysql_query($sql_update, $conn) or die(mysql_error());


                $display_block = "

                <tr><td>SQL patch $sql_patch_ref, $sql_patch_name <b>has</b> been applied</td></tr>



	";
	};


	echo $display_block;
}




function initialise_sql_patch() {
        #product query
        include('./config/config.php');
        $conn = mysql_connect("$db_host","$db_user","$db_password");
        mysql_select_db("$db_name",$conn);



	#check sql patch 1
	$sql_patch_init = "CREATE TABLE {$tb_prefix}sql_patchmanager (sql_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,sql_patch_ref VARCHAR( 50 ) NOT NULL ,sql_patch VARCHAR( 50 ) NOT NULL ,sql_release VARCHAR( 25 ) NOT NULL ,sql_statement TEXT NOT NULL) TYPE = MYISAM ";
	mysql_query($sql_patch_init, $conn) or die(mysql_error());

	$display_block = "
		<tr>
		<td>Step 2 - The SQL patch table has been created<br></td></tr>";

	echo $display_block;

	$sql_insert = "INSERT INTO {$tb_prefix}sql_patchmanager
 ( sql_id  ,sql_patch_ref , sql_patch , sql_release , sql_statement )
VALUES ('','1','Create {$tb_prefix}sql_patchmanger table','20060514','$sql_patch_init')";
	mysql_query($sql_insert, $conn) or die(mysql_error());

	$display_block2 = "
		<tr><td>Step 3 - The SQL patch has been inserted into the SQL patch table<br></td></tr>";


	echo $display_block2;


}

#Max patches applied - start
$check_patches_sql = "
        SELECT
                count(sql_patch_ref) as count
        FROM 
                {$tb_prefix}sql_patchmanager
        ";

        $patches_result = mysql_query($check_patches_sql, $conn) or die(mysql_error());

        while ($Array_patches = mysql_fetch_array($patches_result)) {
                $max_patches_applied = $Array_patches['count'];
        };

	if ($max_patches_applied < $patch_count ) {
		$patches_to_be_applied = $patch_count - $max_patches_applied;
		$display_note = "<br>
			<b>Note:</b>You have $patches_to_be_applied patches to be applied
		";	
	}
#Top biller query - start





if ($_GET['op'] == "run_updates") {
	$table = "{$tb_prefix}sql_patchmanager";
#DEFINE SQL PATCH
	
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$table'"))==1) {



		echo "
		<table align='center'>
		";
/*
//MAKE THIS CODE WORK!!!	
                $r = 1;
		//get count of sql patches and run the check_sql_patch for each patch
                while  ($r <= $patch_count) {
                        run_sql_patch($r,"$sql_patch_name_$r","$sql_patch_$r","$sql_patch_update_$r");
                        $r++;
                }
*/

		run_sql_patch(1,$sql_patch_name_1,$sql_patch_1,$sql_patch_update_1);
		run_sql_patch(2,$sql_patch_name_2,$sql_patch_2,$sql_patch_update_2);
		run_sql_patch(3,$sql_patch_name_3,$sql_patch_3,$sql_patch_update_3);
		run_sql_patch(4,$sql_patch_name_4,$sql_patch_4,$sql_patch_update_4);
		run_sql_patch(5,$sql_patch_name_5,$sql_patch_5,$sql_patch_update_5);
		run_sql_patch(6,$sql_patch_name_6,$sql_patch_6,$sql_patch_update_6);
		run_sql_patch(7,$sql_patch_name_7,$sql_patch_7,$sql_patch_update_7);
		run_sql_patch(8,$sql_patch_name_8,$sql_patch_8,$sql_patch_update_8);
		run_sql_patch(9,$sql_patch_name_9,$sql_patch_9,$sql_patch_update_9);
		run_sql_patch(10,$sql_patch_name_10,$sql_patch_10,$sql_patch_update_10);
		run_sql_patch(11,$sql_patch_name_11,$sql_patch_11,$sql_patch_update_11);
		run_sql_patch(12,$sql_patch_name_12,$sql_patch_12,$sql_patch_update_12);
		run_sql_patch(13,$sql_patch_name_13,$sql_patch_13,$sql_patch_update_13);
		run_sql_patch(14,$sql_patch_name_14,$sql_patch_14,$sql_patch_update_14);
		run_sql_patch(15,$sql_patch_name_15,$sql_patch_15,$sql_patch_update_15);
		run_sql_patch(16,$sql_patch_name_16,$sql_patch_16,$sql_patch_update_16);
		run_sql_patch(17,$sql_patch_name_17,$sql_patch_17,$sql_patch_update_17);
		run_sql_patch(18,$sql_patch_name_18,$sql_patch_18,$sql_patch_update_18);
		run_sql_patch(19,$sql_patch_name_19,$sql_patch_19,$sql_patch_update_19);
		run_sql_patch(20,$sql_patch_name_20,$sql_patch_20,$sql_patch_update_20);
		run_sql_patch(21,$sql_patch_name_21,$sql_patch_21,$sql_patch_update_21);
		run_sql_patch(22,$sql_patch_name_22,$sql_patch_22,$sql_patch_update_22);
		run_sql_patch(23,$sql_patch_name_23,$sql_patch_23,$sql_patch_update_23);
		run_sql_patch(24,$sql_patch_name_24,$sql_patch_24,$sql_patch_update_24);
		run_sql_patch(25,$sql_patch_name_25,$sql_patch_25,$sql_patch_update_25);
		run_sql_patch(26,$sql_patch_name_26,$sql_patch_26,$sql_patch_update_26);
		run_sql_patch(27,$sql_patch_name_27,$sql_patch_27,$sql_patch_update_27);
		run_sql_patch(28,$sql_patch_name_28,$sql_patch_28,$sql_patch_update_28);
		run_sql_patch(29,$sql_patch_name_29,$sql_patch_29,$sql_patch_update_29);
		run_sql_patch(30,$sql_patch_name_30,$sql_patch_30,$sql_patch_update_30);
		run_sql_patch(31,$sql_patch_name_31,$sql_patch_31,$sql_patch_update_31);
		run_sql_patch(32,$sql_patch_name_32,$sql_patch_32,$sql_patch_update_32);
		run_sql_patch(33,$sql_patch_name_33,$sql_patch_33,$sql_patch_update_33);
		run_sql_patch(34,$sql_patch_name_34,$sql_patch_34,$sql_patch_update_34);
		run_sql_patch(35,$sql_patch_name_35,$sql_patch_35,$sql_patch_update_35);
		run_sql_patch(36,$sql_patch_name_36,$sql_patch_36,$sql_patch_update_36);
		run_sql_patch(37,$sql_patch_name_37,$sql_patch_37,$sql_patch_update_37);
		run_sql_patch(38,$sql_patch_name_38,$sql_patch_38,$sql_patch_update_38);
		run_sql_patch(39,$sql_patch_name_39,$sql_patch_39,$sql_patch_update_39);
		run_sql_patch(40,$sql_patch_name_40,$sql_patch_40,$sql_patch_update_40);
		run_sql_patch(41,$sql_patch_name_41,$sql_patch_41,$sql_patch_update_41);


		echo "<tr><td><br>The database patches have now been applied, please go back to the <a href='index.php?module=options&view=database_sqlpatches'>Database Upgrade Manager page</a> to see what patches have been applied. If all patches have been applied then there is now further action required</td></tr>";
		echo "
		</table>
";


	} else {


		echo "
		<table align='center'>
		";
	echo "<br><br><tr><td>Step 1 - This is the first time Database Updates has been run</td></tr><br>";
		initialise_sql_patch();
		
		echo "<tr><td><br>Now that the Database upgrade table has been initialised, please go back to the Database Upgrade Manger page by clicking <a href='index.php?module=options&view=database_sqlpatches'>HERE</a> to run the remaining patches</td></tr>";
		echo "
		</table>
		</div>
";

	}
}
	


else {
	#$tables = mysql_list_tables($dbname);
	$table = "{$tb_prefix}sql_patchmanager";

	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$table'"))==1) {


		echo <<<EOD
		<b>Database Upgrade Manager</b>
		$display_note
		<hr></hr>

		<table align="center">
			<tr></i><tr><td><br>The list below describes which patches have and have not been applied to the database, the aim is to have them all applied.  If there are patches that have not been applied to the Simple Invoices database, please run the Update database by clicking update </td></tr><tr align=center><td><p class='align_center'><br><a href='index.php?module=options&view=database_sqlpatches&op=run_updates'>UPDATE</a></p></td></tr></table><br>
<a href="./documentation/info_pages/text.html" rel="gb_page_center[450, 450]"><font color="red"><img src="./images/common/important.png"></img>Warning:</font></a>
<table align="center">
EOD;

		$p = 1;
                while  ($p <= $patch_count) {
			check_sql_patch($p,$sql_patch_name_.$p);
                        $p++;
                }

/*
	check_sql_patch(1,$sql_patch_name_1);
	check_sql_patch(2,$sql_patch_name_2);
	....
*/
		echo "</table>";

	}
	else {
		echo <<<EOD

		<table align='center'>
          <tr><td><br>This is the first time that the Database Upgrade process is to be run.  The first step in the process is to Initialse the database upgrade table. To do this click the Initialise database button<br><br><a href='index.php?module=options&view=database_sqlpatches&op=run_updates'>INITIALISE DATABASE UPGRADE</a></td></tr>
		</table>
EOD;
	}

}


include('./config/config.php'); ?>

<br><br>